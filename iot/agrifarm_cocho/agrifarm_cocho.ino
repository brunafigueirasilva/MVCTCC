// ======================================================
// AGRIFARM — CONTROLADOR DO COCHO (ESP32)
//
// Balança (célula de carga + HX711) mede a ração no cocho e o HC-SR04
// detecta animal na frente dele. A cada INTERVALO_ENVIO_MS a leitura vai
// para a API do AgriFarm:
//
//   POST {SERVIDOR}/api/iot/leitura
//   Cabeçalho: X-IoT-Key: {IOT_KEY}   (IOT_API_KEY do .env do Laravel)
//   {"cocho_id":1,"sensor_id":"BAL-001","tipo_sensor":"balanca",
//    "peso_kg":4.250,"presenca_animal":true,"distancia_presenca_cm":37.2}
//
// A API grava em leituras_iot e atualiza a quantidade do cocho
// (cochos.nivel_atual). Antes de ligar:
//   1) No sistema, edite o cocho e preencha "Balança / célula de carga"
//      com o mesmo código de SENSOR_CODIGO (ex.: BAL-001).
//   2) Rode o Laravel acessível na rede:  php artisan serve --host=0.0.0.0
//      e use em SERVIDOR o IP do computador (não "localhost": para o ESP32
//      localhost é ele mesmo).
//   3) Preencha WIFI_SSID, WIFI_SENHA, SERVIDOR, IOT_KEY e COCHO_ID abaixo.
//
// Calibração: feita uma vez e salva na memória do ESP32. Para refazer,
// abra o Monitor Serial (115200) e digite "c" nos primeiros 5 segundos.
//
// Bibliotecas: "HX711" (Bogdan Necula) — WiFi, HTTPClient e Preferences
// já vêm com o pacote de placas ESP32.
// ======================================================

#include <WiFi.h>
#include <HTTPClient.h>
#include <Preferences.h>
#include "HX711.h"

// ======================================================
// CONFIGURAÇÃO DA REDE E DA API
// ======================================================

const char* WIFI_SSID  = "NOME_DA_REDE";
const char* WIFI_SENHA = "SENHA_DA_REDE";

// IP do computador que roda o Laravel + porta (sem barra no fim)
const char* SERVIDOR = "http://192.168.0.10:8000";

// Mesmo valor de IOT_API_KEY no .env do Laravel
const char* IOT_KEY = "COLE_AQUI_A_IOT_API_KEY";

// Cocho onde o controlador está instalado e código da balança cadastrado nele
const int   COCHO_ID      = 1;
const char* SENSOR_CODIGO = "BAL-001";

// Envia uma leitura a cada 10 s (a API aceita até 240 por minuto)
const unsigned long INTERVALO_ENVIO_MS = 10000;

// ======================================================
// PINOS
// ======================================================

// HC-SR04
const int TRIG_PIN = 18;
const int ECHO_PIN = 19;

// HX711
const int HX711_DOUT = 4;
const int HX711_SCK  = 5;

// LEDs
const int LED_VERMELHO = 25;
const int LED_VERDE = 26;

// ======================================================
// CONFIGURAÇÕES DA BALANÇA
// ======================================================

// Capacidade da célula de carga
const float PESO_MAXIMO = 20000.0; // 20 kg

// Peso abaixo disso será considerado zero
const float LIMITE_ZERO = 10.0;

// Limites dos LEDs
const float LIMITE_LED_VERDE = 100.0;       // 100 g
const float LIMITE_LED_VERMELHO = 10000.0;  // 10 kg

// Animal detectado quando o HC-SR04 mede até esta distância
const float DISTANCIA_PRESENCA_CM = 200.0;

// ======================================================
// ESTADO
// ======================================================

HX711 scale;
Preferences memoria;

float fatorCalibracao = 1.0;
long offset = 0;

unsigned long ultimoEnvio = 0;
bool presencaAnterior = false;

// ======================================================
// MEDIR DISTÂNCIA
// ======================================================

float medirDistancia() {
  digitalWrite(TRIG_PIN, LOW);
  delayMicroseconds(2);

  digitalWrite(TRIG_PIN, HIGH);
  delayMicroseconds(10);

  digitalWrite(TRIG_PIN, LOW);

  long duration = pulseIn(ECHO_PIN, HIGH, 30000);

  if (duration == 0) {
    return -1;
  }

  return duration * 0.0343 / 2.0;
}

