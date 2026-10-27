<?php

/*
|--------------------------------------------------------------------------
| Integração IoT dos cochos (sensor de distância + célula de carga)
|--------------------------------------------------------------------------
| O controlador (ESP32) envia as leituras para POST /api/iot/leitura com o
| cabeçalho "X-IoT-Key: <chave>". Sem chave configurada o endpoint recusa
| tudo (503) — nunca fica aberto por engano.
*/

return [

    // Chave compartilhada com os dispositivos (cabeçalho X-IoT-Key).
    'chave' => env('IOT_API_KEY'),

    // Sem leitura há mais que isto, o sensor passa a "offline".
    'minutos_offline' => (int) env('IOT_MINUTOS_OFFLINE', 15),

    // Quanto o peso lido pode passar da capacidade do cocho antes de ser
    // recusado como incompatível (ruído/calibração da célula de carga).
    // Dentro da tolerância, a quantidade é limitada à capacidade.
    'tolerancia_peso_percentual' => (float) env('IOT_TOLERANCIA_PESO', 10),

    // Faixa aceita para o sensor de distância (cm).
    'distancia_maxima_cm' => (float) env('IOT_DISTANCIA_MAXIMA_CM', 500),

    // Leituras com data_hora mais antiga que isto são recusadas.
    'dias_maximos_leitura_atrasada' => (int) env('IOT_DIAS_LEITURA_ATRASADA', 30),

    // Variações menores que isto (kg) entre duas leituras são ruído da
    // balança — não contam como consumo nem reposição.
    'ruido_kg' => (float) env('IOT_RUIDO_KG', 0.2),
];
