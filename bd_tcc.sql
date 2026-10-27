-- ==========================================================================
-- AgriFarm — Gerenciamento Inteligente de Ração em Fazendas
-- Estrutura FINAL do banco de dados (MySQL/MariaDB, utf8mb4)
--
-- Gerado com mysqldump a partir de um banco montado SOMENTE pelas
-- migrations do Laravel (php artisan migrate:fresh) — é idêntico, coluna a
-- coluna e chave a chave, ao que as migrations criam. Não contém dados de
-- usuários; só as linhas da tabela "migrations", para que um
-- "php artisan migrate" depois da importação saiba que tudo já foi aplicado.
--
-- Duas formas equivalentes de instalar em outro computador:
--   A) php artisan migrate --seed      (recomendado; --seed = dados de teste)
--   B) importar este arquivo num banco VAZIO e depois rodar
--      php artisan migrate (não faz nada) e, se quiser, php artisan db:seed
--
-- Relacionamentos principais:
--   cadastros 1─N fazendas 1─N lotes 1─N cochos
--   cochos 1─N sensores 1─N leituras_iot (IoT: balança/distância + presença
--            de animal enviadas pelo ESP32 em POST /api/iot/leitura),
--            cocho_consumos (consumo registrado nos cochos, sem sensor)
--   fazendas 1─N racaos 1─N estoque_movimentacoes (histórico de estoque)
--   fazendas 1─N planos (plano alimentar: lote + ração)
--   lotes 1─N lote_ocorrencias (óbito, doença, parto...), ordenhas,
--            lote_consumo_snapshots, desempenho_lotes
-- O manejo é por LOTE: não existe cadastro individual de animais.
-- "users" é a tabela padrão do Laravel e não é usada (contas = cadastros).
-- ==========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acessibilidade_preferencias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `tema` varchar(20) NOT NULL DEFAULT 'light',
  `tamanho_fonte` tinyint(3) unsigned NOT NULL DEFAULT 100,
  `zoom` decimal(3,2) NOT NULL DEFAULT 1.00,
  `voz_nome` varchar(255) DEFAULT NULL,
  `voz_velocidade` decimal(3,2) NOT NULL DEFAULT 1.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `acessibilidade_preferencias_usuario_id_unique` (`usuario_id`),
  CONSTRAINT `acessibilidade_preferencias_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `cadastros` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cadastros` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `responsavel` varchar(100) NOT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `email_alterado_em` timestamp NULL DEFAULT NULL,
  `email_verificado_em` timestamp NULL DEFAULT NULL,
  `telefone` varchar(20) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `senha_alterada_em` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cadastros_email_unique` (`email`),
  UNIQUE KEY `cadastros_cpf_unique` (`cpf`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cocho_consumos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fazenda_id` bigint(20) unsigned NOT NULL,
  `lote_id` bigint(20) unsigned DEFAULT NULL,
  `cocho_id` bigint(20) unsigned DEFAULT NULL,
  `racao_id` bigint(20) unsigned DEFAULT NULL,
  `cadastro_id` bigint(20) unsigned DEFAULT NULL,
  `origem` varchar(30) NOT NULL DEFAULT 'manual',
  `quantidade` decimal(10,2) NOT NULL,
  `nivel_anterior` decimal(10,2) NOT NULL,
  `nivel_posterior` decimal(10,2) NOT NULL,
  `valor_unitario` decimal(10,2) DEFAULT NULL,
  `observacao` varchar(255) DEFAULT NULL,
  `data_consumo` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cocho_consumos_racao_id_foreign` (`racao_id`),
  KEY `cocho_consumos_cadastro_id_foreign` (`cadastro_id`),
  KEY `cocho_consumos_lote_id_data_consumo_index` (`lote_id`,`data_consumo`),
  KEY `cocho_consumos_fazenda_id_data_consumo_index` (`fazenda_id`,`data_consumo`),
  KEY `cocho_consumos_cocho_id_data_consumo_index` (`cocho_id`,`data_consumo`),
  CONSTRAINT `cocho_consumos_cadastro_id_foreign` FOREIGN KEY (`cadastro_id`) REFERENCES `cadastros` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cocho_consumos_cocho_id_foreign` FOREIGN KEY (`cocho_id`) REFERENCES `cochos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cocho_consumos_fazenda_id_foreign` FOREIGN KEY (`fazenda_id`) REFERENCES `fazendas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cocho_consumos_lote_id_foreign` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cocho_consumos_racao_id_foreign` FOREIGN KEY (`racao_id`) REFERENCES `racaos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cocho_consumos_quantidade_positiva` CHECK (`quantidade` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cochos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lote_id` bigint(20) unsigned NOT NULL,
  `numero` int(10) unsigned DEFAULT NULL,
  `nome_cocho` varchar(255) NOT NULL,
  `localizacao` varchar(150) DEFAULT NULL,
  `capacidade` decimal(10,2) NOT NULL DEFAULT 100.00,
  `nivel_atual` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sensor` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cochos_lote_id_foreign` (`lote_id`),
  CONSTRAINT `cochos_lote_id_foreign` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cochos_capacidade_positiva` CHECK (`capacidade` > 0),
  CONSTRAINT `cochos_nivel_na_capacidade` CHECK (`nivel_atual` >= 0 and `nivel_atual` <= `capacidade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `codigos_verificacao` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `codigo_hash` varchar(255) NOT NULL,
  `proposito` varchar(255) NOT NULL,
  `payload` text DEFAULT NULL,
  `expira_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `usado_em` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `codigos_verificacao_email_proposito_index` (`email`,`proposito`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comentarios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cadastro_id` bigint(20) unsigned DEFAULT NULL,
  `nome` varchar(150) DEFAULT NULL,
  `mensagem` text NOT NULL,
  `nota` tinyint(3) unsigned DEFAULT NULL,
  `nota_facilidade` tinyint(3) unsigned DEFAULT NULL,
  `nota_visual` tinyint(3) unsigned DEFAULT NULL,
  `nota_desempenho` tinyint(3) unsigned DEFAULT NULL,
  `nota_organizacao` tinyint(3) unsigned DEFAULT NULL,
  `nota_funcionalidades` tinyint(3) unsigned DEFAULT NULL,
  `nota_acessibilidade` tinyint(3) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comentarios_cadastro_id_foreign` (`cadastro_id`),
  CONSTRAINT `comentarios_cadastro_id_foreign` FOREIGN KEY (`cadastro_id`) REFERENCES `cadastros` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `desempenho_lotes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lote_id` bigint(20) unsigned NOT NULL,
  `peso_medio_kg` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantidade_animais` int(10) unsigned NOT NULL DEFAULT 0,
  `data_registro` date NOT NULL,
  `consumo_kg` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fonte_consumo` varchar(20) DEFAULT NULL,
  `custo_rs` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ganho_peso_kg` decimal(10,2) NOT NULL DEFAULT 0.00,
  `desperdicio_porcentagem` decimal(5,2) NOT NULL DEFAULT 0.00,
  `eficiencia_alimentar` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `desempenho_lotes_lote_id_data_registro_unique` (`lote_id`,`data_registro`),
  CONSTRAINT `desempenho_lotes_lote_id_foreign` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estoque_movimentacoes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fazenda_id` bigint(20) unsigned NOT NULL,
  `racao_id` bigint(20) unsigned NOT NULL,
  `cadastro_id` bigint(20) unsigned DEFAULT NULL,
  `lote_id` bigint(20) unsigned DEFAULT NULL,
  `cocho_id` bigint(20) unsigned DEFAULT NULL,
  `tipo` varchar(10) NOT NULL,
  `origem` varchar(30) NOT NULL DEFAULT 'manual',
  `quantidade` decimal(10,2) NOT NULL,
  `saldo_anterior` decimal(10,2) NOT NULL,
  `saldo_posterior` decimal(10,2) NOT NULL,
  `valor_unitario` decimal(10,2) DEFAULT NULL,
  `observacao` varchar(255) DEFAULT NULL,
  `data_movimentacao` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estoque_movimentacoes_cadastro_id_foreign` (`cadastro_id`),
  KEY `estoque_movimentacoes_lote_id_foreign` (`lote_id`),
  KEY `estoque_movimentacoes_cocho_id_foreign` (`cocho_id`),
  KEY `estoque_movimentacoes_fazenda_id_data_movimentacao_index` (`fazenda_id`,`data_movimentacao`),
  KEY `estoque_movimentacoes_racao_id_data_movimentacao_index` (`racao_id`,`data_movimentacao`),
  CONSTRAINT `estoque_movimentacoes_cadastro_id_foreign` FOREIGN KEY (`cadastro_id`) REFERENCES `cadastros` (`id`) ON DELETE SET NULL,
  CONSTRAINT `estoque_movimentacoes_cocho_id_foreign` FOREIGN KEY (`cocho_id`) REFERENCES `cochos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `estoque_movimentacoes_fazenda_id_foreign` FOREIGN KEY (`fazenda_id`) REFERENCES `fazendas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `estoque_movimentacoes_lote_id_foreign` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `estoque_movimentacoes_racao_id_foreign` FOREIGN KEY (`racao_id`) REFERENCES `racaos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `estoque_mov_quantidade_positiva` CHECK (`quantidade` > 0),
  CONSTRAINT `estoque_mov_saldo_nao_negativo` CHECK (`saldo_posterior` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fazendas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cadastro_id` bigint(20) unsigned NOT NULL,
  `nome_fazenda` varchar(100) NOT NULL,
  `proprietario` varchar(255) DEFAULT NULL,
  `localizacao` varchar(255) DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `cidade` varchar(255) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `area` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fazendas_cadastro_id_foreign` (`cadastro_id`),
  CONSTRAINT `fazendas_cadastro_id_foreign` FOREIGN KEY (`cadastro_id`) REFERENCES `cadastros` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leituras_iot` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cocho_id` bigint(20) unsigned NOT NULL,
  `sensor_id` bigint(20) unsigned NOT NULL,
  `tipo_sensor` varchar(20) NOT NULL,
  `distancia_cm` decimal(8,2) DEFAULT NULL,
  `peso_kg` decimal(10,2) DEFAULT NULL,
  `presenca_animal` tinyint(1) DEFAULT NULL,
  `distancia_presenca_cm` decimal(8,2) DEFAULT NULL,
  `nivel_percentual` decimal(5,2) DEFAULT NULL,
  `quantidade_kg` decimal(10,2) DEFAULT NULL,
  `variacao_kg` decimal(10,2) DEFAULT NULL,
  `data_hora` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leituras_iot_sensor_id_data_hora_unique` (`sensor_id`,`data_hora`),
  KEY `leituras_iot_cocho_id_data_hora_index` (`cocho_id`,`data_hora`),
  CONSTRAINT `leituras_iot_cocho_id_foreign` FOREIGN KEY (`cocho_id`) REFERENCES `cochos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leituras_iot_sensor_id_foreign` FOREIGN KEY (`sensor_id`) REFERENCES `sensores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lote_consumo_snapshots` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lote_id` bigint(20) unsigned NOT NULL,
  `racao_id` bigint(20) unsigned DEFAULT NULL,
  `data` date NOT NULL,
  `quantidade_animais` int(10) unsigned NOT NULL DEFAULT 0,
  `consumo_kg` int(10) unsigned NOT NULL DEFAULT 0,
  `status_class` varchar(30) NOT NULL DEFAULT 'status-warning',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lote_consumo_snapshots_lote_id_data_unique` (`lote_id`,`data`),
  KEY `lote_consumo_snapshots_racao_id_foreign` (`racao_id`),
  CONSTRAINT `lote_consumo_snapshots_lote_id_foreign` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lote_consumo_snapshots_racao_id_foreign` FOREIGN KEY (`racao_id`) REFERENCES `racaos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lote_ocorrencias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lote_id` bigint(20) unsigned NOT NULL,
  `tipo` varchar(255) NOT NULL DEFAULT 'morte',
  `causa` varchar(255) DEFAULT NULL,
  `doenca` varchar(255) DEFAULT NULL,
  `quantidade` int(10) unsigned NOT NULL DEFAULT 1,
  `quantidade_antes` int(10) unsigned NOT NULL,
  `quantidade_depois` int(10) unsigned NOT NULL,
  `lote_destino_id` bigint(20) unsigned DEFAULT NULL,
  `data_ocorrencia` date NOT NULL,
  `observacao` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lote_ocorrencias_lote_id_foreign` (`lote_id`),
  KEY `lote_ocorrencias_lote_destino_id_foreign` (`lote_destino_id`),
  CONSTRAINT `lote_ocorrencias_lote_destino_id_foreign` FOREIGN KEY (`lote_destino_id`) REFERENCES `lotes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lote_ocorrencias_lote_id_foreign` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lotes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fazenda_id` bigint(20) unsigned NOT NULL,
  `racao_id` bigint(20) unsigned DEFAULT NULL,
  `lote_origem_id` bigint(20) unsigned DEFAULT NULL,
  `nome_lote` varchar(100) NOT NULL,
  `tipo_animal` varchar(50) NOT NULL,
  `raca` varchar(100) DEFAULT NULL,
  `finalidade` varchar(255) DEFAULT NULL,
  `sexo` varchar(255) DEFAULT NULL,
  `idade_media` varchar(255) DEFAULT NULL,
  `quantidade_animais` int(11) NOT NULL DEFAULT 0,
  `quantidade_cochos` int(10) unsigned NOT NULL DEFAULT 0,
  `peso_medio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `data_entrada` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Ativo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lotes_lote_origem_id_foreign` (`lote_origem_id`),
  KEY `lotes_racao_id_foreign` (`racao_id`),
  KEY `lotes_fazenda_status_index` (`fazenda_id`,`status`),
  CONSTRAINT `lotes_fazenda_id_foreign` FOREIGN KEY (`fazenda_id`) REFERENCES `fazendas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lotes_lote_origem_id_foreign` FOREIGN KEY (`lote_origem_id`) REFERENCES `lotes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lotes_racao_id_foreign` FOREIGN KEY (`racao_id`) REFERENCES `racaos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lotes_animais_nao_negativo` CHECK (`quantidade_animais` >= 0),
  CONSTRAINT `lotes_peso_nao_negativo` CHECK (`peso_medio` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ordenhas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lote_id` bigint(20) unsigned NOT NULL,
  `data` date NOT NULL,
  `horario` time NOT NULL,
  `turno` varchar(10) NOT NULL,
  `quantidade_litros` decimal(8,2) NOT NULL,
  `responsavel_id` bigint(20) unsigned DEFAULT NULL,
  `observacoes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ordenhas_responsavel_id_foreign` (`responsavel_id`),
  KEY `ordenhas_lote_id_data_index` (`lote_id`,`data`),
  CONSTRAINT `ordenhas_lote_id_foreign` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ordenhas_responsavel_id_foreign` FOREIGN KEY (`responsavel_id`) REFERENCES `cadastros` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ordenhas_turno_valido` CHECK (`turno` in ('Manhã','Tarde','Noite')),
  CONSTRAINT `ordenhas_turno_horario` CHECK (`turno` = 'Manhã' and `horario` >= '04:00:00' and `horario` < '12:00:00' or `turno` = 'Tarde' and `horario` >= '12:00:00' and `horario` < '18:00:00' or `turno` = 'Noite' and (`horario` >= '18:00:00' or `horario` < '04:00:00')),
  CONSTRAINT `ordenhas_litros_positivos` CHECK (`quantidade_litros` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `planos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fazenda_id` bigint(20) unsigned NOT NULL,
  `lote_id` bigint(20) unsigned NOT NULL,
  `racao_id` bigint(20) unsigned NOT NULL,
  `quantidade_recomendada` decimal(10,2) NOT NULL,
  `frequencia_diaria` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `data_inicio` date NOT NULL,
  `data_fim` date DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `planos_racao_id_foreign` (`racao_id`),
  KEY `planos_fazenda_id_ativo_index` (`fazenda_id`,`ativo`),
  KEY `planos_lote_id_ativo_index` (`lote_id`,`ativo`),
  CONSTRAINT `planos_fazenda_id_foreign` FOREIGN KEY (`fazenda_id`) REFERENCES `fazendas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `planos_lote_id_foreign` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `planos_racao_id_foreign` FOREIGN KEY (`racao_id`) REFERENCES `racaos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `racao_estoque_snapshots` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `racao_id` bigint(20) unsigned NOT NULL,
  `data` date NOT NULL,
  `estoque_kg` decimal(10,2) NOT NULL DEFAULT 0.00,
  `percentual_estoque` tinyint(3) unsigned DEFAULT NULL,
  `status_class` varchar(20) NOT NULL DEFAULT 'ok',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `racao_estoque_snapshots_racao_id_data_unique` (`racao_id`,`data`),
  CONSTRAINT `racao_estoque_snapshots_racao_id_foreign` FOREIGN KEY (`racao_id`) REFERENCES `racaos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `racaos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fazenda_id` bigint(20) unsigned DEFAULT NULL,
  `tipo` varchar(225) DEFAULT NULL,
  `sexo` varchar(255) DEFAULT NULL,
  `idade` varchar(255) DEFAULT NULL,
  `finalidade` varchar(255) DEFAULT NULL,
  `nutrientes` text DEFAULT NULL,
  `unidade` varchar(10) NOT NULL DEFAULT 'kg',
  `nome_racao` varchar(100) NOT NULL,
  `estoque` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estoque_total` decimal(10,2) DEFAULT NULL,
  `consumo` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_kg` decimal(10,2) DEFAULT NULL,
  `fornecedor` varchar(150) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `proteina_percentual` decimal(5,2) DEFAULT NULL,
  `energia_mcal_kg` decimal(6,2) DEFAULT NULL,
  `fibra_percentual` decimal(5,2) DEFAULT NULL,
  `minerais` varchar(255) DEFAULT NULL,
  `estoque_minimo` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ativa',
  `observacoes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `racaos_fazenda_id_foreign` (`fazenda_id`),
  CONSTRAINT `racaos_fazenda_id_foreign` FOREIGN KEY (`fazenda_id`) REFERENCES `fazendas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `racaos_estoque_nao_negativo` CHECK (`estoque` >= 0),
  CONSTRAINT `racaos_valor_kg_nao_negativo` CHECK (`valor_kg` is null or `valor_kg` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sensores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cocho_id` bigint(20) unsigned NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `distancia_vazio_cm` decimal(8,2) DEFAULT NULL,
  `distancia_cheio_cm` decimal(8,2) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `ultima_leitura_em` datetime DEFAULT NULL,
  `ultimo_valor` decimal(10,2) DEFAULT NULL,
  `ultimo_erro` varchar(255) DEFAULT NULL,
  `ultimo_erro_em` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sensores_cocho_id_tipo_unique` (`cocho_id`,`tipo`),
  UNIQUE KEY `sensores_codigo_unique` (`codigo`),
  CONSTRAINT `sensores_cocho_id_foreign` FOREIGN KEY (`cocho_id`) REFERENCES `cochos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Migrations já aplicadas por esta estrutura
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` VALUES (4,'2026_08_01_000000_create_tabelas_base_agrifarm',1);
INSERT INTO `migrations` VALUES (5,'2026_08_04_180125_add_cpf_and_foto_to_cadastros_table',1);
INSERT INTO `migrations` VALUES (6,'2026_08_04_181346_widen_telefone_on_cadastros_table',1);
INSERT INTO `migrations` VALUES (7,'2026_08_04_183250_add_data_nascimento_and_seguranca_to_cadastros_table',1);
INSERT INTO `migrations` VALUES (8,'2026_08_10_142801_add_endereco_to_fazendas_table',1);
INSERT INTO `migrations` VALUES (9,'2026_08_10_152846_add_coordenadas_e_proprietario_to_fazendas_table',1);
INSERT INTO `migrations` VALUES (10,'2026_08_10_161818_add_finalidade_sexo_idade_media_to_lotes_table',1);
INSERT INTO `migrations` VALUES (11,'2026_08_10_181631_add_status_to_lotes_table',1);
INSERT INTO `migrations` VALUES (12,'2026_08_11_120124_add_fazenda_id_to_racoes_table',1);
INSERT INTO `migrations` VALUES (13,'2026_08_11_141827_create_lote_ocorrencias_table',1);
INSERT INTO `migrations` VALUES (14,'2026_08_11_154706_add_doenca_fields_to_lote_ocorrencias_table',1);
INSERT INTO `migrations` VALUES (15,'2026_08_11_160404_add_lote_origem_id_to_lotes_table',1);
INSERT INTO `migrations` VALUES (16,'2026_08_11_163135_add_causa_to_lote_ocorrencias_table',1);
INSERT INTO `migrations` VALUES (17,'2026_08_11_172500_add_valor_kg_to_racaos_table',1);
INSERT INTO `migrations` VALUES (18,'2026_08_11_175642_add_sexo_idade_to_racaos_table',1);
INSERT INTO `migrations` VALUES (19,'2026_08_11_180426_add_finalidade_to_racaos_table',1);
INSERT INTO `migrations` VALUES (20,'2026_08_17_082800_create_codigos_verificacao_table',1);
INSERT INTO `migrations` VALUES (21,'2026_08_17_102301_create_planos_table',1);
INSERT INTO `migrations` VALUES (22,'2026_08_24_120000_add_racao_id_to_lotes_table',1);
INSERT INTO `migrations` VALUES (23,'2026_08_24_130000_create_lote_consumo_snapshots_table',1);
INSERT INTO `migrations` VALUES (24,'2026_08_25_090000_create_acessibilidade_preferencias_table',1);
INSERT INTO `migrations` VALUES (25,'2026_08_25_091000_create_desempenho_lotes_table',1);
INSERT INTO `migrations` VALUES (26,'2026_08_25_102713_create_personal_access_tokens_table',1);
INSERT INTO `migrations` VALUES (27,'2026_08_25_110000_create_comentarios_table',1);
INSERT INTO `migrations` VALUES (28,'2026_08_25_120000_add_nota_to_comentarios_table',1);
INSERT INTO `migrations` VALUES (29,'2026_08_25_130000_add_categorias_to_comentarios_table',1);
INSERT INTO `migrations` VALUES (30,'2026_08_25_140000_make_comentarios_publicos',1);
INSERT INTO `migrations` VALUES (31,'2026_08_25_150000_add_estoque_total_to_racaos_table',1);
INSERT INTO `migrations` VALUES (32,'2026_08_31_150400_create_ordenhas_table',1);
INSERT INTO `migrations` VALUES (33,'2026_09_01_120000_create_racao_estoque_snapshots_table',1);
INSERT INTO `migrations` VALUES (34,'2026_09_08_090000_add_peso_e_quantidade_to_desempenho_lotes_table',1);
INSERT INTO `migrations` VALUES (35,'2026_09_08_100000_add_email_verificado_em_to_cadastros_table',1);
INSERT INTO `migrations` VALUES (36,'2026_09_08_110000_add_raca_to_lotes_table',1);
INSERT INTO `migrations` VALUES (37,'2026_09_08_120000_add_quantidade_cochos_to_lotes_table',1);
INSERT INTO `migrations` VALUES (38,'2026_09_08_120100_reestruturar_cochos_para_geracao_automatica',1);
INSERT INTO `migrations` VALUES (39,'2026_10_05_100000_corrigir_estrutura_agrifarm',1);
INSERT INTO `migrations` VALUES (40,'2026_10_05_110000_corrigir_tipos_acessibilidade_preferencias',1);
INSERT INTO `migrations` VALUES (41,'2026_10_05_140000_add_nutricao_e_controle_to_racaos_table',1);
INSERT INTO `migrations` VALUES (42,'2026_10_06_100000_create_sensores_e_leituras_iot',1);
INSERT INTO `migrations` VALUES (43,'2026_10_06_150000_espacar_numero_no_nome_dos_cochos',1);
INSERT INTO `migrations` VALUES (44,'2026_10_06_150100_remover_fornecedor_reposicao_e_data_entrada',1);
INSERT INTO `migrations` VALUES (45,'2026_10_19_120000_restricoes_ordenha_por_sexo_e_turno',1);
INSERT INTO `migrations` VALUES (46,'2026_10_19_130000_add_fonte_consumo_to_desempenho_lotes_table',1);
INSERT INTO `migrations` VALUES (47,'2026_10_20_100000_add_presenca_animal_to_leituras_iot_table',1);
INSERT INTO `migrations` VALUES (48,'2026_10_20_110000_create_cocho_consumos_table',1);
INSERT INTO `migrations` VALUES (49,'2026_10_20_120000_add_data_entrada_fornecedor_e_localizacao',1);
INSERT INTO `migrations` VALUES (50,'2026_10_20_130000_restricoes_quantidades_nao_negativas',1);
INSERT INTO `migrations` VALUES (51,'2026_10_20_140000_registrar_saldo_inicial_das_racoes_sem_historico',1);
INSERT INTO `migrations` VALUES (52,'2026_10_20_150000_corrigir_horarios_gravados_em_utc',1);

-- --------------------------------------------------------------------------
-- Ordenha só em lote com fêmeas (sexo 'Fêmea' ou 'Misto'): a regra depende
-- da tabela lotes, então é garantida por triggers (um CHECK não enxerga
-- outra tabela). O último impede que um lote com ordenhas vire "Macho".
-- --------------------------------------------------------------------------
DELIMITER ;;
CREATE TRIGGER `ordenhas_somente_femeas_insert` BEFORE INSERT ON `ordenhas` FOR EACH ROW
BEGIN
    IF NOT EXISTS (SELECT 1 FROM lotes WHERE id = NEW.lote_id AND sexo IN ('Fêmea','Misto')) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Ordenha so pode ser registrada em lote com femeas (sexo Femea ou Misto).';
    END IF;
END ;;
CREATE TRIGGER `ordenhas_somente_femeas_update` BEFORE UPDATE ON `ordenhas` FOR EACH ROW
BEGIN
    IF NOT EXISTS (SELECT 1 FROM lotes WHERE id = NEW.lote_id AND sexo IN ('Fêmea','Misto')) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Ordenha so pode ser registrada em lote com femeas (sexo Femea ou Misto).';
    END IF;
END ;;
CREATE TRIGGER `lotes_sexo_com_ordenhas` BEFORE UPDATE ON `lotes` FOR EACH ROW
BEGIN
    IF NOT (NEW.sexo <=> OLD.sexo)
        AND (NEW.sexo IS NULL OR NEW.sexo NOT IN ('Fêmea','Misto'))
        AND EXISTS (SELECT 1 FROM ordenhas WHERE lote_id = OLD.id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Lote com ordenhas registradas nao pode deixar de ter femeas.';
    END IF;
END ;;
DELIMITER ;

SET FOREIGN_KEY_CHECKS = 1;
