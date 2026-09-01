-- ==========================================================================
-- AgriFarm — esquema real do banco de dados
-- Gerado a partir do banco de desenvolvimento (agrifarm_mysql) depois que
-- todas as migrations do Laravel foram aplicadas — reflete exatamente as
-- tabelas, colunas, chaves e relacionamentos que o backend (app/Models +
-- database/migrations) espera. As versões antigas deste arquivo (USUARIOS,
-- FAZENDA, CRIAR_CONTA, DASHBOARD, ANIMAIS, TIPO_RACAO, REGISTRO_CONSUMO,
-- FINANCEIRO, ALERTAS_ESTOQUE, AJUDA_SUPORTE, RELATORIOS_INTELIGENTES) eram
-- só um rascunho inicial e nunca corresponderam ao que o código realmente
-- usa — foram substituídas por completo.
--
-- Nomes de tabela, coluna e os tipos abaixo são os reais, usados pelos
-- Models Eloquent (app/Models) e pelos Controllers. Se o esquema mudar,
-- regenere este arquivo com:
--   mysqldump -h127.0.0.1 -P3307 -uroot --no-data --compact agrifarm_mysql
-- ==========================================================================

CREATE DATABASE IF NOT EXISTS agrifarm_mysql
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE agrifarm_mysql;

SET FOREIGN_KEY_CHECKS = 0;

-- ==========================================================================
-- 1. CONTA E FAZENDA
-- ==========================================================================