// ======================================================
// ESPERAR PESO DIGITADO PELO USUÁRIO
// ======================================================

float esperarPesoConhecido() {
  Serial.println();
  Serial.println("==========================================");
  Serial.println("        INFORME O PESO DE REFERENCIA");
  Serial.println("==========================================");
  Serial.println();
  Serial.println("Digite o peso REAL do objeto em gramas.");
  Serial.println("Exemplos: 152, 237, 703, 1000");
  Serial.println();
  Serial.print("Peso em gramas: ");

  while (Serial.available() == 0) {
    delay(100);
  }

  float peso = Serial.parseFloat();

  while (Serial.available() > 0) {
    Serial.read();
  }

  return peso;
}

void travarComErro(const char* mensagem) {
  Serial.println();
  Serial.println(mensagem);
  Serial.println("Reinicie o sistema e tente novamente.");

  while (true) {
    digitalWrite(LED_VERMELHO, !digitalRead(LED_VERMELHO));
    delay(500);
  }
}

// ======================================================
// CALIBRAÇÃO (salva na memória do ESP32)
// ======================================================

void calibrarBalanca() {
  Serial.println();
  Serial.println("==========================================");
  Serial.println("          CALIBRACAO DA BALANCA");
  Serial.println("==========================================");

  // ---------------- TARA ----------------
  Serial.println();
  Serial.println("1 - RETIRE TODO O PESO.");
  Serial.println("Deixe a celula completamente vazia.");
  Serial.println("Aguardando 5 segundos...");
  delay(5000);

  scale.set_scale(1.0);
  scale.tare(80);
  offset = scale.get_offset();

  Serial.println();
  Serial.print("TARA CONCLUIDA! Offset: ");
  Serial.println(offset);

  // ---------------- PESO CONHECIDO ----------------
  Serial.println();
  Serial.println("==========================================");
  Serial.println("2 - COLOQUE UM PESO CONHECIDO");
  Serial.println("==========================================");
  Serial.println("Pode ser QUALQUER peso (ex.: 152 g, 703 g, 1000 g).");
  Serial.println("Aguarde 5 segundos depois de colocar o peso.");
  delay(5000);

  long leituraComPeso = scale.read_average(40);
  long diferenca = leituraComPeso - offset;

  Serial.println();
  Serial.print("RAW: ");
  Serial.print(leituraComPeso);
  Serial.print("  Diferenca RAW: ");
  Serial.println(diferenca);

  float pesoConhecido = esperarPesoConhecido();

  if (pesoConhecido <= 0) {
    travarComErro("ERRO: peso invalido.");
  }

  fatorCalibracao = (float)diferenca / pesoConhecido;

  if (fatorCalibracao == 0) {
    travarComErro("ERRO: fator de calibracao igual a zero.");
  }

  scale.set_scale(fatorCalibracao);

  // Guarda para os próximos boots (sem precisar do Monitor Serial)
  memoria.putFloat("fator", fatorCalibracao);
  memoria.putLong("offset", offset);

  Serial.println();
  Serial.println("==========================================");
  Serial.println("       CALIBRACAO CONCLUIDA E SALVA");
  Serial.println("==========================================");
  Serial.print("Peso utilizado: ");
  Serial.print(pesoConhecido);
  Serial.println(" g");
  Serial.print("Fator calculado: ");
  Serial.println(fatorCalibracao, 6);

  // ---------------- CONFERIR ZERO ----------------
  Serial.println();
  Serial.println("3 - RETIRE O PESO (5 segundos)");
  delay(5000);

  float pesoZero = scale.get_units(30);
  if ((pesoZero > -LIMITE_ZERO && pesoZero < LIMITE_ZERO) || pesoZero < 0) {
    pesoZero = 0;
  }

  Serial.print("Peso apos retirar referencia: ");
  Serial.print(pesoZero, 1);
  Serial.println(" g");
}

