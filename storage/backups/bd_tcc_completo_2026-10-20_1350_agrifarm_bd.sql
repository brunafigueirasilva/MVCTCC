-- ==========================================================================
-- AgriFarm — banco de dados COMPLETO (estrutura + dados reais)
-- Gerado em 20/10/2026 13:51 a partir do banco oficial agrifarm_bd (MariaDB/XAMPP, porta 3307).
-- Horários no fuso de Brasília (America/Sao_Paulo).
--
-- Contém: todas as tabelas, chaves, índices, CHECKs, triggers e os dados de
-- cadastros, fazendas, lotes, cochos, sensores, leituras IoT, rações, estoque,
-- movimentações, planos, ordenhas, ocorrências, consumos e relatórios.
-- Só a ESTRUTURA (sem linhas) de: sessions, personal_access_tokens,
-- password_reset_tokens, codigos_verificacao, cache, cache_locks, jobs,
-- job_batches, failed_jobs — dados temporários/sensíveis (tokens de login).
-- Depois de importar, cada usuário só precisa fazer login de novo.
--
-- Importar (cria o banco agrifarm_bd se não existir e SUBSTITUI as tabelas):
--   phpMyAdmin -> Importar -> este arquivo
--   ou: mysql -h127.0.0.1 -P3307 -uroot < bd_tcc_completo.sql
-- ==========================================================================

-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: agrifarm_bd
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `agrifarm_bd`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `agrifarm_bd` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `agrifarm_bd`;

--
-- Table structure for table `acessibilidade_preferencias`
--

DROP TABLE IF EXISTS `acessibilidade_preferencias`;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acessibilidade_preferencias`
--

LOCK TABLES `acessibilidade_preferencias` WRITE;
/*!40000 ALTER TABLE `acessibilidade_preferencias` DISABLE KEYS */;
INSERT INTO `acessibilidade_preferencias` VALUES (1,1,'light',100,1.00,NULL,1.00,'2026-08-25 11:17:55','2026-10-05 17:19:16'),(2,3,'dark',100,1.00,NULL,1.00,'2026-08-25 11:41:30','2026-08-25 12:01:20'),(3,6,'light',100,1.00,NULL,1.00,'2026-08-25 13:40:12','2026-08-25 14:19:14'),(4,2,'light',100,1.00,NULL,1.00,'2026-09-08 11:20:34','2026-09-08 11:20:34'),(6,13,'light',100,1.00,NULL,1.00,'2026-09-29 10:32:06','2026-09-29 10:45:10'),(7,14,'dark',100,1.00,NULL,1.00,'2026-09-29 10:45:39','2026-09-29 10:46:03'),(8,15,'light',100,1.00,NULL,1.00,'2026-09-29 10:54:44','2026-09-29 10:54:44'),(9,16,'light',100,1.00,NULL,1.00,'2026-09-29 11:12:41','2026-09-29 14:12:59'),(10,20,'light',100,1.00,NULL,1.00,'2026-10-19 13:58:09','2026-10-19 13:58:15');
/*!40000 ALTER TABLE `acessibilidade_preferencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
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

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
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

--
-- Table structure for table `cadastros`
--