-- Conta do usuário (login/perfil) — App\Models\Cadastro. Autenticação é
-- feita por esta tabela (via sessão, ver CadastroController), não pela
-- tabela "users" padrão do Laravel (que fica sem uso real no app).
DROP TABLE IF EXISTS `cadastros`;
CREATE TABLE `cadastros` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `responsavel` varchar(100) NOT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `email_alterado_em` timestamp NULL DEFAULT NULL,
  `telefone` varchar(20) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `senha_alterada_em` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `cadastros_cpf_unique` (`cpf`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Códigos de verificação por e-mail (troca de senha/e-mail, "Esqueci minha
-- senha") — App\Models\CodigoVerificacao. Fluxo próprio do app, não usa a
-- tabela "password_reset_tokens" padrão do Laravel.
DROP TABLE IF EXISTS `codigos_verificacao`;
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

-- Fazenda cadastrada por uma conta — App\Models\Fazenda. Uma conta pode ter
-- várias fazendas; cada fazenda pertence a exatamente uma conta.
DROP TABLE IF EXISTS `fazendas`;
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
  KEY `fk_fazendas_cadastro_id` (`cadastro_id`),
  CONSTRAINT `fk_fazendas_cadastro_id` FOREIGN KEY (`cadastro_id`) REFERENCES `cadastros` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================================================
-- 2. RAÇÃO, LOTE, COCHO E ESTOQUE
-- ==========================================================================

-- Ração cadastrada para uma fazenda — App\Models\Racao. Compatível com um
-- lote quando bate fazenda + tipo de animal + sexo (ver
-- PlanoController::racoesCompativeisComLote()).
DROP TABLE IF EXISTS `racaos`;
CREATE TABLE `racaos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fazenda_id` bigint(20) unsigned DEFAULT NULL,
  `tipo` varchar(225) DEFAULT NULL,
  `sexo` varchar(255) DEFAULT NULL,
  `idade` varchar(255) DEFAULT NULL,
  `finalidade` varchar(255) DEFAULT NULL,
  `nome_racao` varchar(100) NOT NULL,
  `estoque` int(11) NOT NULL,
  `estoque_total` int(10) unsigned DEFAULT NULL,
  `consumo` int(11) NOT NULL,
  `valor_kg` decimal(10,2) DEFAULT NULL,
  `fornecedor` varchar(100) DEFAULT NULL,
  `reposicao` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `racaos_fazenda_id_foreign` (`fazenda_id`),
  CONSTRAINT `racaos_fazenda_id_foreign` FOREIGN KEY (`fazenda_id`) REFERENCES `fazendas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Lote de animais — App\Models\Lote. "racao_id" é o vínculo explícito
-- escolhido na tela de Plano Alimentar (nulo até o usuário vincular
-- manualmente — ver PlanoController::vincularRacao()); "lote_origem_id"
-- referencia o lote de onde este se originou (ex.: separação por doença).
DROP TABLE IF EXISTS `lotes`;
CREATE TABLE `lotes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fazenda_id` bigint(20) unsigned NOT NULL,
  `racao_id` bigint(20) unsigned DEFAULT NULL,
  `lote_origem_id` bigint(20) unsigned DEFAULT NULL,
  `nome_lote` varchar(100) NOT NULL,
  `tipo_animal` varchar(50) NOT NULL,
  `finalidade` varchar(255) DEFAULT NULL,
  `sexo` varchar(255) DEFAULT NULL,
  `idade_media` varchar(255) DEFAULT NULL,
  `quantidade_animais` int(11) NOT NULL DEFAULT 0,
  `peso_medio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `data_entrada` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Ativo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_lotes_fazenda_id` (`fazenda_id`),
  KEY `lotes_lote_origem_id_foreign` (`lote_origem_id`),
  KEY `lotes_racao_id_foreign` (`racao_id`),
  CONSTRAINT `fk_lotes_fazenda_id` FOREIGN KEY (`fazenda_id`) REFERENCES `fazendas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lotes_lote_origem_id_foreign` FOREIGN KEY (`lote_origem_id`) REFERENCES `lotes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lotes_racao_id_foreign` FOREIGN KEY (`racao_id`) REFERENCES `racaos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cocho (comedouro físico) de um lote — App\Models\Cocho.
DROP TABLE IF EXISTS `cochos`;
CREATE TABLE `cochos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lote_id` bigint(20) unsigned NOT NULL,
  `nome_cocho` varchar(255) NOT NULL,
  `capacidade` decimal(10,2) NOT NULL,
  `nivel_atual` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sensor` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_cochos_lote_id` (`lote_id`),
  CONSTRAINT `fk_cochos_lote_id` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Estoque físico (em kg) de uma ração — App\Models\Estoque.
DROP TABLE IF EXISTS `estoques`;
CREATE TABLE `estoques` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `racao_id` bigint(20) unsigned NOT NULL,
  `quantidade` decimal(10,2) NOT NULL DEFAULT 0.00,
  `capacidade` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fornecedor` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_estoques_racao_id` (`racao_id`),
  CONSTRAINT `fk_estoques_racao_id` FOREIGN KEY (`racao_id`) REFERENCES `racaos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================================================
-- 3. ACESSIBILIDADE
-- ==========================================================================

-- Preferências de acessibilidade do usuário logado (tema, tamanho de
-- fonte, zoom, voz), sincronizadas entre dispositivos —
-- App\Models\AcessibilidadePreferencia / AcessibilidadeController. Uma
-- linha por cadastro (upsert em "usuario_id"). "tamanho_fonte" em % (80-150)
-- e "zoom" decimal (0.5-1.3) — mesma faixa dos controles em
-- partials/acessibilidade.blade.php.
DROP TABLE IF EXISTS `acessibilidade_preferencias`;
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

-- ==========================================================================
-- 4. PLANO ALIMENTAR, CONSUMO E RELATÓRIOS
-- ==========================================================================

-- Registro de plano alimentar (histórico usado pela tela de Relatórios via
-- RelatorioController::filtrarPlanos()) — App\Models\Plano. "lote_id" e
-- "racao_id" ficam como texto porque nasceram antes das FKs numéricas da
-- tabela "lotes"/"racaos" — Plano::lote()/Plano::racao() ainda resolvem o
-- relacionamento normalmente pelo id.
DROP TABLE IF EXISTS `planos`;
CREATE TABLE `planos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `racao_id` varchar(255) NOT NULL,
  `lote_id` varchar(255) NOT NULL,
  `quantidade` decimal(10,2) NOT NULL,
  `horario` datetime NOT NULL,
  `consumo_ideal` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Snapshot diário do consumo de um lote — App\Models\LoteConsumoSnapshot.
-- Fonte de dados real da tabela "Consumo por Lote" e dos gráficos de 7
-- dias/mensal do Plano Alimentar (ver PlanoController).
DROP TABLE IF EXISTS `lote_consumo_snapshots`;
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

-- Ocorrências de um lote (morte, doença, recuperação, divisão em outro
-- lote) — App\Models\LoteOcorrencia.
DROP TABLE IF EXISTS `lote_ocorrencias`;
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

-- Relatório manual cadastrado por lote (CRUD próprio, distinto da tela
-- "Relatórios" que é gerada automaticamente a partir de "planos") —
-- App\Models\Relatorio.
DROP TABLE IF EXISTS `relatorios`;
CREATE TABLE `relatorios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lote_id` bigint(20) unsigned DEFAULT NULL,
  `data_relatorio` date NOT NULL,
  `quantidade_animais` int(11) DEFAULT NULL,
  `peso_medio` decimal(8,2) DEFAULT NULL,
  `consumo_total` decimal(10,2) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lote_id` (`lote_id`),
  CONSTRAINT `relatorios_ibfk_1` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Desempenho diário de um lote (consumo, custo, ganho de peso,
-- desperdício, eficiência alimentar) — fonte de dados da API de dashboard
-- (App\Models\Lote::getTabelaDetalhada()/getKpisGlobais()/
-- getConsumoDiarioGrafico(), usadas por LoteController::getDashboardData()).
-- As consultas já tratam ausência de linhas (IFNULL/SUM), então a tabela
-- pode ficar vazia sem quebrar o endpoint até que algo passe a alimentá-la.
DROP TABLE IF EXISTS `desempenho_lotes`;
CREATE TABLE `desempenho_lotes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lote_id` bigint(20) unsigned NOT NULL,
  `data_registro` date NOT NULL,
  `consumo_kg` decimal(10,2) NOT NULL DEFAULT 0.00,
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

-- ==========================================================================
-- 5. INFRAESTRUTURA DO LARAVEL (sessão, cache, fila, migrations)
-- Nomes e colunas fixos pelo próprio framework — não renomear.
-- ==========================================================================

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SESSION_DRIVER=database (ver .env) — sessão de login fica guardada aqui.
DROP TABLE IF EXISTS `sessions`;
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

-- CACHE_STORE=database (ver .env).
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- QUEUE_CONNECTION=database (ver .env) — fila de e-mails/notificações.
DROP TABLE IF EXISTS `jobs`;
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

DROP TABLE IF EXISTS `job_batches`;
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

DROP TABLE IF EXISTS `failed_jobs`;
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

-- Tabelas de autenticação padrão do Laravel (scaffold inicial) — mantidas
-- para compatibilidade com o framework, mas sem uso real no app: login é
-- feito por "cadastros", e a recuperação de senha usa "codigos_verificacao".
DROP TABLE IF EXISTS `users`;
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

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================================================
-- 6. LEGADO (sem Model/uso no código atual — mantida apenas para não
-- perder dados de uma versão anterior do projeto)
-- ==========================================================================

DROP TABLE IF EXISTS `animals`;
CREATE TABLE `animals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lote` varchar(255) NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `raca` varchar(100) NOT NULL,
  `peso` decimal(10,2) NOT NULL DEFAULT 0.00,
  `idade` int(11) DEFAULT NULL,
  `consumo` decimal(10,2) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