// Usa a calibração salva; sem ela (ou se o usuário digitar "c"), calibra.
void prepararBalanca() {
  memoria.begin("agrifarm", false);

  bool temCalibracao = memoria.isKey("fator") && memoria.isKey("offset");

  Serial.println();
  Serial.println("Digite \"c\" em 5 s para (re)calibrar a balanca...");
  unsigned long inicio = millis();
  bool pediuCalibracao = false;

  while (millis() - inicio < 5000) {
    if (Serial.available() > 0) {
      char c = Serial.read();
      if (c == 'c' || c == 'C') {
        pediuCalibracao = true;
        break;
      }
    }
    delay(50);
  }

  while (Serial.available() > 0) {
    Serial.read();
  }

  if (temCalibracao && !pediuCalibracao) {
    fatorCalibracao = memoria.getFloat("fator", 1.0);
    offset = memoria.getLong("offset", 0);
    scale.set_offset(offset);
    scale.set_scale(fatorCalibracao);

    Serial.print("Calibracao salva carregada. Fator: ");
    Serial.print(fatorCalibracao, 6);
    Serial.print("  Offset: ");
    Serial.println(offset);
  } else {
    calibrarBalanca();
  }
}

// ======================================================
// WI-FI
// ======================================================

void conectarWiFi() {
  if (WiFi.status() == WL_CONNECTED) {
    return;
  }

  Serial.print("Conectando ao Wi-Fi ");
  Serial.print(WIFI_SSID);

  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_SENHA);

  unsigned long inicio = millis();
  while (WiFi.status() != WL_CONNECTED && millis() - inicio < 15000) {
    delay(500);
    Serial.print(".");
  }

  Serial.println();

  if (WiFi.status() == WL_CONNECTED) {
    Serial.print("Wi-Fi conectado. IP: ");
    Serial.println(WiFi.localIP());
  } else {
    Serial.println("Sem Wi-Fi agora; tento de novo no proximo envio.");
  }
}

// ======================================================
// ENVIO PARA A API
// ======================================================

void enviarLeitura(float pesoGramas, bool presenca, float distancia) {
  conectarWiFi();

  if (WiFi.status() != WL_CONNECTED) {
    return;
  }

  char corpo[256];

  if (distancia > 0) {
    snprintf(corpo, sizeof(corpo),
      "{\"cocho_id\":%d,\"sensor_id\":\"%s\",\"tipo_sensor\":\"balanca\","
      "\"peso_kg\":%.3f,\"presenca_animal\":%s,\"distancia_presenca_cm\":%.1f}",
      COCHO_ID, SENSOR_CODIGO, pesoGramas / 1000.0, presenca ? "true" : "false", distancia);
  } else {
    // Sem eco do HC-SR04: manda só o peso e "sem presença".
    snprintf(corpo, sizeof(corpo),
      "{\"cocho_id\":%d,\"sensor_id\":\"%s\",\"tipo_sensor\":\"balanca\","
      "\"peso_kg\":%.3f,\"presenca_animal\":false}",
      COCHO_ID, SENSOR_CODIGO, pesoGramas / 1000.0);
  }

  HTTPClient http;
  String url = String(SERVIDOR) + "/api/iot/leitura";

  http.begin(url);
  http.setTimeout(5000);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Accept", "application/json");
  http.addHeader("X-IoT-Key", IOT_KEY);

  int status = http.POST(corpo);

  Serial.print("API -> ");
  Serial.print(status);

  if (status == 201 || status == 200) {
    Serial.println(" leitura registrada no AgriFarm");
  } else if (status == 401) {
    Serial.println(" chave IoT recusada (confira IOT_KEY / IOT_API_KEY)");
  } else if (status == 422) {
    Serial.print(" dados recusados: ");
    Serial.println(http.getString());
  } else if (status == 503) {
    Serial.println(" IOT_API_KEY nao configurada no servidor");
  } else if (status < 0) {
    Serial.print(" falha de conexao: ");
    Serial.println(http.errorToString(status));
  } else {
    Serial.print(" resposta inesperada: ");
    Serial.println(http.getString());
  }

  http.end();
}

// ======================================================
// CONTROLE DOS LEDs
// ======================================================