DROP TABLE IF EXISTS `cadastros`;
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
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `cadastros_cpf_unique` (`cpf`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cadastros`
--

LOCK TABLES `cadastros` WRITE;
/*!40000 ALTER TABLE `cadastros` DISABLE KEYS */;
INSERT INTO `cadastros` VALUES (1,'Bruna Figueira Silva','49095793893','2008-04-09','brunafsilva.09@icloud.com',NULL,NULL,'19990019586','perfil/38I6p0ymI9fK4mzaqrTnVF2uhUiTo6IDqy7uTITz.png','$2y$12$/4h0uyMbLu2M98r1RCLk/e.WIL866foYZYic7mdG2T0qWvqQ5jzt6',NULL,'2026-08-25 10:56:20','2026-08-25 11:19:48'),(2,'Maria Eduarda Evangelista',NULL,NULL,'maria.e.evangelista7@aluno.senai.br',NULL,NULL,'(19) 99146-9672',NULL,'$2y$12$qEltB7/DE4bMuGPXHfhr6.Uq5j2YW6xsduz.Dep3ip0z38myqqppu',NULL,'2026-08-25 11:01:28','2026-08-25 11:01:28'),(3,'Yasmin',NULL,NULL,'yasmin.gentina@gmail.com.br',NULL,NULL,'(19) 99312-7375',NULL,'$2y$12$CpowZ.OGxihuBTI1PDFyFOxVun8v7I4areIcJzlpWpAOX1rnTj/oS',NULL,'2026-08-25 11:11:02','2026-08-25 11:11:02'),(4,'ARTHUR BARBOSA AZEVEDO',NULL,NULL,'arthur.b.azevedo6@aluno.senai.br',NULL,NULL,'(19) 99584-4882',NULL,'$2y$12$Gn1Bj2RGQj.0YEJFJw/Be.311x292JH9w0F3/3Ac1hLJZlWu1GMfC',NULL,'2026-08-25 13:21:12','2026-08-25 13:21:12'),(5,'Lorena',NULL,NULL,'lorena@gmail.com',NULL,NULL,'(19) 99999-9999',NULL,'$2y$12$mnDrSkZSY1Vq568gdL.YB.5LZWfZ4zlyzBjbKQQQigCCF/gXC4Vom',NULL,'2026-08-25 13:33:09','2026-08-25 13:33:09'),(6,'Arthur Povidelo','42090469811','2008-08-05','arthur.providelo@aluno.senai.br',NULL,NULL,'19991192992',NULL,'$2y$12$g7sC7JC9D7VGMnOMh4m0NepXnE.qsZzw0V5vfj04Ge3dnG1Z..Z06',NULL,'2026-08-25 13:39:46','2026-08-25 13:59:09'),(7,'Maria Fernanda Galdino de Souza Felipe',NULL,NULL,'maria.f.felipe@aluno.senai.br',NULL,NULL,'(11) 98765-4321',NULL,'$2y$12$uBjUORLIhQl8HJJMPx8xb.m4yiunatcz8RwMz/SOXKVAa8JSyNnLG',NULL,'2026-08-25 13:52:44','2026-08-25 13:52:44'),(8,'Bruna Figueira Silva',NULL,NULL,'bruna.f.silva22@icloud.com.br',NULL,NULL,'(19) 99001-9586',NULL,'$2y$12$St14H/mxng9AxdT9/N/LLewIk1TziGq1OWE0ezovy9FCPWJcEFiBy',NULL,'2026-08-31 13:04:47','2026-08-31 13:04:47'),(11,'Bruna',NULL,NULL,'brunafsilva.09@icloud.com.br',NULL,NULL,'19990019586',NULL,'$2y$12$UGQUqyHYLru/elO76x/Kfupd2ZAwEaKY1TxXjnlmL0XySJlVqRnG6',NULL,'2026-09-21 16:46:00','2026-09-21 16:46:00'),(12,'João Emanoel',NULL,NULL,'joao@gmail.com',NULL,NULL,'19994535902',NULL,'$2y$12$REYL8/uVILxOueUDqWPQmulaA8p6FmjOinVgYFntQyCr4XRcGQftq',NULL,'2026-09-22 13:10:30','2026-09-22 13:10:30'),(13,'Maria Eduarda Evangelista',NULL,NULL,'madu@gmail.com',NULL,NULL,'(19) 99146-9672',NULL,'$2y$12$HvmQ3NcVMsvyGmOFFnY5t.pMJf2B6pUQxNLz4PA2AjywzO9wh3AMy',NULL,'2026-09-29 10:31:36','2026-09-29 10:31:36'),(14,'JOÃO EMANOEL PORFIRIO DE SOUZA',NULL,NULL,'joao.e.souza12@aluno.senai.br',NULL,NULL,'19999999999','perfil/arYpmbOYBzKycr9ypdaYfuiacbWG7N756zHy1oYG.jpg','$2y$12$yBGXrqIhupJF3JiTOcO/Q.FQnPcA5mt8FPcGqgpFMJKxDLIS362/q',NULL,'2026-09-29 10:34:32','2026-09-29 10:52:00'),(15,'zaz',NULL,NULL,'princisi@gmail.com',NULL,NULL,'(12) 12344-5554',NULL,'$2y$12$oxeZaYig2gSag1XFsdpNBO7BtcZk9qz.FMixOulmbYKshwaalufUm',NULL,'2026-09-29 10:54:12','2026-09-29 10:54:12'),(16,'Yasmin','45290641823','2008-05-20','yas@gmail.com',NULL,NULL,'19993127375',NULL,'$2y$12$o8aYWxLoYjxMq2BajcD1qu3F0EtC3HvmhOmXjP.jjSXyGnPZ4q.Jy',NULL,'2026-09-29 11:09:00','2026-09-29 11:39:27'),(17,'Yasmin',NULL,NULL,'yas@gmail',NULL,NULL,'19993127375',NULL,'$2y$12$41k39LqJJh.yDnVMZrH2rO2qopH5xCXQaM9C/IfWv4jcECrgy9cFS',NULL,'2026-09-29 11:50:12','2026-09-29 11:50:12'),(19,'Murilo',NULL,NULL,'murilo@gmail.com',NULL,NULL,'77777777777','perfil/PRxeITo9pWyLnDNtmEyCjEYfI7aOXq2xPNPoSnc4.png','$2y$12$ylt3LAEbS/qNy/448VLyEekrNjQM6JZLCrEEASvScFVOdtTy3qgH2',NULL,'2026-09-29 14:36:21','2026-09-29 14:37:31'),(20,'Bruna Figueira Silva',NULL,NULL,'bruna.f.silva22@aluno.senai.br',NULL,NULL,'19990019586','perfil/Qi7NrO69pGJLRzG9tc0OyJWII97U3VcRRR1T4sFx.jpg','$2y$12$7prHjBcg8yuLwoCe8cH2hOyJVLLDI05Ml3DHEtkvab9uXs9xo9mFG',NULL,'2026-10-19 11:17:35','2026-10-20 13:07:01'),(25,'Bruna Demonstração',NULL,NULL,'demo.comparacao@teste.local',NULL,NULL,'(11) 98888-7777',NULL,'$2y$12$ckukjGcwT4Dey6zvmd529emy2zwFQ16HmYNOQTHZ3Clrh/M0BdSmC',NULL,'2026-10-20 16:32:47','2026-10-20 16:32:47');
/*!40000 ALTER TABLE `cadastros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cocho_consumos`
--

DROP TABLE IF EXISTS `cocho_consumos`;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cocho_consumos`
--

LOCK TABLES `cocho_consumos` WRITE;
/*!40000 ALTER TABLE `cocho_consumos` DISABLE KEYS */;
INSERT INTO `cocho_consumos` VALUES (3,36,48,222,34,25,'manual',35.00,140.00,105.00,2.80,'Trato da manhã','2026-10-20 13:33:08','2026-10-20 16:33:08','2026-10-20 16:33:08');
/*!40000 ALTER TABLE `cocho_consumos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cochos`
--

DROP TABLE IF EXISTS `cochos`;
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
  KEY `fk_cochos_lote_id` (`lote_id`),
  CONSTRAINT `fk_cochos_lote_id` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cochos_capacidade_positiva` CHECK (`capacidade` > 0),
  CONSTRAINT `cochos_nivel_na_capacidade` CHECK (`nivel_atual` >= 0 and `nivel_atual` <= `capacidade`)
) ENGINE=InnoDB AUTO_INCREMENT=229 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cochos`
--

LOCK TABLES `cochos` WRITE;
/*!40000 ALTER TABLE `cochos` DISABLE KEYS */;
INSERT INTO `cochos` VALUES (19,23,1,'Lote A1 - 01',NULL,200.00,200.00,NULL,'2026-09-22 14:03:13','2026-09-22 14:03:13'),(20,23,2,'Lote A1 - 02',NULL,200.00,123.45,NULL,'2026-09-22 14:03:13','2026-10-06 16:32:54'),(21,23,3,'Lote A1 - 03',NULL,200.00,200.00,NULL,'2026-09-22 14:03:13','2026-09-22 14:03:13'),(22,23,4,'Lote A1 - 04',NULL,200.00,200.00,NULL,'2026-09-22 14:03:13','2026-09-22 14:03:13'),(23,23,5,'Lote A1 - 05',NULL,200.00,200.00,NULL,'2026-09-22 14:03:13','2026-09-22 14:03:13'),(24,23,6,'Lote A1 - 06',NULL,200.00,200.00,NULL,'2026-09-22 14:03:13','2026-09-22 14:03:13'),(25,23,7,'Lote A1 - 07',NULL,200.00,200.00,NULL,'2026-09-22 14:03:13','2026-09-22 14:03:13'),(26,23,8,'Lote A1 - 08',NULL,200.00,200.00,NULL,'2026-09-22 14:03:13','2026-09-22 14:03:13'),(27,23,9,'Lote A1 - 09',NULL,200.00,200.00,NULL,'2026-09-22 14:03:13','2026-09-22 14:03:13'),(28,23,10,'Lote A1 - 10',NULL,200.00,200.00,NULL,'2026-09-22 14:03:13','2026-09-22 14:03:13'),(29,24,1,'Lote B1 - 01',NULL,200.00,50.00,NULL,'2026-09-22 14:06:08','2026-09-22 14:06:08'),(30,24,2,'Lote B1 - 02',NULL,200.00,50.00,NULL,'2026-09-22 14:06:08','2026-09-22 14:06:08'),(31,24,3,'Lote B1 - 03',NULL,200.00,50.00,NULL,'2026-09-22 14:06:08','2026-09-22 14:06:08'),(32,24,4,'Lote B1 - 04',NULL,200.00,50.00,NULL,'2026-09-22 14:06:08','2026-09-22 14:06:08'),(33,25,1,'Lote C1 - 01',NULL,10.00,5.00,NULL,'2026-09-22 14:10:36','2026-09-22 14:10:36'),(34,25,2,'Lote C1 - 02',NULL,10.00,5.00,NULL,'2026-09-22 14:10:36','2026-09-22 14:10:36'),(35,25,3,'Lote C1 - 03',NULL,10.00,5.00,NULL,'2026-09-22 14:10:36','2026-09-22 14:10:36'),(36,25,4,'Lote C1 - 04',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(37,25,5,'Lote C1 - 05',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(38,25,6,'Lote C1 - 06',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(39,25,7,'Lote C1 - 07',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(40,25,8,'Lote C1 - 08',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(41,25,9,'Lote C1 - 09',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(42,25,10,'Lote C1 - 10',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(43,25,11,'Lote C1 - 11',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(44,25,12,'Lote C1 - 12',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(45,25,13,'Lote C1 - 13',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(46,25,14,'Lote C1 - 14',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(47,25,15,'Lote C1 - 15',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(48,25,16,'Lote C1 - 16',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(49,25,17,'Lote C1 - 17',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(50,25,18,'Lote C1 - 18',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(51,25,19,'Lote C1 - 19',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(52,25,20,'Lote C1 - 20',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(53,25,21,'Lote C1 - 21',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(54,25,22,'Lote C1 - 22',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(55,25,23,'Lote C1 - 23',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(56,25,24,'Lote C1 - 24',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(57,25,25,'Lote C1 - 25',NULL,10.00,5.00,NULL,'2026-09-22 14:10:37','2026-09-22 14:10:37'),(58,26,1,'Lote D1 - 01',NULL,100.00,20.00,NULL,'2026-09-22 14:16:59','2026-09-22 14:16:59'),(59,26,2,'Lote D1 - 02',NULL,100.00,20.00,NULL,'2026-09-22 14:16:59','2026-09-22 14:16:59'),(60,26,3,'Lote D1 - 03',NULL,100.00,20.00,NULL,'2026-09-22 14:16:59','2026-09-22 14:16:59'),(61,26,4,'Lote D1 - 04',NULL,100.00,20.00,NULL,'2026-09-22 14:16:59','2026-09-22 14:16:59'),(62,26,5,'Lote D1 - 05',NULL,100.00,20.00,NULL,'2026-09-22 14:16:59','2026-09-22 14:16:59'),(63,27,1,'Lote A2 - 01',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(64,27,2,'Lote A2 - 02',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(65,27,3,'Lote A2 - 03',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(66,27,4,'Lote A2 - 04',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(67,27,5,'Lote A2 - 05',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(68,27,6,'Lote A2 - 06',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(69,27,7,'Lote A2 - 07',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(70,27,8,'Lote A2 - 08',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(71,27,9,'Lote A2 - 09',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(72,27,10,'Lote A2 - 10',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(73,27,11,'Lote A2 - 11',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(74,27,12,'Lote A2 - 12',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(75,27,13,'Lote A2 - 13',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(76,27,14,'Lote A2 - 14',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(77,27,15,'Lote A2 - 15',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(78,27,16,'Lote A2 - 16',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(79,27,17,'Lote A2 - 17',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(80,27,18,'Lote A2 - 18',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(81,27,19,'Lote A2 - 19',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(82,27,20,'Lote A2 - 20',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(83,27,21,'Lote A2 - 21',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(84,27,22,'Lote A2 - 22',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(85,27,23,'Lote A2 - 23',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(86,27,24,'Lote A2 - 24',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(87,27,25,'Lote A2 - 25',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(88,27,26,'Lote A2 - 26',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(89,27,27,'Lote A2 - 27',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(90,27,28,'Lote A2 - 28',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(91,27,29,'Lote A2 - 29',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(92,27,30,'Lote A2 - 30',NULL,400.00,20.00,NULL,'2026-09-28 10:44:47','2026-09-28 10:44:47'),(93,28,1,'a1 - 01',NULL,122.00,100.00,NULL,'2026-09-29 10:38:32','2026-09-29 10:38:32'),(94,28,2,'a1 - 02',NULL,122.00,100.00,NULL,'2026-09-29 10:38:32','2026-09-29 10:38:32'),(95,28,3,'a1 - 03',NULL,122.00,100.00,NULL,'2026-09-29 10:38:32','2026-09-29 10:38:32'),(96,29,1,'Lote A1 - 01',NULL,200.00,180.00,NULL,'2026-09-29 10:55:16','2026-09-29 11:25:40'),(97,29,2,'Lote A1 - 02',NULL,200.00,190.00,NULL,'2026-09-29 10:55:16','2026-09-29 11:26:02'),(98,29,3,'Lote A1 - 03',NULL,200.00,170.00,NULL,'2026-09-29 10:55:16','2026-09-29 11:31:28'),(99,29,4,'Lote A1 - 04',NULL,200.00,150.00,NULL,'2026-09-29 10:55:16','2026-09-29 10:55:16'),(100,29,5,'Lote A1 - 05',NULL,200.00,180.00,NULL,'2026-09-29 10:55:16','2026-09-29 11:31:57'),(101,29,6,'Lote A1 - 06',NULL,200.00,150.00,NULL,'2026-09-29 10:55:16','2026-09-29 10:55:16'),(102,29,7,'Lote A1 - 07',NULL,200.00,150.00,NULL,'2026-09-29 10:55:16','2026-09-29 10:55:16'),(103,29,8,'Lote A1 - 08',NULL,200.00,150.00,NULL,'2026-09-29 10:55:16','2026-09-29 10:55:16'),(104,29,9,'Lote A1 - 09',NULL,200.00,150.00,NULL,'2026-09-29 10:55:16','2026-09-29 10:55:16'),(105,29,10,'Lote A1 - 10',NULL,200.00,150.00,NULL,'2026-09-29 10:55:16','2026-09-29 10:55:16'),(106,30,1,'Lote A2 - 01',NULL,100.00,100.00,'4545','2026-09-29 10:57:02','2026-09-29 11:25:12'),(107,31,1,'Lote A3 - 01',NULL,80.00,29.98,NULL,'2026-09-29 11:00:46','2026-09-29 11:00:46'),(108,31,2,'Lote A3 - 02',NULL,80.00,29.98,NULL,'2026-09-29 11:00:46','2026-09-29 11:00:46'),(109,31,3,'Lote A3 - 03',NULL,80.00,29.98,NULL,'2026-09-29 11:00:46','2026-09-29 11:00:46'),(110,31,4,'Lote A3 - 04',NULL,80.00,29.98,NULL,'2026-09-29 11:00:46','2026-09-29 11:00:46'),(111,31,5,'Lote A3 - 05',NULL,80.00,29.98,NULL,'2026-09-29 11:00:46','2026-09-29 11:00:46'),(112,31,6,'Lote A3 - 06',NULL,80.00,29.98,NULL,'2026-09-29 11:00:46','2026-09-29 11:00:46'),(113,31,7,'Lote A3 - 07',NULL,80.00,29.98,NULL,'2026-09-29 11:00:46','2026-09-29 11:00:46'),(114,31,8,'Lote A3 - 08',NULL,80.00,29.98,NULL,'2026-09-29 11:00:46','2026-09-29 11:00:46'),(115,31,9,'Lote A3 - 09',NULL,80.00,29.98,NULL,'2026-09-29 11:00:46','2026-09-29 11:00:46'),(116,31,10,'Lote A3 - 10',NULL,80.00,29.98,NULL,'2026-09-29 11:00:46','2026-09-29 11:00:46'),(117,31,11,'Lote A3 - 11',NULL,80.00,29.98,NULL,'2026-09-29 11:00:46','2026-09-29 11:00:46'),(118,31,12,'Lote A3 - 12',NULL,80.00,29.98,NULL,'2026-09-29 11:00:46','2026-09-29 11:00:46'),(119,33,1,'lote 1 - 01',NULL,300.00,200.00,NULL,'2026-09-29 11:12:19','2026-09-29 11:12:19'),(120,33,2,'lote 1 - 02',NULL,300.00,200.00,NULL,'2026-09-29 11:12:19','2026-09-29 11:12:19'),(121,33,3,'lote 1 - 03',NULL,300.00,200.00,NULL,'2026-09-29 11:12:19','2026-09-29 11:12:19'),(122,33,4,'lote 1 - 04',NULL,300.00,200.00,NULL,'2026-09-29 11:12:19','2026-09-29 11:12:19'),(123,33,5,'lote 1 - 05',NULL,300.00,200.00,NULL,'2026-09-29 11:12:19','2026-09-29 11:12:19'),(124,33,6,'lote 1 - 06',NULL,300.00,200.00,NULL,'2026-09-29 11:12:19','2026-09-29 11:12:19'),(125,33,7,'lote 1 - 07',NULL,300.00,200.00,NULL,'2026-09-29 11:12:19','2026-09-29 11:12:19'),(126,33,8,'lote 1 - 08',NULL,300.00,200.00,NULL,'2026-09-29 11:12:19','2026-09-29 11:12:19'),(127,33,9,'lote 1 - 09',NULL,300.00,200.00,NULL,'2026-09-29 11:12:19','2026-09-29 11:12:19'),(128,33,10,'lote 1 - 10',NULL,300.00,200.00,NULL,'2026-09-29 11:12:19','2026-09-29 11:12:19'),(129,34,1,'lote 2 - 01',NULL,400.00,350.00,NULL,'2026-09-29 11:29:48','2026-09-29 11:29:48'),(130,34,2,'lote 2 - 02',NULL,400.00,350.00,NULL,'2026-09-29 11:29:48','2026-09-29 11:29:48'),(131,34,3,'lote 2 - 03',NULL,400.00,350.00,NULL,'2026-09-29 11:29:48','2026-09-29 11:29:48'),(132,34,4,'lote 2 - 04',NULL,400.00,350.00,NULL,'2026-09-29 11:29:48','2026-09-29 11:29:48'),(133,34,5,'lote 2 - 05',NULL,400.00,350.00,NULL,'2026-09-29 11:29:48','2026-09-29 11:29:48'),(134,34,6,'lote 2 - 06',NULL,400.00,350.00,NULL,'2026-09-29 11:29:48','2026-09-29 11:29:48'),(135,34,7,'lote 2 - 07',NULL,400.00,350.00,NULL,'2026-09-29 11:29:48','2026-09-29 11:29:48'),(136,34,8,'lote 2 - 08',NULL,400.00,350.00,NULL,'2026-09-29 11:29:49','2026-09-29 11:29:49'),(137,34,9,'lote 2 - 09',NULL,400.00,350.00,NULL,'2026-09-29 11:29:49','2026-09-29 11:29:49'),(138,34,10,'lote 2 - 10',NULL,400.00,350.00,NULL,'2026-09-29 11:29:49','2026-09-29 11:29:49'),(139,34,11,'lote 2 - 11',NULL,400.00,350.00,NULL,'2026-09-29 11:29:49','2026-09-29 11:29:49'),(140,34,12,'lote 2 - 12',NULL,400.00,350.00,NULL,'2026-09-29 11:29:49','2026-09-29 11:29:49'),(141,34,13,'lote 2 - 13',NULL,400.00,350.00,NULL,'2026-09-29 11:29:49','2026-09-29 11:29:49'),(142,34,14,'lote 2 - 14',NULL,400.00,350.00,NULL,'2026-09-29 11:29:49','2026-09-29 11:29:49'),(143,34,15,'lote 2 - 15',NULL,400.00,350.00,NULL,'2026-09-29 11:29:49','2026-09-29 11:29:49'),(144,34,16,'lote 2 - 16',NULL,400.00,350.00,NULL,'2026-09-29 11:29:49','2026-09-29 11:29:49'),(145,35,1,'Lote F1 - 01',NULL,100.00,100.00,NULL,'2026-10-06 13:50:15','2026-10-06 13:50:15'),(146,35,2,'Lote F1 - 02',NULL,100.00,100.00,NULL,'2026-10-06 13:50:15','2026-10-06 13:50:15'),(147,35,3,'Lote F1 - 03',NULL,100.00,100.00,NULL,'2026-10-06 13:50:15','2026-10-06 13:50:15'),(148,35,4,'Lote F1 - 04',NULL,100.00,100.00,NULL,'2026-10-06 13:50:15','2026-10-06 13:50:15'),(149,35,5,'Lote F1 - 05',NULL,100.00,100.00,NULL,'2026-10-06 13:50:15','2026-10-06 13:50:15'),(150,35,6,'Lote F1 - 06',NULL,100.00,100.00,NULL,'2026-10-06 13:50:15','2026-10-06 13:50:15'),(151,35,7,'Lote F1 - 07',NULL,100.00,100.00,NULL,'2026-10-06 13:50:15','2026-10-06 13:50:15'),(152,35,8,'Lote F1 - 08',NULL,100.00,100.00,NULL,'2026-10-06 13:50:15','2026-10-06 13:50:15'),(154,36,1,'Lote A1 - 01',NULL,200.00,20.00,NULL,'2026-10-19 11:22:16','2026-10-20 13:21:46'),(155,36,2,'Lote A1 - 02',NULL,200.00,101.67,NULL,'2026-10-19 11:22:16','2026-10-19 11:28:00'),(156,36,3,'Lote A1 - 03',NULL,200.00,101.67,NULL,'2026-10-19 11:22:16','2026-10-19 11:28:00'),(157,36,4,'Lote A1 - 04',NULL,200.00,101.67,NULL,'2026-10-19 11:22:16','2026-10-19 11:28:00'),(158,36,5,'Lote A1 - 05',NULL,200.00,101.67,NULL,'2026-10-19 11:22:16','2026-10-19 11:28:00'),(159,36,6,'Lote A1 - 06',NULL,200.00,101.67,NULL,'2026-10-19 11:22:16','2026-10-19 11:28:00'),(160,36,7,'Lote A1 - 07',NULL,200.00,101.67,NULL,'2026-10-19 11:22:16','2026-10-19 11:28:00'),(161,36,8,'Lote A1 - 08',NULL,200.00,101.67,NULL,'2026-10-19 11:22:16','2026-10-19 11:28:00'),(162,36,9,'Lote A1 - 09',NULL,200.00,101.67,NULL,'2026-10-19 11:22:16','2026-10-19 11:28:00'),(163,36,10,'Lote A1 - 10',NULL,200.00,101.67,NULL,'2026-10-19 11:22:16','2026-10-19 11:28:00'),(164,36,11,'Lote A1 - 11',NULL,200.00,101.67,NULL,'2026-10-19 11:22:16','2026-10-19 11:28:00'),(165,36,12,'Lote A1 - 12',NULL,200.00,101.67,NULL,'2026-10-19 11:22:17','2026-10-19 11:28:00'),(172,37,1,'Lote A2 - 01',NULL,200.00,140.00,NULL,'2026-10-19 11:32:52','2026-10-19 16:12:17'),(173,37,2,'Lote A2 - 02',NULL,200.00,140.00,NULL,'2026-10-19 11:32:52','2026-10-19 16:12:17'),(174,37,3,'Lote A2 - 03',NULL,200.00,140.00,NULL,'2026-10-19 11:32:52','2026-10-19 16:12:17'),(175,37,4,'Lote A2 - 04',NULL,200.00,140.00,NULL,'2026-10-19 11:32:52','2026-10-19 16:12:17'),(176,37,5,'Lote A2 - 05',NULL,200.00,140.00,NULL,'2026-10-19 11:32:52','2026-10-19 16:12:17'),(177,37,6,'Lote A2 - 06',NULL,200.00,140.00,NULL,'2026-10-19 11:32:52','2026-10-19 16:12:17'),(178,37,7,'Lote A2 - 07',NULL,200.00,140.00,NULL,'2026-10-19 11:32:52','2026-10-19 16:12:17'),(179,37,8,'Lote A2 - 08',NULL,200.00,140.00,NULL,'2026-10-19 11:32:52','2026-10-19 16:12:17'),(180,37,9,'Lote A2 - 09',NULL,200.00,140.00,NULL,'2026-10-19 11:32:52','2026-10-19 16:12:17'),(181,37,10,'Lote A2 - 10',NULL,200.00,140.00,NULL,'2026-10-19 11:32:52','2026-10-19 16:12:17'),(182,37,11,'Lote A2 - 11',NULL,400.00,200.00,NULL,'2026-10-19 14:23:08','2026-10-19 16:12:17'),(183,37,12,'Lote A2 - 12',NULL,400.00,200.00,NULL,'2026-10-19 14:23:08','2026-10-19 16:12:17'),(184,37,13,'Lote A2 - 13',NULL,400.00,200.00,NULL,'2026-10-19 14:23:08','2026-10-19 16:12:17'),(185,37,14,'Lote A2 - 14',NULL,400.00,200.00,NULL,'2026-10-19 14:23:08','2026-10-19 16:12:17'),(186,37,15,'Lote A2 - 15',NULL,400.00,200.00,NULL,'2026-10-19 14:23:08','2026-10-19 16:12:17'),(187,37,16,'Lote A2 - 16',NULL,400.00,200.00,NULL,'2026-10-19 14:23:08','2026-10-19 16:12:17'),(188,37,17,'Lote A2 - 17',NULL,400.00,200.00,NULL,'2026-10-19 14:23:08','2026-10-19 16:12:17'),(189,37,18,'Lote A2 - 18',NULL,400.00,200.00,NULL,'2026-10-19 14:23:08','2026-10-19 16:12:17'),(190,37,19,'Lote A2 - 19',NULL,400.00,200.00,NULL,'2026-10-19 14:23:08','2026-10-19 16:12:17'),(191,37,20,'Lote A2 - 20',NULL,400.00,200.00,NULL,'2026-10-19 14:23:08','2026-10-19 16:12:17'),(192,37,21,'Lote A2 - 21',NULL,400.00,200.00,NULL,'2026-10-19 14:23:08','2026-10-19 16:12:17'),(193,37,22,'Lote A2 - 22',NULL,400.00,200.00,NULL,'2026-10-19 14:23:08','2026-10-19 16:12:17'),(194,37,23,'Lote A2 - 23',NULL,400.00,200.00,NULL,'2026-10-19 14:23:08','2026-10-19 16:12:17'),(195,37,24,'Lote A2 - 24',NULL,400.00,200.00,NULL,'2026-10-19 14:23:08','2026-10-19 16:12:17'),(196,39,1,'Lote C1 - 01',NULL,100.00,50.00,NULL,'2026-10-19 16:15:00','2026-10-19 16:15:00'),(197,39,2,'Lote C1 - 02',NULL,100.00,50.00,NULL,'2026-10-19 16:15:24','2026-10-19 16:15:24'),(198,39,3,'Lote C1 - 03',NULL,100.00,50.00,NULL,'2026-10-19 16:15:24','2026-10-19 16:15:24'),(199,39,4,'Lote C1 - 04',NULL,100.00,50.00,NULL,'2026-10-19 16:15:24','2026-10-19 16:15:24'),(200,39,5,'Lote C1 - 05',NULL,100.00,50.00,NULL,'2026-10-19 16:15:24','2026-10-19 16:15:24'),(201,40,1,'Lote D1 - 01',NULL,400.00,100.00,NULL,'2026-10-19 16:16:31','2026-10-19 16:16:31'),(202,40,2,'Lote D1 - 02',NULL,400.00,100.00,NULL,'2026-10-19 16:16:31','2026-10-19 16:16:31'),(203,40,3,'Lote D1 - 03',NULL,400.00,100.00,NULL,'2026-10-19 16:16:31','2026-10-19 16:16:31'),(204,40,4,'Lote D1 - 04',NULL,400.00,100.00,NULL,'2026-10-19 16:16:31','2026-10-19 16:16:31'),(205,40,5,'Lote D1 - 05',NULL,400.00,100.00,NULL,'2026-10-19 16:16:31','2026-10-19 16:16:31'),(206,41,1,'Lote F1 - 01',NULL,400.00,100.00,NULL,'2026-10-19 16:19:00','2026-10-19 16:19:00'),(207,41,2,'Lote F1 - 02',NULL,400.00,100.00,NULL,'2026-10-19 16:19:00','2026-10-19 16:19:00'),(208,41,3,'Lote F1 - 03',NULL,400.00,100.00,NULL,'2026-10-19 16:19:00','2026-10-19 16:19:00'),(209,41,4,'Lote F1 - 04',NULL,400.00,100.00,NULL,'2026-10-19 16:19:00','2026-10-19 16:19:00'),(210,41,5,'Lote F1 - 05',NULL,400.00,100.00,NULL,'2026-10-19 16:19:00','2026-10-19 16:19:00'),(211,41,6,'Lote F1 - 06',NULL,400.00,100.00,NULL,'2026-10-19 16:19:00','2026-10-19 16:19:00'),(212,41,7,'Lote F1 - 07',NULL,400.00,100.00,NULL,'2026-10-19 16:19:00','2026-10-19 16:19:00'),(213,41,8,'Lote F1 - 08',NULL,400.00,100.00,NULL,'2026-10-19 16:19:00','2026-10-19 16:19:00'),(214,38,1,'Lote B1 - 01',NULL,400.00,400.00,NULL,'2026-10-19 16:38:14','2026-10-19 16:38:14'),(215,38,2,'Lote B1 - 02',NULL,400.00,400.00,NULL,'2026-10-19 16:38:14','2026-10-19 16:38:14'),(222,48,1,'Vacas Leiteiras - 01',NULL,200.00,105.00,NULL,'2026-10-20 16:32:52','2026-10-20 16:33:08'),(223,48,2,'Vacas Leiteiras - 02',NULL,200.00,140.00,NULL,'2026-10-20 16:32:52','2026-10-20 16:33:00'),(224,48,3,'Vacas Leiteiras - 03',NULL,200.00,140.00,NULL,'2026-10-20 16:32:52','2026-10-20 16:33:00'),(225,49,1,'Suínos Engorda - 01',NULL,150.00,90.00,NULL,'2026-10-20 16:32:53','2026-10-20 16:33:03'),(226,49,2,'Suínos Engorda - 02',NULL,150.00,90.00,NULL,'2026-10-20 16:32:53','2026-10-20 16:33:03'),(227,50,1,'Galinhas Poedeiras - 01',NULL,80.00,45.00,NULL,'2026-10-20 16:32:54','2026-10-20 16:33:06'),(228,50,2,'Galinhas Poedeiras - 02',NULL,80.00,45.00,NULL,'2026-10-20 16:32:54','2026-10-20 16:33:06');
/*!40000 ALTER TABLE `cochos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `codigos_verificacao`
--

DROP TABLE IF EXISTS `codigos_verificacao`;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comentarios`
--

DROP TABLE IF EXISTS `comentarios`;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comentarios`
--

LOCK TABLES `comentarios` WRITE;
/*!40000 ALTER TABLE `comentarios` DISABLE KEYS */;
INSERT INTO `comentarios` VALUES (1,1,NULL,'Site muito bom!',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-25 14:48:01','2026-08-25 14:48:01'),(4,1,'Bruna Figueira Silva','Gostei!',4,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-25 16:58:52','2026-08-25 16:58:52'),(5,1,'Bruna Figueira Silva','Bom!',3,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-25 17:26:32','2026-08-25 17:26:32'),(6,1,'Bruna Figueira Silva','Muito bom!',5,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-25 18:01:32','2026-08-25 18:01:32'),(7,1,'Bruna Figueira Silva','Excelente!',5,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-25 18:01:44','2026-08-25 18:01:44'),(8,1,'Bruna Figueira Silva','Bom demais!',5,5,5,5,5,5,5,'2026-08-25 18:02:19','2026-08-25 18:02:19');
/*!40000 ALTER TABLE `comentarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `desempenho_lotes`
--

DROP TABLE IF EXISTS `desempenho_lotes`;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `desempenho_lotes`
--

LOCK TABLES `desempenho_lotes` WRITE;
/*!40000 ALTER TABLE `desempenho_lotes` DISABLE KEYS */;
INSERT INTO `desempenho_lotes` VALUES (9,23,478.00,40,'2026-10-05',0.00,NULL,0.00,0.00,0.00,0.00,'2026-10-05 14:49:50','2026-10-05 14:49:50'),(10,36,740.00,80,'2026-10-19',0.00,NULL,0.00,0.00,0.00,0.00,'2026-10-19 16:26:56','2026-10-19 16:26:56');
/*!40000 ALTER TABLE `desempenho_lotes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estoque_movimentacoes`
--

DROP TABLE IF EXISTS `estoque_movimentacoes`;
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
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estoque_movimentacoes`
--

LOCK TABLES `estoque_movimentacoes` WRITE;
/*!40000 ALTER TABLE `estoque_movimentacoes` DISABLE KEYS */;
INSERT INTO `estoque_movimentacoes` VALUES (1,1,23,1,NULL,NULL,'entrada','cadastro_racao',100.00,0.00,100.00,1.50,'Estoque inicial no cadastro da ração.','2026-10-06 09:35:20','2026-10-06 12:35:20','2026-10-06 12:35:20'),(4,30,26,20,NULL,NULL,'entrada','cadastro_racao',500.00,0.00,500.00,1.50,'Estoque inicial no cadastro da ração.','2026-10-19 08:26:54','2026-10-19 11:26:54','2026-10-19 11:26:54'),(5,30,26,20,36,154,'saida','abastecimento_cocho',1.67,500.00,498.33,1.50,NULL,'2026-10-19 08:28:00','2026-10-19 11:28:00','2026-10-19 11:28:00'),(6,30,26,20,36,155,'saida','abastecimento_cocho',1.67,498.33,496.66,1.50,NULL,'2026-10-19 08:28:00','2026-10-19 11:28:00','2026-10-19 11:28:00'),(7,30,26,20,36,156,'saida','abastecimento_cocho',1.67,496.66,494.99,1.50,NULL,'2026-10-19 08:28:00','2026-10-19 11:28:00','2026-10-19 11:28:00'),(8,30,26,20,36,157,'saida','abastecimento_cocho',1.67,494.99,493.32,1.50,NULL,'2026-10-19 08:28:00','2026-10-19 11:28:00','2026-10-19 11:28:00'),(9,30,26,20,36,158,'saida','abastecimento_cocho',1.67,493.32,491.65,1.50,NULL,'2026-10-19 08:28:00','2026-10-19 11:28:00','2026-10-19 11:28:00'),(10,30,26,20,36,159,'saida','abastecimento_cocho',1.67,491.65,489.98,1.50,NULL,'2026-10-19 08:28:00','2026-10-19 11:28:00','2026-10-19 11:28:00'),(11,30,26,20,36,160,'saida','abastecimento_cocho',1.67,489.98,488.31,1.50,NULL,'2026-10-19 08:28:00','2026-10-19 11:28:00','2026-10-19 11:28:00'),(12,30,26,20,36,161,'saida','abastecimento_cocho',1.67,488.31,486.64,1.50,NULL,'2026-10-19 08:28:00','2026-10-19 11:28:00','2026-10-19 11:28:00'),(13,30,26,20,36,162,'saida','abastecimento_cocho',1.67,486.64,484.97,1.50,NULL,'2026-10-19 08:28:00','2026-10-19 11:28:00','2026-10-19 11:28:00'),(14,30,26,20,36,163,'saida','abastecimento_cocho',1.67,484.97,483.30,1.50,NULL,'2026-10-19 08:28:00','2026-10-19 11:28:00','2026-10-19 11:28:00'),(15,30,26,20,36,164,'saida','abastecimento_cocho',1.67,483.30,481.63,1.50,NULL,'2026-10-19 08:28:00','2026-10-19 11:28:00','2026-10-19 11:28:00'),(16,30,26,20,36,165,'saida','abastecimento_cocho',1.67,481.63,479.96,1.50,NULL,'2026-10-19 08:28:00','2026-10-19 11:28:00','2026-10-19 11:28:00'),(17,30,26,20,36,NULL,'saida','abastecimento_cocho',1.67,479.96,478.29,1.50,NULL,'2026-10-19 08:28:00','2026-10-19 11:28:00','2026-10-19 11:28:00'),(18,30,26,20,36,NULL,'saida','abastecimento_cocho',1.67,478.29,476.62,1.50,NULL,'2026-10-19 08:28:00','2026-10-19 11:28:00','2026-10-19 11:28:00'),(19,30,26,20,36,NULL,'saida','abastecimento_cocho',1.67,476.62,474.95,1.50,NULL,'2026-10-19 08:28:00','2026-10-19 11:28:00','2026-10-19 11:28:00'),(20,30,26,20,36,NULL,'saida','abastecimento_cocho',1.67,474.95,473.28,1.50,NULL,'2026-10-19 08:28:00','2026-10-19 11:28:00','2026-10-19 11:28:00'),(21,30,26,20,36,NULL,'saida','abastecimento_cocho',1.67,473.28,471.61,1.50,NULL,'2026-10-19 08:28:00','2026-10-19 11:28:00','2026-10-19 11:28:00'),(22,30,26,20,36,NULL,'saida','abastecimento_cocho',1.67,471.61,469.94,1.50,NULL,'2026-10-19 08:28:00','2026-10-19 11:28:00','2026-10-19 11:28:00'),(23,30,27,20,NULL,NULL,'entrada','cadastro_racao',500.00,0.00,500.00,1.00,'Estoque inicial no cadastro da ração.','2026-10-19 08:35:01','2026-10-19 11:35:01','2026-10-19 11:35:01'),(24,30,27,20,37,172,'saida','abastecimento_cocho',40.00,500.00,460.00,1.00,NULL,'2026-10-19 08:35:19','2026-10-19 11:35:20','2026-10-19 11:35:20'),(25,30,27,20,37,173,'saida','abastecimento_cocho',40.00,460.00,420.00,1.00,NULL,'2026-10-19 08:35:20','2026-10-19 11:35:20','2026-10-19 11:35:20'),(26,30,27,20,37,174,'saida','abastecimento_cocho',40.00,420.00,380.00,1.00,NULL,'2026-10-19 08:35:20','2026-10-19 11:35:20','2026-10-19 11:35:20'),(27,30,27,20,37,175,'saida','abastecimento_cocho',40.00,380.00,340.00,1.00,NULL,'2026-10-19 08:35:20','2026-10-19 11:35:20','2026-10-19 11:35:20'),(28,30,27,20,37,176,'saida','abastecimento_cocho',40.00,340.00,300.00,1.00,NULL,'2026-10-19 08:35:20','2026-10-19 11:35:20','2026-10-19 11:35:20'),(29,30,27,20,37,177,'saida','abastecimento_cocho',40.00,300.00,260.00,1.00,NULL,'2026-10-19 08:35:20','2026-10-19 11:35:20','2026-10-19 11:35:20'),(30,30,27,20,37,178,'saida','abastecimento_cocho',40.00,260.00,220.00,1.00,NULL,'2026-10-19 08:35:20','2026-10-19 11:35:20','2026-10-19 11:35:20'),(31,30,27,20,37,179,'saida','abastecimento_cocho',40.00,220.00,180.00,1.00,NULL,'2026-10-19 08:35:20','2026-10-19 11:35:20','2026-10-19 11:35:20'),(32,30,27,20,37,180,'saida','abastecimento_cocho',40.00,180.00,140.00,1.00,NULL,'2026-10-19 08:35:20','2026-10-19 11:35:20','2026-10-19 11:35:20'),(33,30,27,20,37,181,'saida','abastecimento_cocho',40.00,140.00,100.00,1.00,NULL,'2026-10-19 08:35:20','2026-10-19 11:35:20','2026-10-19 11:35:20'),(34,30,26,20,NULL,NULL,'ajuste','edicao_racao',2530.06,469.94,3000.00,1.50,'Estoque alterado na edição da ração.','2026-10-19 11:51:45','2026-10-19 14:51:45','2026-10-19 14:51:45'),(35,30,27,20,NULL,NULL,'ajuste','edicao_racao',2900.00,100.00,3000.00,1.00,'Estoque alterado na edição da ração.','2026-10-19 11:52:06','2026-10-19 14:52:06','2026-10-19 14:52:06'),(36,30,28,20,NULL,NULL,'entrada','cadastro_racao',2000.00,0.00,2000.00,3.00,'Estoque inicial no cadastro da ração.','2026-10-19 13:25:23','2026-10-19 16:25:23','2026-10-19 16:25:23'),(37,30,29,20,NULL,NULL,'entrada','cadastro_racao',4000.00,0.00,4000.00,1.20,'Estoque inicial no cadastro da ração.','2026-10-19 13:34:38','2026-10-19 16:34:38','2026-10-19 16:34:38'),(38,5,4,NULL,NULL,NULL,'entrada','cadastro_racao',500.00,0.00,500.00,90.00,'Saldo existente antes do histórico de estoque.','2026-08-25 08:16:17','2026-10-20 11:46:16','2026-10-20 11:46:16'),(39,11,7,NULL,NULL,NULL,'entrada','cadastro_racao',500.00,0.00,500.00,3.00,'Saldo existente antes do histórico de estoque.','2026-08-25 11:09:19','2026-10-20 11:46:16','2026-10-20 11:46:16'),(40,11,8,NULL,NULL,NULL,'entrada','cadastro_racao',222.00,0.00,222.00,222.00,'Saldo existente antes do histórico de estoque.','2026-08-25 11:52:34','2026-10-20 11:46:16','2026-10-20 11:46:16'),(41,18,14,NULL,NULL,NULL,'entrada','cadastro_racao',500.00,0.00,500.00,2.40,'Saldo existente antes do histórico de estoque.','2026-09-29 07:41:17','2026-10-20 11:46:16','2026-10-20 11:46:16'),(42,18,15,NULL,NULL,NULL,'entrada','cadastro_racao',500.00,0.00,500.00,2.40,'Saldo existente antes do histórico de estoque.','2026-09-29 07:41:18','2026-10-20 11:46:16','2026-10-20 11:46:16'),(43,18,16,NULL,NULL,NULL,'entrada','cadastro_racao',500.00,0.00,500.00,2.40,'Saldo existente antes do histórico de estoque.','2026-09-29 07:41:19','2026-10-20 11:46:16','2026-10-20 11:46:16'),(44,18,17,NULL,NULL,NULL,'entrada','cadastro_racao',500.00,0.00,500.00,2.40,'Saldo existente antes do histórico de estoque.','2026-09-29 07:41:19','2026-10-20 11:46:16','2026-10-20 11:46:16'),(45,18,18,NULL,NULL,NULL,'entrada','cadastro_racao',500.00,0.00,500.00,2.40,'Saldo existente antes do histórico de estoque.','2026-09-29 07:41:20','2026-10-20 11:46:16','2026-10-20 11:46:16'),(46,18,19,NULL,NULL,NULL,'entrada','cadastro_racao',500.00,0.00,500.00,2.40,'Saldo existente antes do histórico de estoque.','2026-09-29 07:41:21','2026-10-20 11:46:16','2026-10-20 11:46:16'),(47,21,20,NULL,NULL,NULL,'entrada','cadastro_racao',180.00,0.00,180.00,6.00,'Saldo existente antes do histórico de estoque.','2026-09-29 08:09:20','2026-10-20 11:46:16','2026-10-20 11:46:16'),(48,25,21,NULL,NULL,NULL,'entrada','cadastro_racao',200.00,0.00,200.00,2.30,'Saldo existente antes do histórico de estoque.','2026-09-29 08:14:42','2026-10-20 11:46:16','2026-10-20 11:46:16'),(49,23,22,NULL,NULL,NULL,'entrada','cadastro_racao',130.00,0.00,130.00,5.00,'Saldo existente antes do histórico de estoque.','2026-09-29 08:18:57','2026-10-20 11:46:16','2026-10-20 11:46:16'),(60,36,34,25,NULL,NULL,'entrada','manual',1800.00,0.00,1800.00,2.80,'Compra mensal','2026-10-20 13:32:58','2026-10-20 16:32:58','2026-10-20 16:32:58'),(61,36,34,25,48,222,'saida','abastecimento_cocho',140.00,1800.00,1660.00,2.80,NULL,'2026-10-20 13:33:00','2026-10-20 16:33:00','2026-10-20 16:33:00'),(62,36,34,25,48,223,'saida','abastecimento_cocho',140.00,1660.00,1520.00,2.80,NULL,'2026-10-20 13:33:00','2026-10-20 16:33:00','2026-10-20 16:33:00'),(63,36,34,25,48,224,'saida','abastecimento_cocho',140.00,1520.00,1380.00,2.80,NULL,'2026-10-20 13:33:00','2026-10-20 16:33:00','2026-10-20 16:33:00'),(64,36,35,25,NULL,NULL,'entrada','manual',900.00,0.00,900.00,2.10,'Compra mensal','2026-10-20 13:33:01','2026-10-20 16:33:01','2026-10-20 16:33:01'),(65,36,35,25,49,225,'saida','abastecimento_cocho',90.00,900.00,810.00,2.10,NULL,'2026-10-20 13:33:03','2026-10-20 16:33:03','2026-10-20 16:33:03'),(66,36,35,25,49,226,'saida','abastecimento_cocho',90.00,810.00,720.00,2.10,NULL,'2026-10-20 13:33:03','2026-10-20 16:33:03','2026-10-20 16:33:03'),(67,36,36,25,NULL,NULL,'entrada','manual',120.00,0.00,120.00,1.90,'Compra mensal','2026-10-20 13:33:04','2026-10-20 16:33:04','2026-10-20 16:33:04'),(68,36,36,25,50,227,'saida','abastecimento_cocho',45.00,120.00,75.00,1.90,NULL,'2026-10-20 13:33:06','2026-10-20 16:33:06','2026-10-20 16:33:06'),(69,36,36,25,50,228,'saida','abastecimento_cocho',45.00,75.00,30.00,1.90,NULL,'2026-10-20 13:33:06','2026-10-20 16:33:06','2026-10-20 16:33:06');
/*!40000 ALTER TABLE `estoque_movimentacoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
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

--
-- Table structure for table `fazendas`
--

DROP TABLE IF EXISTS `fazendas`;
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
  KEY `fk_fazendas_cadastro_id` (`cadastro_id`),
  CONSTRAINT `fk_fazendas_cadastro_id` FOREIGN KEY (`cadastro_id`) REFERENCES `cadastros` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fazendas`
--

LOCK TABLES `fazendas` WRITE;
/*!40000 ALTER TABLE `fazendas` DISABLE KEYS */;
INSERT INTO `fazendas` VALUES (1,1,'Fazenda Talismã','Bruna Figueira Silva','Tambaú - SP','13710-224','Tambaú','SP',-21.7021054,-47.2652065,70.00,'2026-08-25 10:57:05','2026-08-25 10:57:05'),(2,1,'Fazenda Morandin','Bruna Figueira Silva','Tambaú - SP','13710-224','Tambaú','SP',-21.6931734,-47.2739316,56.00,'2026-08-25 10:57:26','2026-08-25 10:57:26'),(3,1,'Fazenda São João','Bruna Figueira Silva','São João da Boa Vista - SP','13870-020','São João da Boa Vista','SP',-21.9704416,-46.7953369,80.00,'2026-08-25 10:58:04','2026-08-25 10:58:04'),(4,2,'Fazenda Santa Helena','Maria Eduarda Evangelista','Adolfo - SP','35609-00','Adolfo','SP',NULL,NULL,10.00,'2026-08-25 11:02:35','2026-08-25 11:02:35'),(5,3,'Fazenda del gado','Yasmin','Tambaú - SP','13710-083','Tambaú','SP',NULL,NULL,10.00,'2026-08-25 11:13:51','2026-08-25 11:13:51'),(8,4,'ARTHUR BARBOSA AZEVEDO','ARTHUR BARBOSA AZEVEDO','Araruna - PB','92480-00','Araruna','PB',NULL,NULL,1.29,'2026-08-25 13:51:26','2026-08-25 13:51:26'),(9,5,'São José','Lorena','São José do Rio Pardo - SP',NULL,'São José do Rio Pardo','SP',NULL,NULL,1.01,'2026-08-25 13:51:56','2026-08-25 13:51:56'),(10,7,'Fazenda São Miguel','Maria Fernanda Galdino de Souza Felipe','Tambaú - SP','13710-000','Tambaú','SP',NULL,NULL,12.06,'2026-08-25 13:58:34','2026-08-25 13:58:34'),(11,6,'FAZENDA SENAI','Arthur Povidelo','Tambaú - SP','13710-290','Tambaú','SP',-21.7126435,-47.2442523,210003.00,'2026-08-25 14:05:00','2026-08-25 14:05:00'),(12,8,'Fazenda Talismã','Bruna Figueira Silva','Tambaú - SP','13711-244','Tambaú','SP',-21.7105437,-47.2748834,56.00,'2026-08-31 13:09:14','2026-08-31 13:09:14'),(18,14,'bexe','JOÃO EMANOEL PORFIRIO DE SOUZA','Santo Antônio de Jesus - BA','44444-444','Santo Antônio de Jesus','BA',-12.9555997,-39.2609139,10.00,'2026-09-29 10:36:22','2026-09-29 10:36:22'),(21,13,'Fazenda Santa Helena','Maria Eduarda Evangelista','Areias - SP','12820-000','Areias','SP',NULL,NULL,10.00,'2026-09-29 10:47:43','2026-09-29 10:52:18'),(22,13,'Fazenda Vila Nova','Maria Eduarda Evangelista','Areado - MG','37140-000','Areado','MG',NULL,NULL,20.00,'2026-09-29 10:49:06','2026-09-29 10:51:38'),(23,13,'Fazenda São João','Maria Eduarda Evangelista','Areial - PB','58140-000','Areial','PB',NULL,NULL,30.00,'2026-09-29 10:49:48','2026-09-29 10:52:52'),(24,15,'zazafabu','zaz','Tambaú - SP','13711-412','Tambaú','SP',NULL,NULL,34444.00,'2026-09-29 10:55:26','2026-09-29 10:55:26'),(25,16,'Fazenda del gado','Yasmin','Tambaú - SP','13710-083','Tambaú','SP',NULL,NULL,10.00,'2026-09-29 11:10:39','2026-09-29 11:10:39'),(26,16,'Fazenda del gado','Yasmin','Tambaú - SP','13710-083','Tambaú','SP',NULL,NULL,10.00,'2026-09-29 11:27:48','2026-09-29 11:27:48'),(29,19,'JOÃO EMANOEL PORFIRIO DE SOUZA','Murilo','Santo Antônio de Jesus - BA','44444-444','Santo Antônio de Jesus','BA',NULL,NULL,9999999.00,'2026-09-29 14:38:32','2026-09-29 14:38:32'),(30,20,'Fazenda Talismã','Bruna Figueira Silva','Jussara - GO','76270-000','Jussara','GO',-15.9393599,-50.1322387,230.00,'2026-10-19 11:19:38','2026-10-19 14:10:05'),(31,20,'Fazenda Pirassununga','Bruna Figueira Silva','Pirassununga - SP','13631-904','Pirassununga','SP',-22.0073139,-47.4447773,120.00,'2026-10-19 11:31:43','2026-10-19 14:10:28'),(36,25,'Fazenda Boa Vista','Bruna Demonstração','Uberlândia - MG','38400-000','Uberlândia','MG',NULL,NULL,250.00,'2026-10-20 16:32:50','2026-10-20 16:32:50'),(37,25,'Sítio Santa Luzia','Bruna Demonstração','Campinas - SP',NULL,'Campinas','SP',NULL,NULL,40.00,'2026-10-20 16:32:51','2026-10-20 16:32:51');
/*!40000 ALTER TABLE `fazendas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
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

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
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

--
-- Table structure for table `leituras_iot`
--

DROP TABLE IF EXISTS `leituras_iot`;
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
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leituras_iot`
--

LOCK TABLES `leituras_iot` WRITE;
/*!40000 ALTER TABLE `leituras_iot` DISABLE KEYS */;
INSERT INTO `leituras_iot` VALUES (1,20,1,'balanca',NULL,190.00,NULL,NULL,95.00,190.00,-10.00,'2026-10-05 07:30:54','2026-10-06 16:32:54','2026-10-06 16:32:54'),(2,20,2,'distancia',12.50,NULL,NULL,NULL,95.00,NULL,NULL,'2026-10-05 07:30:59','2026-10-06 16:32:54','2026-10-06 16:32:54'),(3,20,1,'balanca',NULL,182.40,NULL,NULL,91.20,182.40,-7.60,'2026-10-05 09:30:54','2026-10-06 16:32:54','2026-10-06 16:32:54'),(4,20,2,'distancia',14.40,NULL,NULL,NULL,91.20,NULL,NULL,'2026-10-05 09:30:59','2026-10-06 16:32:54','2026-10-06 16:32:54'),(5,20,1,'balanca',NULL,175.10,NULL,NULL,87.55,175.10,-7.30,'2026-10-05 11:30:54','2026-10-06 16:32:54','2026-10-06 16:32:54'),(6,20,2,'distancia',16.23,NULL,NULL,NULL,87.54,NULL,NULL,'2026-10-05 11:30:59','2026-10-06 16:32:54','2026-10-06 16:32:54'),(7,20,1,'balanca',NULL,166.80,NULL,NULL,83.40,166.80,-8.30,'2026-10-05 13:30:54','2026-10-06 16:32:54','2026-10-06 16:32:54'),(8,20,2,'distancia',18.30,NULL,NULL,NULL,83.40,NULL,NULL,'2026-10-05 13:30:59','2026-10-06 16:32:54','2026-10-06 16:32:54'),(9,20,1,'balanca',NULL,158.90,NULL,NULL,79.45,158.90,-7.90,'2026-10-05 15:30:54','2026-10-06 16:32:54','2026-10-06 16:32:54'),(10,20,2,'distancia',20.28,NULL,NULL,NULL,79.44,NULL,NULL,'2026-10-05 15:30:59','2026-10-06 16:32:54','2026-10-06 16:32:54'),(11,20,1,'balanca',NULL,151.30,NULL,NULL,75.65,151.30,-7.60,'2026-10-05 17:30:54','2026-10-06 16:32:54','2026-10-06 16:32:54'),(12,20,2,'distancia',22.18,NULL,NULL,NULL,75.64,NULL,NULL,'2026-10-05 17:30:59','2026-10-06 16:32:54','2026-10-06 16:32:54'),(13,20,1,'balanca',NULL,143.70,NULL,NULL,71.85,143.70,-7.60,'2026-10-05 19:30:54','2026-10-06 16:32:54','2026-10-06 16:32:54'),(14,20,2,'distancia',24.08,NULL,NULL,NULL,71.84,NULL,NULL,'2026-10-05 19:30:59','2026-10-06 16:32:54','2026-10-06 16:32:54'),(15,20,1,'balanca',NULL,135.20,NULL,NULL,67.60,135.20,-8.50,'2026-10-05 21:30:54','2026-10-06 16:32:54','2026-10-06 16:32:54'),(16,20,2,'distancia',26.20,NULL,NULL,NULL,67.60,NULL,NULL,'2026-10-05 21:30:59','2026-10-06 16:32:54','2026-10-06 16:32:54'),(17,20,1,'balanca',NULL,196.50,NULL,NULL,98.25,196.50,61.30,'2026-10-05 23:30:54','2026-10-06 16:32:54','2026-10-06 16:32:54'),(18,20,2,'distancia',10.88,NULL,NULL,NULL,98.24,NULL,NULL,'2026-10-05 23:30:59','2026-10-06 16:32:54','2026-10-06 16:32:54'),(19,20,1,'balanca',NULL,188.10,NULL,NULL,94.05,188.10,-8.40,'2026-10-06 01:30:54','2026-10-06 16:32:54','2026-10-06 16:32:54'),(20,20,2,'distancia',12.98,NULL,NULL,NULL,94.04,NULL,NULL,'2026-10-06 01:30:59','2026-10-06 16:32:54','2026-10-06 16:32:54'),(21,20,1,'balanca',NULL,179.60,NULL,NULL,89.80,179.60,-8.50,'2026-10-06 03:30:54','2026-10-06 16:32:54','2026-10-06 16:32:54'),(22,20,2,'distancia',15.10,NULL,NULL,NULL,89.80,NULL,NULL,'2026-10-06 03:30:59','2026-10-06 16:32:54','2026-10-06 16:32:54'),(23,20,1,'balanca',NULL,171.00,NULL,NULL,85.50,171.00,-8.60,'2026-10-06 05:30:54','2026-10-06 16:32:54','2026-10-06 16:32:54'),(24,20,2,'distancia',17.25,NULL,NULL,NULL,85.50,NULL,NULL,'2026-10-06 05:30:59','2026-10-06 16:32:54','2026-10-06 16:32:54'),(25,20,1,'balanca',NULL,162.20,NULL,NULL,81.10,162.20,-8.80,'2026-10-06 07:30:54','2026-10-06 16:32:54','2026-10-06 16:32:54'),(26,20,2,'distancia',19.45,NULL,NULL,NULL,81.10,NULL,NULL,'2026-10-06 07:30:59','2026-10-06 16:32:54','2026-10-06 16:32:54'),(27,20,1,'balanca',NULL,150.80,NULL,NULL,75.40,150.80,-11.40,'2026-10-06 09:30:54','2026-10-06 16:32:54','2026-10-06 16:32:54'),(28,20,2,'distancia',22.30,NULL,NULL,NULL,75.40,NULL,NULL,'2026-10-06 09:30:59','2026-10-06 16:32:54','2026-10-06 16:32:54'),(29,20,1,'balanca',NULL,138.90,NULL,NULL,69.45,138.90,-11.90,'2026-10-06 11:30:54','2026-10-06 16:32:54','2026-10-06 16:32:54'),(30,20,2,'distancia',25.28,NULL,NULL,NULL,69.44,NULL,NULL,'2026-10-06 11:30:59','2026-10-06 16:32:54','2026-10-06 16:32:54'),(31,20,1,'balanca',NULL,123.45,NULL,NULL,61.73,123.45,-15.45,'2026-10-06 13:30:54','2026-10-06 16:32:54','2026-10-06 16:32:54'),(32,20,2,'distancia',29.14,NULL,NULL,NULL,61.72,NULL,NULL,'2026-10-06 13:30:59','2026-10-06 16:32:54','2026-10-06 16:32:54'),(33,154,4,'balanca',NULL,4.25,NULL,NULL,2.13,4.25,-97.42,'2026-10-20 09:38:49','2026-10-20 12:38:49','2026-10-20 12:38:49'),(34,154,4,'balanca',NULL,10.25,NULL,NULL,5.13,10.25,6.00,'2026-10-20 09:41:36','2026-10-20 12:41:36','2026-10-20 12:41:36'),(35,154,3,'distancia',0.01,NULL,NULL,NULL,100.00,NULL,NULL,'2026-10-20 09:43:39','2026-10-20 12:43:39','2026-10-20 12:43:39'),(36,154,3,'distancia',5.00,NULL,NULL,NULL,100.00,NULL,NULL,'2026-10-20 09:44:28','2026-10-20 12:44:28','2026-10-20 12:44:28'),(37,154,3,'distancia',6.50,NULL,NULL,NULL,96.67,NULL,NULL,'2026-10-20 09:44:47','2026-10-20 12:44:47','2026-10-20 12:44:47'),(38,154,3,'distancia',7.00,NULL,NULL,NULL,95.56,NULL,NULL,'2026-10-20 09:46:04','2026-10-20 12:46:04','2026-10-20 12:46:04'),(39,154,3,'distancia',8.00,NULL,NULL,NULL,93.33,NULL,NULL,'2026-10-20 09:46:09','2026-10-20 12:46:09','2026-10-20 12:46:09'),(40,154,3,'distancia',9.00,NULL,NULL,NULL,91.11,NULL,NULL,'2026-10-20 09:46:12','2026-10-20 12:46:12','2026-10-20 12:46:12'),(41,154,3,'distancia',10.00,NULL,NULL,NULL,88.89,NULL,NULL,'2026-10-20 09:46:18','2026-10-20 12:46:18','2026-10-20 12:46:18'),(42,154,3,'distancia',15.00,NULL,NULL,NULL,77.78,NULL,NULL,'2026-10-20 09:48:22','2026-10-20 12:48:22','2026-10-20 12:48:22'),(43,154,4,'balanca',NULL,20.00,NULL,NULL,10.00,20.00,9.75,'2026-10-20 09:48:50','2026-10-20 12:48:50','2026-10-20 12:48:50'),(44,154,3,'distancia',18.00,NULL,NULL,NULL,71.11,NULL,NULL,'2026-10-20 09:54:09','2026-10-20 12:54:09','2026-10-20 12:54:09'),(45,154,4,'balanca',NULL,23.00,NULL,NULL,11.50,23.00,3.00,'2026-10-20 09:54:14','2026-10-20 12:54:14','2026-10-20 12:54:14'),(46,154,3,'distancia',101.70,NULL,NULL,NULL,0.00,NULL,NULL,'2026-10-20 10:16:22','2026-10-20 13:16:22','2026-10-20 13:16:22'),(47,154,4,'balanca',NULL,0.00,NULL,NULL,0.00,0.00,-23.00,'2026-10-20 10:16:24','2026-10-20 13:16:24','2026-10-20 13:16:24'),(48,154,3,'distancia',95.32,NULL,NULL,NULL,0.00,NULL,NULL,'2026-10-20 10:16:27','2026-10-20 13:16:27','2026-10-20 13:16:27'),(49,154,4,'balanca',NULL,0.00,NULL,NULL,0.00,0.00,0.00,'2026-10-20 10:16:29','2026-10-20 13:16:29','2026-10-20 13:16:29'),(50,154,3,'distancia',93.78,NULL,NULL,NULL,0.00,NULL,NULL,'2026-10-20 10:16:33','2026-10-20 13:16:33','2026-10-20 13:16:33'),(51,154,4,'balanca',NULL,0.00,NULL,NULL,0.00,0.00,0.00,'2026-10-20 10:16:34','2026-10-20 13:16:34','2026-10-20 13:16:34'),(52,154,4,'balanca',NULL,0.00,NULL,NULL,0.00,0.00,0.00,'2026-10-20 10:16:38','2026-10-20 13:16:38','2026-10-20 13:16:38'),(53,154,3,'distancia',96.30,NULL,NULL,NULL,0.00,NULL,NULL,'2026-10-20 10:16:45','2026-10-20 13:16:45','2026-10-20 13:16:45'),(54,154,4,'balanca',NULL,0.02,NULL,NULL,0.01,0.02,0.02,'2026-10-20 10:16:46','2026-10-20 13:16:46','2026-10-20 13:16:46'),(55,154,3,'distancia',17.39,NULL,NULL,NULL,72.47,NULL,NULL,'2026-10-20 10:16:50','2026-10-20 13:16:50','2026-10-20 13:16:50'),(56,154,4,'balanca',NULL,0.02,NULL,NULL,0.01,0.02,0.00,'2026-10-20 10:16:52','2026-10-20 13:16:52','2026-10-20 13:16:52'),(57,154,3,'distancia',137.82,NULL,NULL,NULL,0.00,NULL,NULL,'2026-10-20 10:16:55','2026-10-20 13:16:55','2026-10-20 13:16:55'),(58,154,4,'balanca',NULL,0.02,NULL,NULL,0.01,0.02,0.00,'2026-10-20 10:16:57','2026-10-20 13:16:57','2026-10-20 13:16:57'),(59,154,3,'distancia',21.64,NULL,NULL,NULL,63.02,NULL,NULL,'2026-10-20 10:17:01','2026-10-20 13:17:01','2026-10-20 13:17:01'),(60,154,4,'balanca',NULL,0.02,NULL,NULL,0.01,0.02,0.00,'2026-10-20 10:17:02','2026-10-20 13:17:02','2026-10-20 13:17:02'),(61,154,3,'distancia',21.39,NULL,NULL,NULL,63.58,NULL,NULL,'2026-10-20 10:17:06','2026-10-20 13:17:06','2026-10-20 13:17:06'),(62,154,4,'balanca',NULL,0.02,NULL,NULL,0.01,0.02,0.00,'2026-10-20 10:17:07','2026-10-20 13:17:07','2026-10-20 13:17:07'),(63,154,3,'distancia',19.60,NULL,NULL,NULL,67.56,NULL,NULL,'2026-10-20 10:17:12','2026-10-20 13:17:12','2026-10-20 13:17:12'),(64,154,4,'balanca',NULL,0.02,NULL,NULL,0.01,0.02,0.00,'2026-10-20 10:17:14','2026-10-20 13:17:14','2026-10-20 13:17:14'),(65,154,3,'distancia',97.14,NULL,NULL,NULL,0.00,NULL,NULL,'2026-10-20 10:17:18','2026-10-20 13:17:18','2026-10-20 13:17:18'),(66,154,4,'balanca',NULL,0.00,NULL,NULL,0.00,0.00,-0.02,'2026-10-20 10:17:20','2026-10-20 13:17:20','2026-10-20 13:17:20'),(67,154,3,'distancia',80.67,NULL,NULL,NULL,0.00,NULL,NULL,'2026-10-20 10:17:24','2026-10-20 13:17:24','2026-10-20 13:17:24'),(68,154,4,'balanca',NULL,0.40,NULL,NULL,0.20,0.40,0.40,'2026-10-20 10:17:25','2026-10-20 13:17:25','2026-10-20 13:17:25'),(69,154,3,'distancia',8.66,NULL,NULL,NULL,91.87,NULL,NULL,'2026-10-20 10:17:29','2026-10-20 13:17:29','2026-10-20 13:17:29'),(70,154,4,'balanca',NULL,0.31,NULL,NULL,0.16,0.31,-0.09,'2026-10-20 10:17:31','2026-10-20 13:17:31','2026-10-20 13:17:31'),(71,154,3,'distancia',103.89,NULL,NULL,NULL,0.00,NULL,NULL,'2026-10-20 10:19:44','2026-10-20 13:19:44','2026-10-20 13:19:44'),(72,154,4,'balanca',NULL,0.00,NULL,NULL,0.00,0.00,-0.31,'2026-10-20 10:19:47','2026-10-20 13:19:47','2026-10-20 13:19:47'),(73,154,3,'distancia',48.65,NULL,NULL,NULL,3.00,NULL,NULL,'2026-10-20 10:19:53','2026-10-20 13:19:53','2026-10-20 13:19:53'),(74,154,4,'balanca',NULL,1.01,NULL,NULL,0.51,1.01,1.01,'2026-10-20 10:20:00','2026-10-20 13:20:00','2026-10-20 13:20:00'),(75,154,3,'distancia',53.97,NULL,NULL,NULL,0.00,NULL,NULL,'2026-10-20 10:20:02','2026-10-20 13:20:02','2026-10-20 13:20:02'),(76,154,4,'balanca',NULL,1.00,NULL,NULL,0.50,1.00,-0.01,'2026-10-20 10:20:04','2026-10-20 13:20:04','2026-10-20 13:20:04'),(77,154,3,'distancia',104.19,NULL,NULL,NULL,0.00,NULL,NULL,'2026-10-20 10:20:14','2026-10-20 13:20:14','2026-10-20 13:20:14'),(78,154,4,'balanca',NULL,0.99,NULL,NULL,0.50,0.99,-0.01,'2026-10-20 10:20:18','2026-10-20 13:20:18','2026-10-20 13:20:18'),(79,154,3,'distancia',92.23,NULL,NULL,NULL,0.00,NULL,NULL,'2026-10-20 10:20:20','2026-10-20 13:20:20','2026-10-20 13:20:20'),(80,154,4,'balanca',NULL,1.00,NULL,NULL,0.50,1.00,0.01,'2026-10-20 10:20:22','2026-10-20 13:20:22','2026-10-20 13:20:22'),(81,154,3,'distancia',13.09,NULL,NULL,NULL,82.02,NULL,NULL,'2026-10-20 10:20:32','2026-10-20 13:20:32','2026-10-20 13:20:32'),(82,154,4,'balanca',NULL,1.01,NULL,NULL,0.51,1.01,0.01,'2026-10-20 10:20:38','2026-10-20 13:20:38','2026-10-20 13:20:38'),(83,154,3,'distancia',19.41,NULL,NULL,NULL,67.98,NULL,NULL,'2026-10-20 10:20:40','2026-10-20 13:20:40','2026-10-20 13:20:40'),(84,154,4,'balanca',NULL,1.00,NULL,NULL,0.50,1.00,-0.01,'2026-10-20 10:20:44','2026-10-20 13:20:44','2026-10-20 13:20:44'),(85,154,3,'distancia',15.26,NULL,NULL,NULL,77.20,NULL,NULL,'2026-10-20 10:20:48','2026-10-20 13:20:48','2026-10-20 13:20:48'),(86,154,4,'balanca',NULL,1.00,NULL,NULL,0.50,1.00,0.00,'2026-10-20 10:20:52','2026-10-20 13:20:52','2026-10-20 13:20:52'),(87,154,3,'distancia',99.11,NULL,NULL,NULL,0.00,NULL,NULL,'2026-10-20 10:20:56','2026-10-20 13:20:56','2026-10-20 13:20:56'),(88,154,4,'balanca',NULL,0.99,NULL,NULL,0.50,0.99,-0.01,'2026-10-20 10:21:08','2026-10-20 13:21:08','2026-10-20 13:21:08'),(89,154,3,'distancia',57.93,NULL,NULL,NULL,0.00,NULL,NULL,'2026-10-20 10:21:12','2026-10-20 13:21:12','2026-10-20 13:21:12'),(90,154,4,'balanca',NULL,2.73,NULL,NULL,1.37,2.73,1.74,'2026-10-20 10:21:18','2026-10-20 13:21:18','2026-10-20 13:21:18'),(91,154,3,'distancia',51.06,NULL,NULL,NULL,0.00,NULL,NULL,'2026-10-20 10:21:24','2026-10-20 13:21:24','2026-10-20 13:21:24'),(92,154,4,'balanca',NULL,2.74,NULL,NULL,1.37,2.74,0.01,'2026-10-20 10:21:28','2026-10-20 13:21:28','2026-10-20 13:21:28'),(93,154,3,'distancia',97.77,NULL,NULL,NULL,0.00,NULL,NULL,'2026-10-20 10:21:34','2026-10-20 13:21:34','2026-10-20 13:21:34'),(94,154,4,'balanca',NULL,2.74,NULL,NULL,1.37,2.74,0.00,'2026-10-20 10:21:38','2026-10-20 13:21:38','2026-10-20 13:21:38'),(95,154,3,'distancia',90.88,NULL,NULL,NULL,0.00,NULL,NULL,'2026-10-20 10:21:44','2026-10-20 13:21:44','2026-10-20 13:21:44'),(96,154,4,'balanca',NULL,20.00,NULL,NULL,10.00,20.00,17.26,'2026-10-20 10:21:46','2026-10-20 13:21:46','2026-10-20 13:21:46');
/*!40000 ALTER TABLE `leituras_iot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lote_consumo_snapshots`
--

DROP TABLE IF EXISTS `lote_consumo_snapshots`;
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
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lote_consumo_snapshots`
--

LOCK TABLES `lote_consumo_snapshots` WRITE;
/*!40000 ALTER TABLE `lote_consumo_snapshots` DISABLE KEYS */;
INSERT INTO `lote_consumo_snapshots` VALUES (7,9,NULL,'2026-08-25',65,0,'status-warning','2026-08-25 13:55:51','2026-08-25 13:55:51'),(8,11,7,'2026-08-25',899,30,'status-success','2026-08-25 14:10:04','2026-08-25 14:10:04'),(9,10,NULL,'2026-08-25',100,0,'status-warning','2026-08-25 14:39:08','2026-08-25 14:39:08'),(10,12,NULL,'2026-08-31',600,0,'status-warning','2026-08-31 13:18:09','2026-08-31 13:18:09'),(11,13,NULL,'2026-08-31',300,0,'status-warning','2026-08-31 13:18:10','2026-08-31 13:18:10'),(70,23,NULL,'2026-09-22',40,11,'status-warning','2026-09-22 14:11:16','2026-09-22 14:11:16'),(71,24,NULL,'2026-09-22',10,13,'status-warning','2026-09-22 14:11:16','2026-09-22 14:11:16'),(72,25,NULL,'2026-09-22',100,5,'status-warning','2026-09-22 14:11:16','2026-09-22 14:11:16'),(73,26,NULL,'2026-09-22',20,0,'status-warning','2026-09-22 14:33:22','2026-09-22 14:33:22'),(74,23,NULL,'2026-09-28',40,11,'status-warning','2026-09-28 10:42:32','2026-09-28 10:42:32'),(75,24,NULL,'2026-09-28',10,13,'status-warning','2026-09-28 10:42:32','2026-09-28 10:42:32'),(76,25,NULL,'2026-09-28',100,5,'status-warning','2026-09-28 10:42:32','2026-09-28 10:42:32'),(77,26,NULL,'2026-09-28',20,0,'status-warning','2026-09-28 10:42:32','2026-09-28 10:42:32'),(78,27,NULL,'2026-09-28',100,15,'status-warning','2026-09-28 10:46:50','2026-09-28 10:46:50'),(79,23,NULL,'2026-09-29',40,11,'status-warning','2026-09-29 10:22:43','2026-09-29 10:22:43'),(80,24,NULL,'2026-09-29',10,13,'status-warning','2026-09-29 10:22:43','2026-09-29 10:22:43'),(81,25,NULL,'2026-09-29',100,5,'status-warning','2026-09-29 10:22:43','2026-09-29 10:22:43'),(82,26,NULL,'2026-09-29',20,0,'status-warning','2026-09-29 10:22:43','2026-09-29 10:22:43'),(83,27,NULL,'2026-09-29',99,15,'status-warning','2026-09-29 10:22:43','2026-09-29 18:06:12'),(84,28,19,'2026-09-29',300,20,'status-success','2026-09-29 10:41:52','2026-09-29 10:41:52'),(85,29,20,'2026-09-29',50,30,'status-success','2026-09-29 11:05:15','2026-09-29 11:12:31'),(86,31,NULL,'2026-09-29',79,0,'status-warning','2026-09-29 11:05:15','2026-09-29 11:05:15'),(87,30,22,'2026-09-29',30,50,'status-success','2026-09-29 11:05:15','2026-09-29 11:19:17'),(88,33,21,'2026-09-29',200,30,'status-success','2026-09-29 11:14:56','2026-09-29 11:14:56'),(89,23,NULL,'2026-10-05',40,11,'status-success','2026-10-05 11:21:29','2026-10-05 12:59:31'),(90,24,NULL,'2026-10-05',10,13,'status-warning','2026-10-05 11:21:29','2026-10-05 11:21:29'),(91,25,NULL,'2026-10-05',100,5,'status-warning','2026-10-05 11:21:29','2026-10-05 11:21:29'),(92,26,NULL,'2026-10-05',20,0,'status-warning','2026-10-05 11:21:29','2026-10-05 11:21:29'),(93,27,NULL,'2026-10-05',99,15,'status-success','2026-10-05 11:21:29','2026-10-05 13:00:11'),(94,23,NULL,'2026-10-06',40,25,'status-warning','2026-10-06 10:40:11','2026-10-06 12:40:27'),(95,24,NULL,'2026-10-06',10,0,'status-warning','2026-10-06 10:40:11','2026-10-06 12:40:27'),(96,25,NULL,'2026-10-06',100,0,'status-warning','2026-10-06 10:40:11','2026-10-06 12:40:27'),(97,26,NULL,'2026-10-06',20,0,'status-warning','2026-10-06 10:40:11','2026-10-06 10:40:11'),(98,27,NULL,'2026-10-06',99,25,'status-warning','2026-10-06 10:40:11','2026-10-06 12:40:27'),(99,35,NULL,'2026-10-06',30,0,'status-warning','2026-10-06 14:22:40','2026-10-06 14:22:40'),(100,36,27,'2026-10-19',80,810,'status-success','2026-10-19 11:29:27','2026-10-19 16:35:11'),(101,37,26,'2026-10-19',120,850,'status-success','2026-10-19 13:55:36','2026-10-19 16:33:03'),(102,38,28,'2026-10-19',10,70,'status-success','2026-10-19 16:22:22','2026-10-19 16:30:52'),(103,39,NULL,'2026-10-19',80,0,'status-warning','2026-10-19 16:22:22','2026-10-19 16:22:22'),(104,40,NULL,'2026-10-19',40,0,'status-warning','2026-10-19 16:22:22','2026-10-19 16:22:22'),(105,41,29,'2026-10-19',40,490,'status-success','2026-10-19 16:22:22','2026-10-19 16:40:37'),(106,36,27,'2026-10-20',80,810,'status-success','2026-10-20 11:47:11','2026-10-20 11:47:11'),(107,37,26,'2026-10-20',120,850,'status-success','2026-10-20 11:47:11','2026-10-20 11:47:11'),(108,38,28,'2026-10-20',10,70,'status-success','2026-10-20 11:47:11','2026-10-20 11:47:11'),(109,39,NULL,'2026-10-20',80,0,'status-warning','2026-10-20 11:47:11','2026-10-20 11:47:11'),(110,40,NULL,'2026-10-20',40,0,'status-warning','2026-10-20 11:47:11','2026-10-20 11:47:11'),(111,41,29,'2026-10-20',40,490,'status-success','2026-10-20 11:47:11','2026-10-20 11:47:11'),(118,48,34,'2026-10-20',24,12,'status-success','2026-10-20 16:33:43','2026-10-20 16:33:43'),(119,49,35,'2026-10-20',39,8,'status-success','2026-10-20 16:33:43','2026-10-20 16:42:34'),(120,50,36,'2026-10-20',300,3,'status-success','2026-10-20 16:33:43','2026-10-20 16:33:43');
/*!40000 ALTER TABLE `lote_consumo_snapshots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lote_ocorrencias`
--

DROP TABLE IF EXISTS `lote_ocorrencias`;
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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lote_ocorrencias`
--

LOCK TABLES `lote_ocorrencias` WRITE;
/*!40000 ALTER TABLE `lote_ocorrencias` DISABLE KEYS */;
INSERT INTO `lote_ocorrencias` VALUES (1,9,'morte','Predador',NULL,5,70,65,NULL,'2026-08-25',NULL,'2026-08-25 13:54:58','2026-08-25 13:54:58'),(11,31,'doenca',NULL,'Gripe Aviária',1,80,79,NULL,'2026-09-29','medicados','2026-09-29 11:02:47','2026-09-29 11:02:47'),(14,27,'morte','Causa natural',NULL,1,100,99,NULL,'2026-09-29',NULL,'2026-09-29 18:04:31','2026-09-29 18:04:31'),(23,49,'morte','Acidente',NULL,1,40,39,NULL,'2026-10-20',NULL,'2026-10-20 16:33:12','2026-10-20 16:33:12');
/*!40000 ALTER TABLE `lote_ocorrencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lotes`
--

DROP TABLE IF EXISTS `lotes`;
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
  KEY `fk_lotes_fazenda_id` (`fazenda_id`),
  KEY `lotes_lote_origem_id_foreign` (`lote_origem_id`),
  KEY `lotes_racao_id_foreign` (`racao_id`),
  KEY `lotes_fazenda_status_index` (`fazenda_id`,`status`),
  CONSTRAINT `fk_lotes_fazenda_id` FOREIGN KEY (`fazenda_id`) REFERENCES `fazendas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lotes_lote_origem_id_foreign` FOREIGN KEY (`lote_origem_id`) REFERENCES `lotes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lotes_racao_id_foreign` FOREIGN KEY (`racao_id`) REFERENCES `racaos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lotes_animais_nao_negativo` CHECK (`quantidade_animais` >= 0),
  CONSTRAINT `lotes_peso_nao_negativo` CHECK (`peso_medio` >= 0)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lotes`
--

LOCK TABLES `lotes` WRITE;
/*!40000 ALTER TABLE `lotes` DISABLE KEYS */;
INSERT INTO `lotes` VALUES (2,4,NULL,NULL,'Lote A5','Bovino',NULL,'Leite','Fêmea','Acima de 3 anos',20,1,600.00,'2026-08-25','Ativo','2026-08-25 11:03:08','2026-08-25 11:03:08'),(8,5,NULL,NULL,'lote 1','Bovino',NULL,'Leite','Fêmea','Acima de 3 anos',200,1,370.00,'2026-08-25','Ativo','2026-08-25 11:14:40','2026-08-25 11:14:40'),(9,8,NULL,NULL,'arthur','Bovino',NULL,'Leite','Fêmea','0-6 meses',65,0,15.00,'2026-08-25','Ativo','2026-08-25 13:53:24','2026-08-25 13:54:58'),(10,9,NULL,NULL,'12','Bovino',NULL,'Reprodução','Misto','1-2 anos',100,1,44.98,'2026-08-25','Ativo','2026-08-25 13:54:33','2026-08-25 13:54:33'),(11,11,8,NULL,'LOTE 1','Bovino',NULL,'Corte','Macho','6-12 meses',899,1,555.00,'2026-08-25','Ativo','2026-08-25 14:06:04','2026-08-25 14:52:34'),(12,12,NULL,NULL,'Lote A1','Bovino',NULL,'Corte','Macho','1-2 anos',600,0,860.00,'2026-08-31','Ativo','2026-08-31 13:13:04','2026-08-31 13:13:04'),(13,12,NULL,NULL,'Lote A2','Bovino',NULL,'Leite','Fêmea','6-12 meses',300,0,690.00,'2026-08-31','Ativo','2026-08-31 13:15:16','2026-08-31 13:15:16'),(23,1,NULL,NULL,'Lote A1','Bovino','Nelore','Leite','Fêmea','1-2 anos',40,10,478.00,'2026-09-22','Ativo','2026-09-22 14:03:13','2026-10-05 12:59:29'),(24,1,NULL,NULL,'Lote B1','Equino','Manga Larga','Esporte','Macho','2-3 anos',10,4,780.00,'2026-09-22','Ativo','2026-09-22 14:06:08','2026-09-22 14:06:08'),(25,1,NULL,NULL,'Lote C1','Avicultura','Leghorn','Ovos','Fêmea','6-12 meses',100,25,3.00,'2026-09-22','Ativo','2026-09-22 14:10:36','2026-09-22 14:10:37'),(26,1,NULL,NULL,'Lote D1','Ovino','Santa Inês','Leite','Fêmea','1-2 anos',20,5,112.00,'2026-09-22','Ativo','2026-09-22 14:16:59','2026-09-22 14:16:59'),(27,1,NULL,NULL,'Lote A2','Bovino','Nelore','Corte','Macho','2-3 anos',99,30,890.00,'2026-09-28','Ativo','2026-09-28 10:44:47','2026-10-05 13:00:09'),(28,18,19,NULL,'a1','Bovino','nle','Reprodução','Fêmea','1-2 anos',300,3,350.00,'2026-09-29','Ativo','2026-09-29 10:38:32','2026-09-29 10:41:21'),(29,21,20,NULL,'Lote A1','Bovino','..','Corte','Macho','0-6 meses',50,10,500.00,'2026-09-29','Ativo','2026-09-29 10:55:15','2026-09-29 11:09:20'),(30,23,22,NULL,'Lote A2','Suíno','..','Reprodução','Fêmea','6-12 meses',30,1,200.00,'2026-09-29','Inativo','2026-09-29 10:57:02','2026-09-29 11:18:57'),(31,22,NULL,NULL,'Lote A3','Avicultura','..','Ovos','Fêmea','6-12 meses',79,12,8.00,'2026-09-29','Ativo','2026-09-29 11:00:46','2026-09-29 11:02:47'),(33,25,21,NULL,'lote 1','Bovino','Nelore','Reprodução','Misto','Acima de 3 anos',200,10,400.00,'2026-09-29','Ativo','2026-09-29 11:12:19','2026-09-29 11:14:42'),(34,26,NULL,NULL,'lote 2','Bovino','Nelore','Leite','Fêmea','2-3 anos',500,16,300.00,'2026-09-29','Ativo','2026-09-29 11:29:48','2026-09-29 11:29:49'),(35,1,NULL,NULL,'Lote F1','Caprino','Holandês','Leite','Fêmea','1-2 anos',30,8,109.00,'2026-10-06','Ativo','2026-10-06 13:50:15','2026-10-06 13:50:15'),(36,30,27,NULL,'Lote A1','Bovino','Nelore','Leite','Fêmea','1-2 anos',80,12,740.00,'2026-10-19','Ativo','2026-10-19 11:22:16','2026-10-19 16:27:29'),(37,30,26,NULL,'Lote A2','Bovino','Nelore','Corte','Macho','2-3 anos',120,24,800.00,'2026-10-19','Ativo','2026-10-19 11:32:52','2026-10-19 16:27:39'),(38,30,28,NULL,'Lote B1','Equino','Mangalarga Machador','Esporte','Macho','2-3 anos',10,2,880.00,'2026-10-19','Ativo','2026-10-19 16:13:08','2026-10-19 16:38:14'),(39,30,NULL,NULL,'Lote C1','Avicultura','Rhode Island Red','Ovos','Fêmea','6-12 meses',80,5,6.00,'2026-10-19','Ativo','2026-10-19 16:15:00','2026-10-19 16:20:06'),(40,30,NULL,NULL,'Lote D1','Caprino','Saanen','Leite','Fêmea','1-2 anos',40,5,90.00,'2026-10-19','Ativo','2026-10-19 16:16:31','2026-10-19 16:16:31'),(41,30,29,NULL,'Lote F1','Suíno','Large White','Corte','Macho','2-3 anos',40,8,380.00,'2026-10-19','Ativo','2026-10-19 16:19:00','2026-10-19 16:36:32'),(48,36,34,NULL,'Vacas Leiteiras','Bovino','Holandês','Leite','Fêmea','2-3 anos',24,3,520.00,'2026-09-30','Ativo','2026-10-20 16:32:52','2026-10-20 16:32:59'),(49,36,35,NULL,'Suínos Engorda','Suíno','Duroc','Corte','Macho','0-6 meses',39,2,85.00,'2026-09-30','Ativo','2026-10-20 16:32:53','2026-10-20 16:33:12'),(50,36,36,NULL,'Galinhas Poedeiras','Avicultura','Embrapa 051','Ovos','Fêmea','6-12 meses',300,2,2.00,'2026-09-30','Ativo','2026-10-20 16:32:54','2026-10-20 16:33:05');
/*!40000 ALTER TABLE `lotes` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER lotes_sexo_com_ordenhas BEFORE UPDATE ON lotes FOR EACH ROW
            BEGIN
                IF NOT (NEW.sexo <=> OLD.sexo)
                    AND (NEW.sexo IS NULL OR NEW.sexo NOT IN ('Fêmea','Misto'))
                    AND EXISTS (SELECT 1 FROM ordenhas WHERE lote_id = OLD.id) THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Lote com ordenhas registradas nao pode deixar de ter femeas.';
                END IF;
            END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026_08_25_110000_create_comentarios_table',1),(2,'2026_08_25_120000_add_nota_to_comentarios_table',2),(3,'2026_08_25_130000_add_categorias_to_comentarios_table',3),(4,'2026_08_25_140000_make_comentarios_publicos',4),(5,'2026_08_25_150000_add_estoque_total_to_racaos_table',5),(16,'2026_09_01_120000_create_racao_estoque_snapshots_table',9),(17,'2026_08_31_150400_create_ordenhas_table',10),(18,'0001_01_01_000000_create_users_table',11),(19,'0001_01_01_000001_create_cache_table',11),(20,'0001_01_01_000002_create_jobs_table',11),(21,'2026_08_04_180125_add_cpf_and_foto_to_cadastros_table',11),(22,'2026_08_04_181346_widen_telefone_on_cadastros_table',11),(23,'2026_08_04_183250_add_data_nascimento_and_seguranca_to_cadastros_table',11),(24,'2026_08_10_142801_add_endereco_to_fazendas_table',11),(25,'2026_08_10_152846_add_coordenadas_e_proprietario_to_fazendas_table',11),(26,'2026_08_10_161818_add_finalidade_sexo_idade_media_to_lotes_table',11),(27,'2026_08_10_181631_add_status_to_lotes_table',11),(28,'2026_08_11_120124_add_fazenda_id_to_racoes_table',11),(29,'2026_08_11_141827_create_lote_ocorrencias_table',11),(30,'2026_08_11_154706_add_doenca_fields_to_lote_ocorrencias_table',11),(31,'2026_08_11_160404_add_lote_origem_id_to_lotes_table',11),(32,'2026_08_11_163135_add_causa_to_lote_ocorrencias_table',11),(33,'2026_08_11_172500_add_valor_kg_to_racaos_table',11),(34,'2026_08_11_175642_add_sexo_idade_to_racaos_table',11),(35,'2026_08_11_180426_add_finalidade_to_racaos_table',11),(36,'2026_08_17_082800_create_codigos_verificacao_table',11),(37,'2026_08_17_102301_create_planos_table',11),(38,'2026_08_24_120000_add_racao_id_to_lotes_table',11),(39,'2026_08_24_130000_create_lote_consumo_snapshots_table',11),(40,'2026_08_25_090000_create_acessibilidade_preferencias_table',11),(41,'2026_08_25_091000_create_desempenho_lotes_table',11),(42,'2026_08_25_102713_create_personal_access_tokens_table',12),(43,'2026_09_08_090000_add_peso_e_quantidade_to_desempenho_lotes_table',12),(44,'2026_09_08_100000_add_email_verificado_em_to_cadastros_table',12),(45,'2026_09_08_110000_add_raca_to_lotes_table',13),(46,'2026_09_08_120000_add_quantidade_cochos_to_lotes_table',14),(47,'2026_09_08_120100_reestruturar_cochos_para_geracao_automatica',14),(48,'2026_08_01_000000_create_tabelas_base_agrifarm',15),(49,'2026_10_05_100000_corrigir_estrutura_agrifarm',15),(50,'2026_10_05_110000_corrigir_tipos_acessibilidade_preferencias',16),(51,'2026_10_05_140000_add_nutricao_e_controle_to_racaos_table',17),(52,'2026_10_06_100000_create_sensores_e_leituras_iot',18),(53,'2026_10_06_150000_espacar_numero_no_nome_dos_cochos',19),(54,'2026_10_06_150100_remover_fornecedor_reposicao_e_data_entrada',19),(55,'2026_10_20_100000_add_presenca_animal_to_leituras_iot_table',20),(56,'2026_10_20_110000_create_cocho_consumos_table',21),(57,'2026_10_20_120000_add_data_entrada_fornecedor_e_localizacao',22),(58,'2026_10_20_130000_restricoes_quantidades_nao_negativas',23),(59,'2026_10_19_120000_restricoes_ordenha_por_sexo_e_turno',24),(60,'2026_10_19_130000_add_fonte_consumo_to_desempenho_lotes_table',24),(61,'2026_10_20_140000_registrar_saldo_inicial_das_racoes_sem_historico',25),(62,'2026_10_20_150000_corrigir_horarios_gravados_em_utc',26);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ordenhas`
--

DROP TABLE IF EXISTS `ordenhas`;
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
  CONSTRAINT `ordenhas_litros_positivos` CHECK (`quantidade_litros` > 0),
  CONSTRAINT `ordenhas_turno_valido` CHECK (`turno` in ('Manhã','Tarde','Noite')),
  CONSTRAINT `ordenhas_turno_horario` CHECK (`turno` = 'Manhã' and `horario` >= '04:00:00' and `horario` < '12:00:00' or `turno` = 'Tarde' and `horario` >= '12:00:00' and `horario` < '18:00:00' or `turno` = 'Noite' and (`horario` >= '18:00:00' or `horario` < '04:00:00'))
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ordenhas`
--

LOCK TABLES `ordenhas` WRITE;
/*!40000 ALTER TABLE `ordenhas` DISABLE KEYS */;
INSERT INTO `ordenhas` VALUES (6,23,'2026-09-29','09:00:00','Manhã',30.00,1,NULL,'2026-09-29 11:04:12','2026-09-29 11:04:12'),(7,34,'2026-09-29','08:00:00','Manhã',120.00,16,'mafalda está machucada','2026-09-29 11:32:12','2026-09-29 11:32:12'),(8,26,'2026-10-06','10:50:00','Manhã',120.50,1,NULL,'2026-10-06 13:50:44','2026-10-06 13:50:44'),(9,23,'2026-10-06','11:00:00','Manhã',105.00,1,NULL,'2026-10-06 13:51:17','2026-10-06 13:51:17'),(12,36,'2026-10-19','13:43:00','Tarde',220.00,20,NULL,'2026-10-19 16:43:57','2026-10-19 16:43:57'),(15,48,'2026-10-20','06:00:00','Manhã',310.00,25,NULL,'2026-10-20 16:33:10','2026-10-20 16:33:10'),(16,48,'2026-10-20','16:00:00','Tarde',285.00,25,NULL,'2026-10-20 16:33:11','2026-10-20 16:33:11');
/*!40000 ALTER TABLE `ordenhas` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER ordenhas_somente_femeas_insert BEFORE INSERT ON ordenhas FOR EACH ROW
            BEGIN
                IF NOT EXISTS (SELECT 1 FROM lotes WHERE id = NEW.lote_id AND sexo IN ('Fêmea','Misto')) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Ordenha so pode ser registrada em lote com femeas (sexo Femea ou Misto).'; END IF;
            END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER ordenhas_somente_femeas_update BEFORE UPDATE ON ordenhas FOR EACH ROW
            BEGIN
                IF NOT EXISTS (SELECT 1 FROM lotes WHERE id = NEW.lote_id AND sexo IN ('Fêmea','Misto')) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Ordenha so pode ser registrada em lote com femeas (sexo Femea ou Misto).'; END IF;
            END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
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
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `planos`
--

DROP TABLE IF EXISTS `planos`;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `planos`
--

LOCK TABLES `planos` WRITE;
/*!40000 ALTER TABLE `planos` DISABLE KEYS */;
INSERT INTO `planos` VALUES (3,36,48,34,0.50,3,'2026-10-20',NULL,NULL,1,'2026-10-20 16:32:59','2026-10-20 16:32:59'),(4,36,49,35,0.20,3,'2026-10-20',NULL,NULL,1,'2026-10-20 16:33:02','2026-10-20 16:33:02'),(5,36,50,36,0.01,3,'2026-10-20',NULL,NULL,1,'2026-10-20 16:33:05','2026-10-20 16:33:05');
/*!40000 ALTER TABLE `planos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `racao_estoque_snapshots`
--

DROP TABLE IF EXISTS `racao_estoque_snapshots`;
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

--
-- Dumping data for table `racao_estoque_snapshots`
--

LOCK TABLES `racao_estoque_snapshots` WRITE;
/*!40000 ALTER TABLE `racao_estoque_snapshots` DISABLE KEYS */;
/*!40000 ALTER TABLE `racao_estoque_snapshots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `racaos`
--

DROP TABLE IF EXISTS `racaos`;
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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `racaos`
--

LOCK TABLES `racaos` WRITE;
/*!40000 ALTER TABLE `racaos` DISABLE KEYS */;
INSERT INTO `racaos` VALUES (4,5,'Bovino','Fêmea','Acima de 3 anos','Leite',NULL,'kg','raçao magnus',500.00,1000.00,25.00,90.00,NULL,'2026-08-25 11:16:17','2026-08-25 11:16:17',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(7,11,'Bovino','Macho','6-12 meses','Corte',NULL,'kg','Ração Premium',500.00,NULL,30.00,3.00,NULL,'2026-08-25 14:09:19','2026-08-25 14:09:19',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(8,11,'Bovino','Macho','6-12 meses','Corte',NULL,'kg','Ração Premium 2',222.00,NULL,22.00,222.00,NULL,'2026-08-25 14:52:34','2026-08-25 14:52:34',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(12,NULL,'Bovino','Fêmea','2-3 anos','Leite',NULL,'kg','R1',100.00,500.00,10.00,2.00,NULL,'2026-09-21 16:05:06','2026-09-21 16:05:06',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(13,NULL,'Bovino','Fêmea','2-3 anos','Leite',NULL,'kg','R1',100.00,500.00,10.00,2.00,NULL,'2026-09-21 16:12:01','2026-09-21 16:12:01',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(14,18,'Bovino','Fêmea','1-2 anos','Reprodução',NULL,'kg','premio',500.00,1000.00,20.00,2.40,NULL,'2026-09-29 10:41:17','2026-09-29 10:41:17',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(15,18,'Bovino','Fêmea','1-2 anos','Reprodução',NULL,'kg','premio',500.00,1000.00,20.00,2.40,NULL,'2026-09-29 10:41:18','2026-09-29 10:41:18',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(16,18,'Bovino','Fêmea','1-2 anos','Reprodução',NULL,'kg','premio',500.00,1000.00,20.00,2.40,NULL,'2026-09-29 10:41:19','2026-09-29 10:41:19',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(17,18,'Bovino','Fêmea','1-2 anos','Reprodução',NULL,'kg','premio',500.00,1000.00,20.00,2.40,NULL,'2026-09-29 10:41:19','2026-09-29 10:41:19',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(18,18,'Bovino','Fêmea','1-2 anos','Reprodução',NULL,'kg','premio',500.00,1000.00,20.00,2.40,NULL,'2026-09-29 10:41:20','2026-09-29 10:41:20',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(19,18,'Bovino','Fêmea','1-2 anos','Reprodução',NULL,'kg','premio',500.00,1000.00,20.00,2.40,NULL,'2026-09-29 10:41:21','2026-09-29 10:41:21',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(20,21,'Bovino','Macho','0-6 meses','Corte',NULL,'kg','Ração Magnus',180.00,400.00,30.00,6.00,NULL,'2026-09-29 11:09:20','2026-09-29 11:38:40',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(21,25,'Bovino','Misto','Acima de 3 anos','Reprodução',NULL,'kg','raçao magnus',200.00,600.00,30.00,2.30,NULL,'2026-09-29 11:14:42','2026-09-29 11:14:42',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(22,23,'Suíno','Fêmea','6-12 meses','Reprodução',NULL,'kg','Ração Premiun',130.00,400.00,50.00,5.00,NULL,'2026-09-29 11:18:57','2026-09-29 11:25:12',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(23,1,'Bovino',NULL,'0-6 meses',NULL,NULL,'kg','Ração Bezerros Inicial',100.00,199.98,25.00,1.50,NULL,'2026-10-06 12:35:20','2026-10-06 12:35:20',20.00,3.00,8.00,NULL,NULL,'ativa','Fase inicial (0 a 6 meses): fácil digestão para o desenvolvimento do rúmen.'),(26,30,'Bovino','Macho','2-3 anos','Corte',NULL,'kg','Ração Bovino Corte',3000.00,3000.00,850.00,2.50,NULL,'2026-10-19 11:26:54','2026-10-19 16:32:42',14.00,2.90,12.00,NULL,NULL,'ativa','Engorda e terminação: energia elevada para ganho de peso rápido.'),(27,30,'Bovino','Fêmea','1-2 anos','Leite',NULL,'kg','Ração Bovino Leite',3000.00,3000.00,810.00,1.00,NULL,'2026-10-19 11:35:01','2026-10-19 16:33:43',22.00,1.75,16.00,NULL,NULL,'ativa','Para vacas em lactação: proteína alta para sustentar a produção de leite.'),(28,30,'Equino','Macho','2-3 anos','Esporte',NULL,'kg','Ração Equino Esporte',2000.00,2000.00,70.00,3.00,NULL,'2026-10-19 16:25:23','2026-10-19 16:30:34',13.00,3.00,12.00,NULL,NULL,'ativa','Alto desempenho: gordura e eletrólitos para treino e competição.'),(29,30,'Suíno','Macho','2-3 anos','Corte',NULL,'kg','Ração Suíno Terminação',4000.00,4000.00,490.00,1.20,NULL,'2026-10-19 16:34:38','2026-10-19 16:37:23',16.00,3.25,4.00,NULL,NULL,'ativa','Crescimento e terminação: energia e aminoácidos para carcaça magra.'),(34,36,'Bovino','Fêmea',NULL,'Leite',NULL,'kg','Ração Leite Bovino',1380.00,3000.00,12.00,2.80,'Nutri Campo','2026-10-20 16:32:55','2026-10-20 16:33:00',18.00,NULL,NULL,NULL,NULL,'ativa',NULL),(35,36,'Suíno',NULL,NULL,'Corte',NULL,'kg','Ração Suína Crescimento',720.00,3000.00,8.00,2.10,'Agro Ração','2026-10-20 16:32:56','2026-10-20 16:33:03',16.00,NULL,NULL,NULL,NULL,'ativa',NULL),(36,36,'Avicultura','Fêmea',NULL,'Ovos',NULL,'kg','Ração Postura Aves',30.00,3000.00,4.00,1.90,'Nutri Campo','2026-10-20 16:32:57','2026-10-20 16:33:06',17.00,NULL,NULL,NULL,NULL,'ativa',NULL);
/*!40000 ALTER TABLE `racaos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sensores`
--

DROP TABLE IF EXISTS `sensores`;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sensores`
--

LOCK TABLES `sensores` WRITE;
/*!40000 ALTER TABLE `sensores` DISABLE KEYS */;
INSERT INTO `sensores` VALUES (1,20,'TESTE-BAL-999','balanca',NULL,NULL,1,'2026-10-06 13:30:54',123.45,NULL,NULL,'2026-10-06 16:32:54','2026-10-06 16:32:54'),(2,20,'TESTE-DIST-999','distancia',60.00,10.00,1,'2026-10-06 13:30:59',29.14,NULL,NULL,'2026-10-06 16:32:54','2026-10-06 16:32:54'),(3,154,'DIST-A1-01','distancia',50.00,5.00,1,'2026-10-20 10:21:44',90.88,NULL,NULL,'2026-10-20 12:13:18','2026-10-20 13:21:44'),(4,154,'BAL-A1-01','balanca',NULL,NULL,1,'2026-10-20 10:21:46',20.00,NULL,NULL,'2026-10-20 12:13:19','2026-10-20 13:21:46');
/*!40000 ALTER TABLE `sensores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
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

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
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

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'agrifarm_bd'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