void controlarLEDs(float pesoGramas) {
  if (pesoGramas > LIMITE_LED_VERMELHO) {
    // ACIMA DE 10 KG: LED VERMELHO
    digitalWrite(LED_VERMELHO, HIGH);
    digitalWrite(LED_VERDE, LOW);
  } else if (pesoGramas > LIMITE_LED_VERDE) {
    // ACIMA DE 100 g: LED VERDE
    digitalWrite(LED_VERMELHO, LOW);
    digitalWrite(LED_VERDE, HIGH);
  } else {
    // ATÉ 100 g: LEDs APAGADOS
    digitalWrite(LED_VERMELHO, LOW);
    digitalWrite(LED_VERDE, LOW);
  }
}

// ======================================================
// SETUP
// ======================================================

void setup() {
  Serial.begin(115200);

  pinMode(TRIG_PIN, OUTPUT);
  pinMode(ECHO_PIN, INPUT);
  digitalWrite(TRIG_PIN, LOW);

  pinMode(LED_VERMELHO, OUTPUT);
  pinMode(LED_VERDE, OUTPUT);
  digitalWrite(LED_VERMELHO, LOW);
  digitalWrite(LED_VERDE, LOW);

  scale.begin(HX711_DOUT, HX711_SCK);

  Serial.println();
  Serial.println("==========================================");
  Serial.println("   AGRIFARM - CONTROLADOR DO COCHO");
  Serial.println("==========================================");

  delay(2000);

  if (!scale.is_ready()) {
    travarComErro("ERRO: HX711 nao encontrado!");
  }

  Serial.println("HX711 encontrado!");

  prepararBalanca();
  conectarWiFi();

  Serial.println();
  Serial.println("==========================================");
  Serial.println("          SISTEMA PRONTO!");
  Serial.println("==========================================");
}

// ======================================================
// LOOP
// ======================================================

void loop() {
  // ---------------- DISTÂNCIA / PRESENÇA ----------------
  float distancia = medirDistancia();
  bool presenca = distancia > 0 && distancia <= DISTANCIA_PRESENCA_CM;

  // ---------------- PESO ----------------
  float pesoGramas = 0;

  if (scale.is_ready()) {
    pesoGramas = scale.get_units(20);

    // Zona de zero e sem negativo
    if ((pesoGramas > -LIMITE_ZERO && pesoGramas < LIMITE_ZERO) || pesoGramas < 0) {
      pesoGramas = 0;
    }

    // Limite de 20 kg da célula
    if (pesoGramas > PESO_MAXIMO) {
      pesoGramas = PESO_MAXIMO;
    }
  }

  controlarLEDs(pesoGramas);

  // ---------------- SERIAL ----------------
  Serial.println();
  Serial.println("------------------------------------------");

  if (presenca) {
    Serial.print("Presenca: DETECTADA (");
    Serial.print(distancia, 2);
    Serial.println(" cm)");
  } else {
    Serial.println("Presenca: NENHUMA");
  }

  Serial.print("Peso: ");
  Serial.print(pesoGramas, 1);
  Serial.print(" g  (");
  Serial.print(pesoGramas / 1000.0, 3);
  Serial.println(" kg)");

  Serial.println(pesoGramas <= LIMITE_ZERO ? "Status: COCHO VAZIO" : "Status: RACAO DETECTADA");

  if (pesoGramas > LIMITE_LED_VERMELHO) {
    Serial.println("LED: VERMELHO - ACIMA DE 10 KG");
  } else if (pesoGramas > LIMITE_LED_VERDE) {
    Serial.println("LED: VERDE - ACIMA DE 100 g");
  } else {
    Serial.println("LEDs: APAGADOS");
  }

  if (pesoGramas >= PESO_MAXIMO) {
    Serial.println("[ALERTA] LIMITE DE 20 KG ATINGIDO!");
  }

  // ---------------- ENVIO PARA A API ----------------
  // No intervalo normal, ou na hora em que um animal chega/sai do cocho.
  unsigned long agora = millis();
  bool mudouPresenca = presenca != presencaAnterior;

  if (ultimoEnvio == 0 || agora - ultimoEnvio >= INTERVALO_ENVIO_MS || (mudouPresenca && agora - ultimoEnvio >= 1500)) {
    enviarLeitura(pesoGramas, presenca, distancia);
    ultimoEnvio = agora;
    presencaAnterior = presenca;
  }

  delay(500);
}
