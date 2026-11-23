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
INSERT INTO `acessibilidade_preferencias` VALUES (1,1,'light',100,1.00,NULL,1.00,'2026-08-25 14:17:55','2026-10-05 20:19:16'),(2,3,'dark',100,1.00,NULL,1.00,'2026-08-25 14:41:30','2026-08-25 15:01:20'),(3,6,'light',100,1.00,NULL,1.00,'2026-08-25 16:40:12','2026-08-25 17:19:14'),(4,2,'light',100,1.00,NULL,1.00,'2026-09-08 14:20:34','2026-09-08 14:20:34'),(6,13,'light',100,1.00,NULL,1.00,'2026-09-29 13:32:06','2026-09-29 13:45:10'),(7,14,'dark',100,1.00,NULL,1.00,'2026-09-29 13:45:39','2026-09-29 13:46:03'),(8,15,'light',100,1.00,NULL,1.00,'2026-09-29 13:54:44','2026-09-29 13:54:44'),(9,16,'light',100,1.00,NULL,1.00,'2026-09-29 14:12:41','2026-09-29 17:12:59'),(10,20,'light',100,1.00,NULL,1.00,'2026-10-19 16:58:09','2026-10-19 16:58:15');
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
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('laravel-cache-2c8893554a4c32ee079a82f172a70f1ca3cfe93d','i:1;',1792496072),('laravel-cache-2c8893554a4c32ee079a82f172a70f1ca3cfe93d:timer','i:1792496072;',1792496072),('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba','i:1;',1792418194),('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba:timer','i:1792418194;',1792418194),('laravel-cache-69d025205dbabc97b4709f1ab89012f3d821f589','i:1;',1792430025),('laravel-cache-69d025205dbabc97b4709f1ab89012f3d821f589:timer','i:1792430025;',1792430025);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cadastros`
--

LOCK TABLES `cadastros` WRITE;
/*!40000 ALTER TABLE `cadastros` DISABLE KEYS */;
INSERT INTO `cadastros` VALUES (1,'Bruna Figueira Silva','49095793893','2008-04-09','brunafsilva.09@icloud.com',NULL,NULL,'19990019586','perfil/38I6p0ymI9fK4mzaqrTnVF2uhUiTo6IDqy7uTITz.png','$2y$12$/4h0uyMbLu2M98r1RCLk/e.WIL866foYZYic7mdG2T0qWvqQ5jzt6',NULL,'2026-08-25 13:56:20','2026-08-25 14:19:48'),(2,'Maria Eduarda Evangelista',NULL,NULL,'maria.e.evangelista7@aluno.senai.br',NULL,NULL,'(19) 99146-9672',NULL,'$2y$12$qEltB7/DE4bMuGPXHfhr6.Uq5j2YW6xsduz.Dep3ip0z38myqqppu',NULL,'2026-08-25 14:01:28','2026-08-25 14:01:28'),(3,'Yasmin',NULL,NULL,'yasmin.gentina@gmail.com.br',NULL,NULL,'(19) 99312-7375',NULL,'$2y$12$CpowZ.OGxihuBTI1PDFyFOxVun8v7I4areIcJzlpWpAOX1rnTj/oS',NULL,'2026-08-25 14:11:02','2026-08-25 14:11:02'),(4,'ARTHUR BARBOSA AZEVEDO',NULL,NULL,'arthur.b.azevedo6@aluno.senai.br',NULL,NULL,'(19) 99584-4882',NULL,'$2y$12$Gn1Bj2RGQj.0YEJFJw/Be.311x292JH9w0F3/3Ac1hLJZlWu1GMfC',NULL,'2026-08-25 16:21:12','2026-08-25 16:21:12'),(5,'Lorena',NULL,NULL,'lorena@gmail.com',NULL,NULL,'(19) 99999-9999',NULL,'$2y$12$mnDrSkZSY1Vq568gdL.YB.5LZWfZ4zlyzBjbKQQQigCCF/gXC4Vom',NULL,'2026-08-25 16:33:09','2026-08-25 16:33:09'),(6,'Arthur Povidelo','42090469811','2008-08-05','arthur.providelo@aluno.senai.br',NULL,NULL,'19991192992',NULL,'$2y$12$g7sC7JC9D7VGMnOMh4m0NepXnE.qsZzw0V5vfj04Ge3dnG1Z..Z06',NULL,'2026-08-25 16:39:46','2026-08-25 16:59:09'),(7,'Maria Fernanda Galdino de Souza Felipe',NULL,NULL,'maria.f.felipe@aluno.senai.br',NULL,NULL,'(11) 98765-4321',NULL,'$2y$12$uBjUORLIhQl8HJJMPx8xb.m4yiunatcz8RwMz/SOXKVAa8JSyNnLG',NULL,'2026-08-25 16:52:44','2026-08-25 16:52:44'),(8,'Bruna Figueira Silva',NULL,NULL,'bruna.f.silva22@icloud.com.br',NULL,NULL,'(19) 99001-9586',NULL,'$2y$12$St14H/mxng9AxdT9/N/LLewIk1TziGq1OWE0ezovy9FCPWJcEFiBy',NULL,'2026-08-31 16:04:47','2026-08-31 16:04:47'),(11,'Bruna',NULL,NULL,'brunafsilva.09@icloud.com.br',NULL,NULL,'19990019586',NULL,'$2y$12$UGQUqyHYLru/elO76x/Kfupd2ZAwEaKY1TxXjnlmL0XySJlVqRnG6',NULL,'2026-09-21 19:46:00','2026-09-21 19:46:00'),(12,'João Emanoel',NULL,NULL,'joao@gmail.com',NULL,NULL,'19994535902',NULL,'$2y$12$REYL8/uVILxOueUDqWPQmulaA8p6FmjOinVgYFntQyCr4XRcGQftq',NULL,'2026-09-22 16:10:30','2026-09-22 16:10:30'),(13,'Maria Eduarda Evangelista',NULL,NULL,'madu@gmail.com',NULL,NULL,'(19) 99146-9672',NULL,'$2y$12$HvmQ3NcVMsvyGmOFFnY5t.pMJf2B6pUQxNLz4PA2AjywzO9wh3AMy',NULL,'2026-09-29 13:31:36','2026-09-29 13:31:36'),(14,'JOÃO EMANOEL PORFIRIO DE SOUZA',NULL,NULL,'joao.e.souza12@aluno.senai.br',NULL,NULL,'19999999999','perfil/arYpmbOYBzKycr9ypdaYfuiacbWG7N756zHy1oYG.jpg','$2y$12$yBGXrqIhupJF3JiTOcO/Q.FQnPcA5mt8FPcGqgpFMJKxDLIS362/q',NULL,'2026-09-29 13:34:32','2026-09-29 13:52:00'),(15,'zaz',NULL,NULL,'princisi@gmail.com',NULL,NULL,'(12) 12344-5554',NULL,'$2y$12$oxeZaYig2gSag1XFsdpNBO7BtcZk9qz.FMixOulmbYKshwaalufUm',NULL,'2026-09-29 13:54:12','2026-09-29 13:54:12'),(16,'Yasmin','45290641823','2008-05-20','yas@gmail.com',NULL,NULL,'19993127375',NULL,'$2y$12$o8aYWxLoYjxMq2BajcD1qu3F0EtC3HvmhOmXjP.jjSXyGnPZ4q.Jy',NULL,'2026-09-29 14:09:00','2026-09-29 14:39:27'),(17,'Yasmin',NULL,NULL,'yas@gmail',NULL,NULL,'19993127375',NULL,'$2y$12$41k39LqJJh.yDnVMZrH2rO2qopH5xCXQaM9C/IfWv4jcECrgy9cFS',NULL,'2026-09-29 14:50:12','2026-09-29 14:50:12'),(19,'Murilo',NULL,NULL,'murilo@gmail.com',NULL,NULL,'77777777777','perfil/PRxeITo9pWyLnDNtmEyCjEYfI7aOXq2xPNPoSnc4.png','$2y$12$ylt3LAEbS/qNy/448VLyEekrNjQM6JZLCrEEASvScFVOdtTy3qgH2',NULL,'2026-09-29 17:36:21','2026-09-29 17:37:31'),(20,'Bruna Figueira Silva',NULL,NULL,'bruna.f.silva22@aluno.senai.br',NULL,NULL,'(19) 99001-9586',NULL,'$2y$12$7prHjBcg8yuLwoCe8cH2hOyJVLLDI05Ml3DHEtkvab9uXs9xo9mFG',NULL,'2026-10-19 14:17:35','2026-10-19 14:17:35');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cocho_consumos`
--

LOCK TABLES `cocho_consumos` WRITE;
/*!40000 ALTER TABLE `cocho_consumos` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=216 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cochos`
--

LOCK TABLES `cochos` WRITE;
/*!40000 ALTER TABLE `cochos` DISABLE KEYS */;
INSERT INTO `cochos` VALUES (19,23,1,'Lote A1 - 01',NULL,200.00,200.00,NULL,'2026-09-22 17:03:13','2026-09-22 17:03:13'),(20,23,2,'Lote A1 - 02',NULL,200.00,123.45,NULL,'2026-09-22 17:03:13','2026-10-06 19:32:54'),(21,23,3,'Lote A1 - 03',NULL,200.00,200.00,NULL,'2026-09-22 17:03:13','2026-09-22 17:03:13'),(22,23,4,'Lote A1 - 04',NULL,200.00,200.00,NULL,'2026-09-22 17:03:13','2026-09-22 17:03:13'),(23,23,5,'Lote A1 - 05',NULL,200.00,200.00,NULL,'2026-09-22 17:03:13','2026-09-22 17:03:13'),(24,23,6,'Lote A1 - 06',NULL,200.00,200.00,NULL,'2026-09-22 17:03:13','2026-09-22 17:03:13'),(25,23,7,'Lote A1 - 07',NULL,200.00,200.00,NULL,'2026-09-22 17:03:13','2026-09-22 17:03:13'),(26,23,8,'Lote A1 - 08',NULL,200.00,200.00,NULL,'2026-09-22 17:03:13','2026-09-22 17:03:13'),(27,23,9,'Lote A1 - 09',NULL,200.00,200.00,NULL,'2026-09-22 17:03:13','2026-09-22 17:03:13'),(28,23,10,'Lote A1 - 10',NULL,200.00,200.00,NULL,'2026-09-22 17:03:13','2026-09-22 17:03:13'),(29,24,1,'Lote B1 - 01',NULL,200.00,50.00,NULL,'2026-09-22 17:06:08','2026-09-22 17:06:08'),(30,24,2,'Lote B1 - 02',NULL,200.00,50.00,NULL,'2026-09-22 17:06:08','2026-09-22 17:06:08'),(31,24,3,'Lote B1 - 03',NULL,200.00,50.00,NULL,'2026-09-22 17:06:08','2026-09-22 17:06:08'),(32,24,4,'Lote B1 - 04',NULL,200.00,50.00,NULL,'2026-09-22 17:06:08','2026-09-22 17:06:08'),(33,25,1,'Lote C1 - 01',NULL,10.00,5.00,NULL,'2026-09-22 17:10:36','2026-09-22 17:10:36'),(34,25,2,'Lote C1 - 02',NULL,10.00,5.00,NULL,'2026-09-22 17:10:36','2026-09-22 17:10:36'),(35,25,3,'Lote C1 - 03',NULL,10.00,5.00,NULL,'2026-09-22 17:10:36','2026-09-22 17:10:36'),(36,25,4,'Lote C1 - 04',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(37,25,5,'Lote C1 - 05',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(38,25,6,'Lote C1 - 06',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(39,25,7,'Lote C1 - 07',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(40,25,8,'Lote C1 - 08',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(41,25,9,'Lote C1 - 09',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(42,25,10,'Lote C1 - 10',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(43,25,11,'Lote C1 - 11',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(44,25,12,'Lote C1 - 12',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(45,25,13,'Lote C1 - 13',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(46,25,14,'Lote C1 - 14',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(47,25,15,'Lote C1 - 15',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(48,25,16,'Lote C1 - 16',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(49,25,17,'Lote C1 - 17',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(50,25,18,'Lote C1 - 18',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(51,25,19,'Lote C1 - 19',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(52,25,20,'Lote C1 - 20',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(53,25,21,'Lote C1 - 21',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(54,25,22,'Lote C1 - 22',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(55,25,23,'Lote C1 - 23',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(56,25,24,'Lote C1 - 24',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(57,25,25,'Lote C1 - 25',NULL,10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(58,26,1,'Lote D1 - 01',NULL,100.00,20.00,NULL,'2026-09-22 17:16:59','2026-09-22 17:16:59'),(59,26,2,'Lote D1 - 02',NULL,100.00,20.00,NULL,'2026-09-22 17:16:59','2026-09-22 17:16:59'),(60,26,3,'Lote D1 - 03',NULL,100.00,20.00,NULL,'2026-09-22 17:16:59','2026-09-22 17:16:59'),(61,26,4,'Lote D1 - 04',NULL,100.00,20.00,NULL,'2026-09-22 17:16:59','2026-09-22 17:16:59'),(62,26,5,'Lote D1 - 05',NULL,100.00,20.00,NULL,'2026-09-22 17:16:59','2026-09-22 17:16:59'),(63,27,1,'Lote A2 - 01',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(64,27,2,'Lote A2 - 02',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(65,27,3,'Lote A2 - 03',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(66,27,4,'Lote A2 - 04',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(67,27,5,'Lote A2 - 05',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(68,27,6,'Lote A2 - 06',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(69,27,7,'Lote A2 - 07',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(70,27,8,'Lote A2 - 08',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(71,27,9,'Lote A2 - 09',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(72,27,10,'Lote A2 - 10',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(73,27,11,'Lote A2 - 11',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(74,27,12,'Lote A2 - 12',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(75,27,13,'Lote A2 - 13',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(76,27,14,'Lote A2 - 14',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(77,27,15,'Lote A2 - 15',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(78,27,16,'Lote A2 - 16',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(79,27,17,'Lote A2 - 17',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(80,27,18,'Lote A2 - 18',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(81,27,19,'Lote A2 - 19',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(82,27,20,'Lote A2 - 20',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(83,27,21,'Lote A2 - 21',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(84,27,22,'Lote A2 - 22',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(85,27,23,'Lote A2 - 23',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(86,27,24,'Lote A2 - 24',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(87,27,25,'Lote A2 - 25',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(88,27,26,'Lote A2 - 26',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(89,27,27,'Lote A2 - 27',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(90,27,28,'Lote A2 - 28',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(91,27,29,'Lote A2 - 29',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(92,27,30,'Lote A2 - 30',NULL,400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(93,28,1,'a1 - 01',NULL,122.00,100.00,NULL,'2026-09-29 13:38:32','2026-09-29 13:38:32'),(94,28,2,'a1 - 02',NULL,122.00,100.00,NULL,'2026-09-29 13:38:32','2026-09-29 13:38:32'),(95,28,3,'a1 - 03',NULL,122.00,100.00,NULL,'2026-09-29 13:38:32','2026-09-29 13:38:32'),(96,29,1,'Lote A1 - 01',NULL,200.00,180.00,NULL,'2026-09-29 13:55:16','2026-09-29 14:25:40'),(97,29,2,'Lote A1 - 02',NULL,200.00,190.00,NULL,'2026-09-29 13:55:16','2026-09-29 14:26:02'),(98,29,3,'Lote A1 - 03',NULL,200.00,170.00,NULL,'2026-09-29 13:55:16','2026-09-29 14:31:28'),(99,29,4,'Lote A1 - 04',NULL,200.00,150.00,NULL,'2026-09-29 13:55:16','2026-09-29 13:55:16'),(100,29,5,'Lote A1 - 05',NULL,200.00,180.00,NULL,'2026-09-29 13:55:16','2026-09-29 14:31:57'),(101,29,6,'Lote A1 - 06',NULL,200.00,150.00,NULL,'2026-09-29 13:55:16','2026-09-29 13:55:16'),(102,29,7,'Lote A1 - 07',NULL,200.00,150.00,NULL,'2026-09-29 13:55:16','2026-09-29 13:55:16'),(103,29,8,'Lote A1 - 08',NULL,200.00,150.00,NULL,'2026-09-29 13:55:16','2026-09-29 13:55:16'),(104,29,9,'Lote A1 - 09',NULL,200.00,150.00,NULL,'2026-09-29 13:55:16','2026-09-29 13:55:16'),(105,29,10,'Lote A1 - 10',NULL,200.00,150.00,NULL,'2026-09-29 13:55:16','2026-09-29 13:55:16'),(106,30,1,'Lote A2 - 01',NULL,100.00,100.00,'4545','2026-09-29 13:57:02','2026-09-29 14:25:12'),(107,31,1,'Lote A3 - 01',NULL,80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(108,31,2,'Lote A3 - 02',NULL,80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(109,31,3,'Lote A3 - 03',NULL,80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(110,31,4,'Lote A3 - 04',NULL,80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(111,31,5,'Lote A3 - 05',NULL,80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(112,31,6,'Lote A3 - 06',NULL,80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(113,31,7,'Lote A3 - 07',NULL,80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(114,31,8,'Lote A3 - 08',NULL,80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(115,31,9,'Lote A3 - 09',NULL,80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(116,31,10,'Lote A3 - 10',NULL,80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(117,31,11,'Lote A3 - 11',NULL,80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(118,31,12,'Lote A3 - 12',NULL,80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(119,33,1,'lote 1 - 01',NULL,300.00,200.00,NULL,'2026-09-29 14:12:19','2026-09-29 14:12:19'),(120,33,2,'lote 1 - 02',NULL,300.00,200.00,NULL,'2026-09-29 14:12:19','2026-09-29 14:12:19'),(121,33,3,'lote 1 - 03',NULL,300.00,200.00,NULL,'2026-09-29 14:12:19','2026-09-29 14:12:19'),(122,33,4,'lote 1 - 04',NULL,300.00,200.00,NULL,'2026-09-29 14:12:19','2026-09-29 14:12:19'),(123,33,5,'lote 1 - 05',NULL,300.00,200.00,NULL,'2026-09-29 14:12:19','2026-09-29 14:12:19'),(124,33,6,'lote 1 - 06',NULL,300.00,200.00,NULL,'2026-09-29 14:12:19','2026-09-29 14:12:19'),(125,33,7,'lote 1 - 07',NULL,300.00,200.00,NULL,'2026-09-29 14:12:19','2026-09-29 14:12:19'),(126,33,8,'lote 1 - 08',NULL,300.00,200.00,NULL,'2026-09-29 14:12:19','2026-09-29 14:12:19'),(127,33,9,'lote 1 - 09',NULL,300.00,200.00,NULL,'2026-09-29 14:12:19','2026-09-29 14:12:19'),(128,33,10,'lote 1 - 10',NULL,300.00,200.00,NULL,'2026-09-29 14:12:19','2026-09-29 14:12:19'),(129,34,1,'lote 2 - 01',NULL,400.00,350.00,NULL,'2026-09-29 14:29:48','2026-09-29 14:29:48'),(130,34,2,'lote 2 - 02',NULL,400.00,350.00,NULL,'2026-09-29 14:29:48','2026-09-29 14:29:48'),(131,34,3,'lote 2 - 03',NULL,400.00,350.00,NULL,'2026-09-29 14:29:48','2026-09-29 14:29:48'),(132,34,4,'lote 2 - 04',NULL,400.00,350.00,NULL,'2026-09-29 14:29:48','2026-09-29 14:29:48'),(133,34,5,'lote 2 - 05',NULL,400.00,350.00,NULL,'2026-09-29 14:29:48','2026-09-29 14:29:48'),(134,34,6,'lote 2 - 06',NULL,400.00,350.00,NULL,'2026-09-29 14:29:48','2026-09-29 14:29:48'),(135,34,7,'lote 2 - 07',NULL,400.00,350.00,NULL,'2026-09-29 14:29:48','2026-09-29 14:29:48'),(136,34,8,'lote 2 - 08',NULL,400.00,350.00,NULL,'2026-09-29 14:29:49','2026-09-29 14:29:49'),(137,34,9,'lote 2 - 09',NULL,400.00,350.00,NULL,'2026-09-29 14:29:49','2026-09-29 14:29:49'),(138,34,10,'lote 2 - 10',NULL,400.00,350.00,NULL,'2026-09-29 14:29:49','2026-09-29 14:29:49'),(139,34,11,'lote 2 - 11',NULL,400.00,350.00,NULL,'2026-09-29 14:29:49','2026-09-29 14:29:49'),(140,34,12,'lote 2 - 12',NULL,400.00,350.00,NULL,'2026-09-29 14:29:49','2026-09-29 14:29:49'),(141,34,13,'lote 2 - 13',NULL,400.00,350.00,NULL,'2026-09-29 14:29:49','2026-09-29 14:29:49'),(142,34,14,'lote 2 - 14',NULL,400.00,350.00,NULL,'2026-09-29 14:29:49','2026-09-29 14:29:49'),(143,34,15,'lote 2 - 15',NULL,400.00,350.00,NULL,'2026-09-29 14:29:49','2026-09-29 14:29:49'),(144,34,16,'lote 2 - 16',NULL,400.00,350.00,NULL,'2026-09-29 14:29:49','2026-09-29 14:29:49'),(145,35,1,'Lote F1 - 01',NULL,100.00,100.00,NULL,'2026-10-06 16:50:15','2026-10-06 16:50:15'),(146,35,2,'Lote F1 - 02',NULL,100.00,100.00,NULL,'2026-10-06 16:50:15','2026-10-06 16:50:15'),(147,35,3,'Lote F1 - 03',NULL,100.00,100.00,NULL,'2026-10-06 16:50:15','2026-10-06 16:50:15'),(148,35,4,'Lote F1 - 04',NULL,100.00,100.00,NULL,'2026-10-06 16:50:15','2026-10-06 16:50:15'),(149,35,5,'Lote F1 - 05',NULL,100.00,100.00,NULL,'2026-10-06 16:50:15','2026-10-06 16:50:15'),(150,35,6,'Lote F1 - 06',NULL,100.00,100.00,NULL,'2026-10-06 16:50:15','2026-10-06 16:50:15'),(151,35,7,'Lote F1 - 07',NULL,100.00,100.00,NULL,'2026-10-06 16:50:15','2026-10-06 16:50:15'),(152,35,8,'Lote F1 - 08',NULL,100.00,100.00,NULL,'2026-10-06 16:50:15','2026-10-06 16:50:15'),(154,36,1,'Lote A1 - 01',NULL,200.00,101.67,NULL,'2026-10-19 14:22:16','2026-10-19 14:28:00'),(155,36,2,'Lote A1 - 02',NULL,200.00,101.67,NULL,'2026-10-19 14:22:16','2026-10-19 14:28:00'),(156,36,3,'Lote A1 - 03',NULL,200.00,101.67,NULL,'2026-10-19 14:22:16','2026-10-19 14:28:00'),(157,36,4,'Lote A1 - 04',NULL,200.00,101.67,NULL,'2026-10-19 14:22:16','2026-10-19 14:28:00'),(158,36,5,'Lote A1 - 05',NULL,200.00,101.67,NULL,'2026-10-19 14:22:16','2026-10-19 14:28:00'),(159,36,6,'Lote A1 - 06',NULL,200.00,101.67,NULL,'2026-10-19 14:22:16','2026-10-19 14:28:00'),(160,36,7,'Lote A1 - 07',NULL,200.00,101.67,NULL,'2026-10-19 14:22:16','2026-10-19 14:28:00'),(161,36,8,'Lote A1 - 08',NULL,200.00,101.67,NULL,'2026-10-19 14:22:16','2026-10-19 14:28:00'),(162,36,9,'Lote A1 - 09',NULL,200.00,101.67,NULL,'2026-10-19 14:22:16','2026-10-19 14:28:00'),(163,36,10,'Lote A1 - 10',NULL,200.00,101.67,NULL,'2026-10-19 14:22:16','2026-10-19 14:28:00'),(164,36,11,'Lote A1 - 11',NULL,200.00,101.67,NULL,'2026-10-19 14:22:16','2026-10-19 14:28:00'),(165,36,12,'Lote A1 - 12',NULL,200.00,101.67,NULL,'2026-10-19 14:22:17','2026-10-19 14:28:00'),(172,37,1,'Lote A2 - 01',NULL,200.00,140.00,NULL,'2026-10-19 14:32:52','2026-10-19 19:12:17'),(173,37,2,'Lote A2 - 02',NULL,200.00,140.00,NULL,'2026-10-19 14:32:52','2026-10-19 19:12:17'),(174,37,3,'Lote A2 - 03',NULL,200.00,140.00,NULL,'2026-10-19 14:32:52','2026-10-19 19:12:17'),(175,37,4,'Lote A2 - 04',NULL,200.00,140.00,NULL,'2026-10-19 14:32:52','2026-10-19 19:12:17'),(176,37,5,'Lote A2 - 05',NULL,200.00,140.00,NULL,'2026-10-19 14:32:52','2026-10-19 19:12:17'),(177,37,6,'Lote A2 - 06',NULL,200.00,140.00,NULL,'2026-10-19 14:32:52','2026-10-19 19:12:17'),(178,37,7,'Lote A2 - 07',NULL,200.00,140.00,NULL,'2026-10-19 14:32:52','2026-10-19 19:12:17'),(179,37,8,'Lote A2 - 08',NULL,200.00,140.00,NULL,'2026-10-19 14:32:52','2026-10-19 19:12:17'),(180,37,9,'Lote A2 - 09',NULL,200.00,140.00,NULL,'2026-10-19 14:32:52','2026-10-19 19:12:17'),(181,37,10,'Lote A2 - 10',NULL,200.00,140.00,NULL,'2026-10-19 14:32:52','2026-10-19 19:12:17'),(182,37,11,'Lote A2 - 11',NULL,400.00,200.00,NULL,'2026-10-19 17:23:08','2026-10-19 19:12:17'),(183,37,12,'Lote A2 - 12',NULL,400.00,200.00,NULL,'2026-10-19 17:23:08','2026-10-19 19:12:17'),(184,37,13,'Lote A2 - 13',NULL,400.00,200.00,NULL,'2026-10-19 17:23:08','2026-10-19 19:12:17'),(185,37,14,'Lote A2 - 14',NULL,400.00,200.00,NULL,'2026-10-19 17:23:08','2026-10-19 19:12:17'),(186,37,15,'Lote A2 - 15',NULL,400.00,200.00,NULL,'2026-10-19 17:23:08','2026-10-19 19:12:17'),(187,37,16,'Lote A2 - 16',NULL,400.00,200.00,NULL,'2026-10-19 17:23:08','2026-10-19 19:12:17'),(188,37,17,'Lote A2 - 17',NULL,400.00,200.00,NULL,'2026-10-19 17:23:08','2026-10-19 19:12:17'),(189,37,18,'Lote A2 - 18',NULL,400.00,200.00,NULL,'2026-10-19 17:23:08','2026-10-19 19:12:17'),(190,37,19,'Lote A2 - 19',NULL,400.00,200.00,NULL,'2026-10-19 17:23:08','2026-10-19 19:12:17'),(191,37,20,'Lote A2 - 20',NULL,400.00,200.00,NULL,'2026-10-19 17:23:08','2026-10-19 19:12:17'),(192,37,21,'Lote A2 - 21',NULL,400.00,200.00,NULL,'2026-10-19 17:23:08','2026-10-19 19:12:17'),(193,37,22,'Lote A2 - 22',NULL,400.00,200.00,NULL,'2026-10-19 17:23:08','2026-10-19 19:12:17'),(194,37,23,'Lote A2 - 23',NULL,400.00,200.00,NULL,'2026-10-19 17:23:08','2026-10-19 19:12:17'),(195,37,24,'Lote A2 - 24',NULL,400.00,200.00,NULL,'2026-10-19 17:23:08','2026-10-19 19:12:17'),(196,39,1,'Lote C1 - 01',NULL,100.00,50.00,NULL,'2026-10-19 19:15:00','2026-10-19 19:15:00'),(197,39,2,'Lote C1 - 02',NULL,100.00,50.00,NULL,'2026-10-19 19:15:24','2026-10-19 19:15:24'),(198,39,3,'Lote C1 - 03',NULL,100.00,50.00,NULL,'2026-10-19 19:15:24','2026-10-19 19:15:24'),(199,39,4,'Lote C1 - 04',NULL,100.00,50.00,NULL,'2026-10-19 19:15:24','2026-10-19 19:15:24'),(200,39,5,'Lote C1 - 05',NULL,100.00,50.00,NULL,'2026-10-19 19:15:24','2026-10-19 19:15:24'),(201,40,1,'Lote D1 - 01',NULL,400.00,100.00,NULL,'2026-10-19 19:16:31','2026-10-19 19:16:31'),(202,40,2,'Lote D1 - 02',NULL,400.00,100.00,NULL,'2026-10-19 19:16:31','2026-10-19 19:16:31'),(203,40,3,'Lote D1 - 03',NULL,400.00,100.00,NULL,'2026-10-19 19:16:31','2026-10-19 19:16:31'),(204,40,4,'Lote D1 - 04',NULL,400.00,100.00,NULL,'2026-10-19 19:16:31','2026-10-19 19:16:31'),(205,40,5,'Lote D1 - 05',NULL,400.00,100.00,NULL,'2026-10-19 19:16:31','2026-10-19 19:16:31'),(206,41,1,'Lote F1 - 01',NULL,400.00,100.00,NULL,'2026-10-19 19:19:00','2026-10-19 19:19:00'),(207,41,2,'Lote F1 - 02',NULL,400.00,100.00,NULL,'2026-10-19 19:19:00','2026-10-19 19:19:00'),(208,41,3,'Lote F1 - 03',NULL,400.00,100.00,NULL,'2026-10-19 19:19:00','2026-10-19 19:19:00'),(209,41,4,'Lote F1 - 04',NULL,400.00,100.00,NULL,'2026-10-19 19:19:00','2026-10-19 19:19:00'),(210,41,5,'Lote F1 - 05',NULL,400.00,100.00,NULL,'2026-10-19 19:19:00','2026-10-19 19:19:00'),(211,41,6,'Lote F1 - 06',NULL,400.00,100.00,NULL,'2026-10-19 19:19:00','2026-10-19 19:19:00'),(212,41,7,'Lote F1 - 07',NULL,400.00,100.00,NULL,'2026-10-19 19:19:00','2026-10-19 19:19:00'),(213,41,8,'Lote F1 - 08',NULL,400.00,100.00,NULL,'2026-10-19 19:19:00','2026-10-19 19:19:00'),(214,38,1,'Lote B1 - 01',NULL,400.00,400.00,NULL,'2026-10-19 19:38:14','2026-10-19 19:38:14'),(215,38,2,'Lote B1 - 02',NULL,400.00,400.00,NULL,'2026-10-19 19:38:14','2026-10-19 19:38:14');
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
-- Dumping data for table `codigos_verificacao`
--

LOCK TABLES `codigos_verificacao` WRITE;
/*!40000 ALTER TABLE `codigos_verificacao` DISABLE KEYS */;
INSERT INTO `codigos_verificacao` VALUES (1,'arthur.providelo@aluno.senai.br','$2y$12$nqob/nbESjnF7pZzRZur2eKijzo6W37SocRyLA/mMrP6pr9tm6u5e','alteracao_senha','$2y$12$bxUL3GphDjkxLng1qrx6FOA59LfPFmEuzCDXVTfrjP1HMO5wSWO6u','2026-08-25 17:14:14',NULL,'2026-08-25 16:59:14','2026-08-25 16:59:14'),(2,'brunafsilva.09@icloud.com','$2y$12$pGw2gipOaLEgTI1IRLCu5eeof9T4/ARPjCZ5GrfzphehYYh/NDlBS','redefinicao_senha',NULL,'2026-09-01 17:54:25',NULL,'2026-09-01 17:39:25','2026-09-01 17:39:25'),(3,'maria.e.evangelista7@aluno.senai.br','$2y$12$qmTERRc8hCi3AIkcb7ak7OBeXtX7QTOHXOPIX0FnHQ..y0YHRJoYC','redefinicao_senha',NULL,'2026-09-08 14:33:05',NULL,'2026-09-08 14:18:05','2026-09-08 14:18:05');
/*!40000 ALTER TABLE `codigos_verificacao` ENABLE KEYS */;
UNLOCK TABLES;

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
INSERT INTO `comentarios` VALUES (1,1,NULL,'Site muito bom!',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-25 17:48:01','2026-08-25 17:48:01'),(4,1,'Bruna Figueira Silva','Gostei!',4,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-25 19:58:52','2026-08-25 19:58:52'),(5,1,'Bruna Figueira Silva','Bom!',3,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-25 20:26:32','2026-08-25 20:26:32'),(6,1,'Bruna Figueira Silva','Muito bom!',5,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-25 21:01:32','2026-08-25 21:01:32'),(7,1,'Bruna Figueira Silva','Excelente!',5,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-25 21:01:44','2026-08-25 21:01:44'),(8,1,'Bruna Figueira Silva','Bom demais!',5,5,5,5,5,5,5,'2026-08-25 21:02:19','2026-08-25 21:02:19');
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
INSERT INTO `desempenho_lotes` VALUES (9,23,478.00,40,'2026-10-05',0.00,0.00,0.00,0.00,0.00,'2026-10-05 17:49:50','2026-10-05 17:49:50'),(10,36,740.00,80,'2026-10-19',0.00,0.00,0.00,0.00,0.00,'2026-10-19 19:26:56','2026-10-19 19:26:56');
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
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estoque_movimentacoes`
--

LOCK TABLES `estoque_movimentacoes` WRITE;
/*!40000 ALTER TABLE `estoque_movimentacoes` DISABLE KEYS */;
INSERT INTO `estoque_movimentacoes` VALUES (1,1,23,1,NULL,NULL,'entrada','cadastro_racao',100.00,0.00,100.00,1.50,'Estoque inicial no cadastro da ração.','2026-10-06 12:35:20','2026-10-06 15:35:20','2026-10-06 15:35:20'),(4,30,26,20,NULL,NULL,'entrada','cadastro_racao',500.00,0.00,500.00,1.50,'Estoque inicial no cadastro da ração.','2026-10-19 11:26:54','2026-10-19 14:26:54','2026-10-19 14:26:54'),(5,30,26,20,36,154,'saida','abastecimento_cocho',1.67,500.00,498.33,1.50,NULL,'2026-10-19 11:28:00','2026-10-19 14:28:00','2026-10-19 14:28:00'),(6,30,26,20,36,155,'saida','abastecimento_cocho',1.67,498.33,496.66,1.50,NULL,'2026-10-19 11:28:00','2026-10-19 14:28:00','2026-10-19 14:28:00'),(7,30,26,20,36,156,'saida','abastecimento_cocho',1.67,496.66,494.99,1.50,NULL,'2026-10-19 11:28:00','2026-10-19 14:28:00','2026-10-19 14:28:00'),(8,30,26,20,36,157,'saida','abastecimento_cocho',1.67,494.99,493.32,1.50,NULL,'2026-10-19 11:28:00','2026-10-19 14:28:00','2026-10-19 14:28:00'),(9,30,26,20,36,158,'saida','abastecimento_cocho',1.67,493.32,491.65,1.50,NULL,'2026-10-19 11:28:00','2026-10-19 14:28:00','2026-10-19 14:28:00'),(10,30,26,20,36,159,'saida','abastecimento_cocho',1.67,491.65,489.98,1.50,NULL,'2026-10-19 11:28:00','2026-10-19 14:28:00','2026-10-19 14:28:00'),(11,30,26,20,36,160,'saida','abastecimento_cocho',1.67,489.98,488.31,1.50,NULL,'2026-10-19 11:28:00','2026-10-19 14:28:00','2026-10-19 14:28:00'),(12,30,26,20,36,161,'saida','abastecimento_cocho',1.67,488.31,486.64,1.50,NULL,'2026-10-19 11:28:00','2026-10-19 14:28:00','2026-10-19 14:28:00'),(13,30,26,20,36,162,'saida','abastecimento_cocho',1.67,486.64,484.97,1.50,NULL,'2026-10-19 11:28:00','2026-10-19 14:28:00','2026-10-19 14:28:00'),(14,30,26,20,36,163,'saida','abastecimento_cocho',1.67,484.97,483.30,1.50,NULL,'2026-10-19 11:28:00','2026-10-19 14:28:00','2026-10-19 14:28:00'),(15,30,26,20,36,164,'saida','abastecimento_cocho',1.67,483.30,481.63,1.50,NULL,'2026-10-19 11:28:00','2026-10-19 14:28:00','2026-10-19 14:28:00'),(16,30,26,20,36,165,'saida','abastecimento_cocho',1.67,481.63,479.96,1.50,NULL,'2026-10-19 11:28:00','2026-10-19 14:28:00','2026-10-19 14:28:00'),(17,30,26,20,36,NULL,'saida','abastecimento_cocho',1.67,479.96,478.29,1.50,NULL,'2026-10-19 11:28:00','2026-10-19 14:28:00','2026-10-19 14:28:00'),(18,30,26,20,36,NULL,'saida','abastecimento_cocho',1.67,478.29,476.62,1.50,NULL,'2026-10-19 11:28:00','2026-10-19 14:28:00','2026-10-19 14:28:00'),(19,30,26,20,36,NULL,'saida','abastecimento_cocho',1.67,476.62,474.95,1.50,NULL,'2026-10-19 11:28:00','2026-10-19 14:28:00','2026-10-19 14:28:00'),(20,30,26,20,36,NULL,'saida','abastecimento_cocho',1.67,474.95,473.28,1.50,NULL,'2026-10-19 11:28:00','2026-10-19 14:28:00','2026-10-19 14:28:00'),(21,30,26,20,36,NULL,'saida','abastecimento_cocho',1.67,473.28,471.61,1.50,NULL,'2026-10-19 11:28:00','2026-10-19 14:28:00','2026-10-19 14:28:00'),(22,30,26,20,36,NULL,'saida','abastecimento_cocho',1.67,471.61,469.94,1.50,NULL,'2026-10-19 11:28:00','2026-10-19 14:28:00','2026-10-19 14:28:00'),(23,30,27,20,NULL,NULL,'entrada','cadastro_racao',500.00,0.00,500.00,1.00,'Estoque inicial no cadastro da ração.','2026-10-19 11:35:01','2026-10-19 14:35:01','2026-10-19 14:35:01'),(24,30,27,20,37,172,'saida','abastecimento_cocho',40.00,500.00,460.00,1.00,NULL,'2026-10-19 11:35:19','2026-10-19 14:35:20','2026-10-19 14:35:20'),(25,30,27,20,37,173,'saida','abastecimento_cocho',40.00,460.00,420.00,1.00,NULL,'2026-10-19 11:35:20','2026-10-19 14:35:20','2026-10-19 14:35:20'),(26,30,27,20,37,174,'saida','abastecimento_cocho',40.00,420.00,380.00,1.00,NULL,'2026-10-19 11:35:20','2026-10-19 14:35:20','2026-10-19 14:35:20'),(27,30,27,20,37,175,'saida','abastecimento_cocho',40.00,380.00,340.00,1.00,NULL,'2026-10-19 11:35:20','2026-10-19 14:35:20','2026-10-19 14:35:20'),(28,30,27,20,37,176,'saida','abastecimento_cocho',40.00,340.00,300.00,1.00,NULL,'2026-10-19 11:35:20','2026-10-19 14:35:20','2026-10-19 14:35:20'),(29,30,27,20,37,177,'saida','abastecimento_cocho',40.00,300.00,260.00,1.00,NULL,'2026-10-19 11:35:20','2026-10-19 14:35:20','2026-10-19 14:35:20'),(30,30,27,20,37,178,'saida','abastecimento_cocho',40.00,260.00,220.00,1.00,NULL,'2026-10-19 11:35:20','2026-10-19 14:35:20','2026-10-19 14:35:20'),(31,30,27,20,37,179,'saida','abastecimento_cocho',40.00,220.00,180.00,1.00,NULL,'2026-10-19 11:35:20','2026-10-19 14:35:20','2026-10-19 14:35:20'),(32,30,27,20,37,180,'saida','abastecimento_cocho',40.00,180.00,140.00,1.00,NULL,'2026-10-19 11:35:20','2026-10-19 14:35:20','2026-10-19 14:35:20'),(33,30,27,20,37,181,'saida','abastecimento_cocho',40.00,140.00,100.00,1.00,NULL,'2026-10-19 11:35:20','2026-10-19 14:35:20','2026-10-19 14:35:20'),(34,30,26,20,NULL,NULL,'ajuste','edicao_racao',2530.06,469.94,3000.00,1.50,'Estoque alterado na edição da ração.','2026-10-19 14:51:45','2026-10-19 17:51:45','2026-10-19 17:51:45'),(35,30,27,20,NULL,NULL,'ajuste','edicao_racao',2900.00,100.00,3000.00,1.00,'Estoque alterado na edição da ração.','2026-10-19 14:52:06','2026-10-19 17:52:06','2026-10-19 17:52:06'),(36,30,28,20,NULL,NULL,'entrada','cadastro_racao',2000.00,0.00,2000.00,3.00,'Estoque inicial no cadastro da ração.','2026-10-19 16:25:23','2026-10-19 19:25:23','2026-10-19 19:25:23'),(37,30,29,20,NULL,NULL,'entrada','cadastro_racao',4000.00,0.00,4000.00,1.20,'Estoque inicial no cadastro da ração.','2026-10-19 16:34:38','2026-10-19 19:34:38','2026-10-19 19:34:38');
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
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fazendas`
--

LOCK TABLES `fazendas` WRITE;
/*!40000 ALTER TABLE `fazendas` DISABLE KEYS */;
INSERT INTO `fazendas` VALUES (1,1,'Fazenda Talismã','Bruna Figueira Silva','Tambaú - SP','13710-224','Tambaú','SP',-21.7021054,-47.2652065,70.00,'2026-08-25 13:57:05','2026-08-25 13:57:05'),(2,1,'Fazenda Morandin','Bruna Figueira Silva','Tambaú - SP','13710-224','Tambaú','SP',-21.6931734,-47.2739316,56.00,'2026-08-25 13:57:26','2026-08-25 13:57:26'),(3,1,'Fazenda São João','Bruna Figueira Silva','São João da Boa Vista - SP','13870-020','São João da Boa Vista','SP',-21.9704416,-46.7953369,80.00,'2026-08-25 13:58:04','2026-08-25 13:58:04'),(4,2,'Fazenda Santa Helena','Maria Eduarda Evangelista','Adolfo - SP','35609-00','Adolfo','SP',NULL,NULL,10.00,'2026-08-25 14:02:35','2026-08-25 14:02:35'),(5,3,'Fazenda del gado','Yasmin','Tambaú - SP','13710-083','Tambaú','SP',NULL,NULL,10.00,'2026-08-25 14:13:51','2026-08-25 14:13:51'),(8,4,'ARTHUR BARBOSA AZEVEDO','ARTHUR BARBOSA AZEVEDO','Araruna - PB','92480-00','Araruna','PB',NULL,NULL,1.29,'2026-08-25 16:51:26','2026-08-25 16:51:26'),(9,5,'São José','Lorena','São José do Rio Pardo - SP',NULL,'São José do Rio Pardo','SP',NULL,NULL,1.01,'2026-08-25 16:51:56','2026-08-25 16:51:56'),(10,7,'Fazenda São Miguel','Maria Fernanda Galdino de Souza Felipe','Tambaú - SP','13710-000','Tambaú','SP',NULL,NULL,12.06,'2026-08-25 16:58:34','2026-08-25 16:58:34'),(11,6,'FAZENDA SENAI','Arthur Povidelo','Tambaú - SP','13710-290','Tambaú','SP',-21.7126435,-47.2442523,210003.00,'2026-08-25 17:05:00','2026-08-25 17:05:00'),(12,8,'Fazenda Talismã','Bruna Figueira Silva','Tambaú - SP','13711-244','Tambaú','SP',-21.7105437,-47.2748834,56.00,'2026-08-31 16:09:14','2026-08-31 16:09:14'),(18,14,'bexe','JOÃO EMANOEL PORFIRIO DE SOUZA','Santo Antônio de Jesus - BA','44444-444','Santo Antônio de Jesus','BA',-12.9555997,-39.2609139,10.00,'2026-09-29 13:36:22','2026-09-29 13:36:22'),(21,13,'Fazenda Santa Helena','Maria Eduarda Evangelista','Areias - SP','12820-000','Areias','SP',NULL,NULL,10.00,'2026-09-29 13:47:43','2026-09-29 13:52:18'),(22,13,'Fazenda Vila Nova','Maria Eduarda Evangelista','Areado - MG','37140-000','Areado','MG',NULL,NULL,20.00,'2026-09-29 13:49:06','2026-09-29 13:51:38'),(23,13,'Fazenda São João','Maria Eduarda Evangelista','Areial - PB','58140-000','Areial','PB',NULL,NULL,30.00,'2026-09-29 13:49:48','2026-09-29 13:52:52'),(24,15,'zazafabu','zaz','Tambaú - SP','13711-412','Tambaú','SP',NULL,NULL,34444.00,'2026-09-29 13:55:26','2026-09-29 13:55:26'),(25,16,'Fazenda del gado','Yasmin','Tambaú - SP','13710-083','Tambaú','SP',NULL,NULL,10.00,'2026-09-29 14:10:39','2026-09-29 14:10:39'),(26,16,'Fazenda del gado','Yasmin','Tambaú - SP','13710-083','Tambaú','SP',NULL,NULL,10.00,'2026-09-29 14:27:48','2026-09-29 14:27:48'),(29,19,'JOÃO EMANOEL PORFIRIO DE SOUZA','Murilo','Santo Antônio de Jesus - BA','44444-444','Santo Antônio de Jesus','BA',NULL,NULL,9999999.00,'2026-09-29 17:38:32','2026-09-29 17:38:32'),(30,20,'Fazenda Talismã','Bruna Figueira Silva','Jussara - GO','76270-000','Jussara','GO',-15.9393599,-50.1322387,230.00,'2026-10-19 14:19:38','2026-10-19 17:10:05'),(31,20,'Fazenda Pirassununga','Bruna Figueira Silva','Pirassununga - SP','13631-904','Pirassununga','SP',-22.0073139,-47.4447773,120.00,'2026-10-19 14:31:43','2026-10-19 17:10:28');
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
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leituras_iot`
--

LOCK TABLES `leituras_iot` WRITE;
/*!40000 ALTER TABLE `leituras_iot` DISABLE KEYS */;
INSERT INTO `leituras_iot` VALUES (1,20,1,'balanca',NULL,190.00,NULL,NULL,95.00,190.00,-10.00,'2026-10-05 10:30:54','2026-10-06 19:32:54','2026-10-06 19:32:54'),(2,20,2,'distancia',12.50,NULL,NULL,NULL,95.00,NULL,NULL,'2026-10-05 10:30:59','2026-10-06 19:32:54','2026-10-06 19:32:54'),(3,20,1,'balanca',NULL,182.40,NULL,NULL,91.20,182.40,-7.60,'2026-10-05 12:30:54','2026-10-06 19:32:54','2026-10-06 19:32:54'),(4,20,2,'distancia',14.40,NULL,NULL,NULL,91.20,NULL,NULL,'2026-10-05 12:30:59','2026-10-06 19:32:54','2026-10-06 19:32:54'),(5,20,1,'balanca',NULL,175.10,NULL,NULL,87.55,175.10,-7.30,'2026-10-05 14:30:54','2026-10-06 19:32:54','2026-10-06 19:32:54'),(6,20,2,'distancia',16.23,NULL,NULL,NULL,87.54,NULL,NULL,'2026-10-05 14:30:59','2026-10-06 19:32:54','2026-10-06 19:32:54'),(7,20,1,'balanca',NULL,166.80,NULL,NULL,83.40,166.80,-8.30,'2026-10-05 16:30:54','2026-10-06 19:32:54','2026-10-06 19:32:54'),(8,20,2,'distancia',18.30,NULL,NULL,NULL,83.40,NULL,NULL,'2026-10-05 16:30:59','2026-10-06 19:32:54','2026-10-06 19:32:54'),(9,20,1,'balanca',NULL,158.90,NULL,NULL,79.45,158.90,-7.90,'2026-10-05 18:30:54','2026-10-06 19:32:54','2026-10-06 19:32:54'),(10,20,2,'distancia',20.28,NULL,NULL,NULL,79.44,NULL,NULL,'2026-10-05 18:30:59','2026-10-06 19:32:54','2026-10-06 19:32:54'),(11,20,1,'balanca',NULL,151.30,NULL,NULL,75.65,151.30,-7.60,'2026-10-05 20:30:54','2026-10-06 19:32:54','2026-10-06 19:32:54'),(12,20,2,'distancia',22.18,NULL,NULL,NULL,75.64,NULL,NULL,'2026-10-05 20:30:59','2026-10-06 19:32:54','2026-10-06 19:32:54'),(13,20,1,'balanca',NULL,143.70,NULL,NULL,71.85,143.70,-7.60,'2026-10-05 22:30:54','2026-10-06 19:32:54','2026-10-06 19:32:54'),(14,20,2,'distancia',24.08,NULL,NULL,NULL,71.84,NULL,NULL,'2026-10-05 22:30:59','2026-10-06 19:32:54','2026-10-06 19:32:54'),(15,20,1,'balanca',NULL,135.20,NULL,NULL,67.60,135.20,-8.50,'2026-10-06 00:30:54','2026-10-06 19:32:54','2026-10-06 19:32:54'),(16,20,2,'distancia',26.20,NULL,NULL,NULL,67.60,NULL,NULL,'2026-10-06 00:30:59','2026-10-06 19:32:54','2026-10-06 19:32:54'),(17,20,1,'balanca',NULL,196.50,NULL,NULL,98.25,196.50,61.30,'2026-10-06 02:30:54','2026-10-06 19:32:54','2026-10-06 19:32:54'),(18,20,2,'distancia',10.88,NULL,NULL,NULL,98.24,NULL,NULL,'2026-10-06 02:30:59','2026-10-06 19:32:54','2026-10-06 19:32:54'),(19,20,1,'balanca',NULL,188.10,NULL,NULL,94.05,188.10,-8.40,'2026-10-06 04:30:54','2026-10-06 19:32:54','2026-10-06 19:32:54'),(20,20,2,'distancia',12.98,NULL,NULL,NULL,94.04,NULL,NULL,'2026-10-06 04:30:59','2026-10-06 19:32:54','2026-10-06 19:32:54'),(21,20,1,'balanca',NULL,179.60,NULL,NULL,89.80,179.60,-8.50,'2026-10-06 06:30:54','2026-10-06 19:32:54','2026-10-06 19:32:54'),(22,20,2,'distancia',15.10,NULL,NULL,NULL,89.80,NULL,NULL,'2026-10-06 06:30:59','2026-10-06 19:32:54','2026-10-06 19:32:54'),(23,20,1,'balanca',NULL,171.00,NULL,NULL,85.50,171.00,-8.60,'2026-10-06 08:30:54','2026-10-06 19:32:54','2026-10-06 19:32:54'),(24,20,2,'distancia',17.25,NULL,NULL,NULL,85.50,NULL,NULL,'2026-10-06 08:30:59','2026-10-06 19:32:54','2026-10-06 19:32:54'),(25,20,1,'balanca',NULL,162.20,NULL,NULL,81.10,162.20,-8.80,'2026-10-06 10:30:54','2026-10-06 19:32:54','2026-10-06 19:32:54'),(26,20,2,'distancia',19.45,NULL,NULL,NULL,81.10,NULL,NULL,'2026-10-06 10:30:59','2026-10-06 19:32:54','2026-10-06 19:32:54'),(27,20,1,'balanca',NULL,150.80,NULL,NULL,75.40,150.80,-11.40,'2026-10-06 12:30:54','2026-10-06 19:32:54','2026-10-06 19:32:54'),(28,20,2,'distancia',22.30,NULL,NULL,NULL,75.40,NULL,NULL,'2026-10-06 12:30:59','2026-10-06 19:32:54','2026-10-06 19:32:54'),(29,20,1,'balanca',NULL,138.90,NULL,NULL,69.45,138.90,-11.90,'2026-10-06 14:30:54','2026-10-06 19:32:54','2026-10-06 19:32:54'),(30,20,2,'distancia',25.28,NULL,NULL,NULL,69.44,NULL,NULL,'2026-10-06 14:30:59','2026-10-06 19:32:54','2026-10-06 19:32:54'),(31,20,1,'balanca',NULL,123.45,NULL,NULL,61.73,123.45,-15.45,'2026-10-06 16:30:54','2026-10-06 19:32:54','2026-10-06 19:32:54'),(32,20,2,'distancia',29.14,NULL,NULL,NULL,61.72,NULL,NULL,'2026-10-06 16:30:59','2026-10-06 19:32:54','2026-10-06 19:32:54');
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
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lote_consumo_snapshots`
--

LOCK TABLES `lote_consumo_snapshots` WRITE;
/*!40000 ALTER TABLE `lote_consumo_snapshots` DISABLE KEYS */;
INSERT INTO `lote_consumo_snapshots` VALUES (7,9,NULL,'2026-08-25',65,0,'status-warning','2026-08-25 16:55:51','2026-08-25 16:55:51'),(8,11,7,'2026-08-25',899,30,'status-success','2026-08-25 17:10:04','2026-08-25 17:10:04'),(9,10,NULL,'2026-08-25',100,0,'status-warning','2026-08-25 17:39:08','2026-08-25 17:39:08'),(10,12,NULL,'2026-08-31',600,0,'status-warning','2026-08-31 16:18:09','2026-08-31 16:18:09'),(11,13,NULL,'2026-08-31',300,0,'status-warning','2026-08-31 16:18:10','2026-08-31 16:18:10'),(70,23,NULL,'2026-09-22',40,11,'status-warning','2026-09-22 17:11:16','2026-09-22 17:11:16'),(71,24,NULL,'2026-09-22',10,13,'status-warning','2026-09-22 17:11:16','2026-09-22 17:11:16'),(72,25,NULL,'2026-09-22',100,5,'status-warning','2026-09-22 17:11:16','2026-09-22 17:11:16'),(73,26,NULL,'2026-09-22',20,0,'status-warning','2026-09-22 17:33:22','2026-09-22 17:33:22'),(74,23,NULL,'2026-09-28',40,11,'status-warning','2026-09-28 13:42:32','2026-09-28 13:42:32'),(75,24,NULL,'2026-09-28',10,13,'status-warning','2026-09-28 13:42:32','2026-09-28 13:42:32'),(76,25,NULL,'2026-09-28',100,5,'status-warning','2026-09-28 13:42:32','2026-09-28 13:42:32'),(77,26,NULL,'2026-09-28',20,0,'status-warning','2026-09-28 13:42:32','2026-09-28 13:42:32'),(78,27,NULL,'2026-09-28',100,15,'status-warning','2026-09-28 13:46:50','2026-09-28 13:46:50'),(79,23,NULL,'2026-09-29',40,11,'status-warning','2026-09-29 13:22:43','2026-09-29 13:22:43'),(80,24,NULL,'2026-09-29',10,13,'status-warning','2026-09-29 13:22:43','2026-09-29 13:22:43'),(81,25,NULL,'2026-09-29',100,5,'status-warning','2026-09-29 13:22:43','2026-09-29 13:22:43'),(82,26,NULL,'2026-09-29',20,0,'status-warning','2026-09-29 13:22:43','2026-09-29 13:22:43'),(83,27,NULL,'2026-09-29',99,15,'status-warning','2026-09-29 13:22:43','2026-09-29 21:06:12'),(84,28,19,'2026-09-29',300,20,'status-success','2026-09-29 13:41:52','2026-09-29 13:41:52'),(85,29,20,'2026-09-29',50,30,'status-success','2026-09-29 14:05:15','2026-09-29 14:12:31'),(86,31,NULL,'2026-09-29',79,0,'status-warning','2026-09-29 14:05:15','2026-09-29 14:05:15'),(87,30,22,'2026-09-29',30,50,'status-success','2026-09-29 14:05:15','2026-09-29 14:19:17'),(88,33,21,'2026-09-29',200,30,'status-success','2026-09-29 14:14:56','2026-09-29 14:14:56'),(89,23,NULL,'2026-10-05',40,11,'status-success','2026-10-05 14:21:29','2026-10-05 15:59:31'),(90,24,NULL,'2026-10-05',10,13,'status-warning','2026-10-05 14:21:29','2026-10-05 14:21:29'),(91,25,NULL,'2026-10-05',100,5,'status-warning','2026-10-05 14:21:29','2026-10-05 14:21:29'),(92,26,NULL,'2026-10-05',20,0,'status-warning','2026-10-05 14:21:29','2026-10-05 14:21:29'),(93,27,NULL,'2026-10-05',99,15,'status-success','2026-10-05 14:21:29','2026-10-05 16:00:11'),(94,23,NULL,'2026-10-06',40,25,'status-warning','2026-10-06 13:40:11','2026-10-06 15:40:27'),(95,24,NULL,'2026-10-06',10,0,'status-warning','2026-10-06 13:40:11','2026-10-06 15:40:27'),(96,25,NULL,'2026-10-06',100,0,'status-warning','2026-10-06 13:40:11','2026-10-06 15:40:27'),(97,26,NULL,'2026-10-06',20,0,'status-warning','2026-10-06 13:40:11','2026-10-06 13:40:11'),(98,27,NULL,'2026-10-06',99,25,'status-warning','2026-10-06 13:40:11','2026-10-06 15:40:27'),(99,35,NULL,'2026-10-06',30,0,'status-warning','2026-10-06 17:22:40','2026-10-06 17:22:40'),(100,36,27,'2026-10-19',80,810,'status-success','2026-10-19 14:29:27','2026-10-19 19:35:11'),(101,37,26,'2026-10-19',120,850,'status-success','2026-10-19 16:55:36','2026-10-19 19:33:03'),(102,38,28,'2026-10-19',10,70,'status-success','2026-10-19 19:22:22','2026-10-19 19:30:52'),(103,39,NULL,'2026-10-19',80,0,'status-warning','2026-10-19 19:22:22','2026-10-19 19:22:22'),(104,40,NULL,'2026-10-19',40,0,'status-warning','2026-10-19 19:22:22','2026-10-19 19:22:22'),(105,41,29,'2026-10-19',40,490,'status-success','2026-10-19 19:22:22','2026-10-19 19:40:37');
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lote_ocorrencias`
--

LOCK TABLES `lote_ocorrencias` WRITE;
/*!40000 ALTER TABLE `lote_ocorrencias` DISABLE KEYS */;
INSERT INTO `lote_ocorrencias` VALUES (1,9,'morte','Predador',NULL,5,70,65,NULL,'2026-08-25',NULL,'2026-08-25 16:54:58','2026-08-25 16:54:58'),(11,31,'doenca',NULL,'Gripe Aviária',1,80,79,NULL,'2026-09-29','medicados','2026-09-29 14:02:47','2026-09-29 14:02:47'),(14,27,'morte','Causa natural',NULL,1,100,99,NULL,'2026-09-29',NULL,'2026-09-29 21:04:31','2026-09-29 21:04:31');
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
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lotes`
--

LOCK TABLES `lotes` WRITE;
/*!40000 ALTER TABLE `lotes` DISABLE KEYS */;
INSERT INTO `lotes` VALUES (2,4,NULL,NULL,'Lote A5','Bovino',NULL,'Leite','Fêmea','Acima de 3 anos',20,1,600.00,'2026-08-25','Ativo','2026-08-25 14:03:08','2026-08-25 14:03:08'),(8,5,NULL,NULL,'lote 1','Bovino',NULL,'Leite','Fêmea','Acima de 3 anos',200,1,370.00,'2026-08-25','Ativo','2026-08-25 14:14:40','2026-08-25 14:14:40'),(9,8,NULL,NULL,'arthur','Bovino',NULL,'Leite','Fêmea','0-6 meses',65,0,15.00,'2026-08-25','Ativo','2026-08-25 16:53:24','2026-08-25 16:54:58'),(10,9,NULL,NULL,'12','Bovino',NULL,'Reprodução','Misto','1-2 anos',100,1,44.98,'2026-08-25','Ativo','2026-08-25 16:54:33','2026-08-25 16:54:33'),(11,11,8,NULL,'LOTE 1','Bovino',NULL,'Corte','Macho','6-12 meses',899,1,555.00,'2026-08-25','Ativo','2026-08-25 17:06:04','2026-08-25 17:52:34'),(12,12,NULL,NULL,'Lote A1','Bovino',NULL,'Corte','Macho','1-2 anos',600,0,860.00,'2026-08-31','Ativo','2026-08-31 16:13:04','2026-08-31 16:13:04'),(13,12,NULL,NULL,'Lote A2','Bovino',NULL,'Leite','Fêmea','6-12 meses',300,0,690.00,'2026-08-31','Ativo','2026-08-31 16:15:16','2026-08-31 16:15:16'),(23,1,NULL,NULL,'Lote A1','Bovino','Nelore','Leite','Fêmea','1-2 anos',40,10,478.00,'2026-09-22','Ativo','2026-09-22 17:03:13','2026-10-05 15:59:29'),(24,1,NULL,NULL,'Lote B1','Equino','Manga Larga','Esporte','Macho','2-3 anos',10,4,780.00,'2026-09-22','Ativo','2026-09-22 17:06:08','2026-09-22 17:06:08'),(25,1,NULL,NULL,'Lote C1','Avicultura','Leghorn','Ovos','Fêmea','6-12 meses',100,25,3.00,'2026-09-22','Ativo','2026-09-22 17:10:36','2026-09-22 17:10:37'),(26,1,NULL,NULL,'Lote D1','Ovino','Santa Inês','Leite','Fêmea','1-2 anos',20,5,112.00,'2026-09-22','Ativo','2026-09-22 17:16:59','2026-09-22 17:16:59'),(27,1,NULL,NULL,'Lote A2','Bovino','Nelore','Corte','Macho','2-3 anos',99,30,890.00,'2026-09-28','Ativo','2026-09-28 13:44:47','2026-10-05 16:00:09'),(28,18,19,NULL,'a1','Bovino','nle','Reprodução','Fêmea','1-2 anos',300,3,350.00,'2026-09-29','Ativo','2026-09-29 13:38:32','2026-09-29 13:41:21'),(29,21,20,NULL,'Lote A1','Bovino','..','Corte','Macho','0-6 meses',50,10,500.00,'2026-09-29','Ativo','2026-09-29 13:55:15','2026-09-29 14:09:20'),(30,23,22,NULL,'Lote A2','Suíno','..','Reprodução','Fêmea','6-12 meses',30,1,200.00,'2026-09-29','Inativo','2026-09-29 13:57:02','2026-09-29 14:18:57'),(31,22,NULL,NULL,'Lote A3','Avicultura','..','Ovos','Fêmea','6-12 meses',79,12,8.00,'2026-09-29','Ativo','2026-09-29 14:00:46','2026-09-29 14:02:47'),(33,25,21,NULL,'lote 1','Bovino','Nelore','Reprodução','Misto','Acima de 3 anos',200,10,400.00,'2026-09-29','Ativo','2026-09-29 14:12:19','2026-09-29 14:14:42'),(34,26,NULL,NULL,'lote 2','Bovino','Nelore','Leite','Fêmea','2-3 anos',500,16,300.00,'2026-09-29','Ativo','2026-09-29 14:29:48','2026-09-29 14:29:49'),(35,1,NULL,NULL,'Lote F1','Caprino','Holandês','Leite','Fêmea','1-2 anos',30,8,109.00,'2026-10-06','Ativo','2026-10-06 16:50:15','2026-10-06 16:50:15'),(36,30,27,NULL,'Lote A1','Bovino','Nelore','Leite','Fêmea','1-2 anos',80,12,740.00,'2026-10-19','Ativo','2026-10-19 14:22:16','2026-10-19 19:27:29'),(37,30,26,NULL,'Lote A2','Bovino','Nelore','Corte','Macho','2-3 anos',120,24,800.00,'2026-10-19','Ativo','2026-10-19 14:32:52','2026-10-19 19:27:39'),(38,30,28,NULL,'Lote B1','Equino','Mangalarga Machador','Esporte','Macho','2-3 anos',10,2,880.00,'2026-10-19','Ativo','2026-10-19 19:13:08','2026-10-19 19:38:14'),(39,30,NULL,NULL,'Lote C1','Avicultura','Rhode Island Red','Ovos','Fêmea','6-12 meses',80,5,6.00,'2026-10-19','Ativo','2026-10-19 19:15:00','2026-10-19 19:20:06'),(40,30,NULL,NULL,'Lote D1','Caprino','Saanen','Leite','Fêmea','1-2 anos',40,5,90.00,'2026-10-19','Ativo','2026-10-19 19:16:31','2026-10-19 19:16:31'),(41,30,29,NULL,'Lote F1','Suíno','Large White','Corte','Macho','2-3 anos',40,8,380.00,'2026-10-19','Ativo','2026-10-19 19:19:00','2026-10-19 19:36:32');
/*!40000 ALTER TABLE `lotes` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026_08_25_110000_create_comentarios_table',1),(2,'2026_08_25_120000_add_nota_to_comentarios_table',2),(3,'2026_08_25_130000_add_categorias_to_comentarios_table',3),(4,'2026_08_25_140000_make_comentarios_publicos',4),(5,'2026_08_25_150000_add_estoque_total_to_racaos_table',5),(16,'2026_09_01_120000_create_racao_estoque_snapshots_table',9),(17,'2026_08_31_150400_create_ordenhas_table',10),(18,'0001_01_01_000000_create_users_table',11),(19,'0001_01_01_000001_create_cache_table',11),(20,'0001_01_01_000002_create_jobs_table',11),(21,'2026_08_04_180125_add_cpf_and_foto_to_cadastros_table',11),(22,'2026_08_04_181346_widen_telefone_on_cadastros_table',11),(23,'2026_08_04_183250_add_data_nascimento_and_seguranca_to_cadastros_table',11),(24,'2026_08_10_142801_add_endereco_to_fazendas_table',11),(25,'2026_08_10_152846_add_coordenadas_e_proprietario_to_fazendas_table',11),(26,'2026_08_10_161818_add_finalidade_sexo_idade_media_to_lotes_table',11),(27,'2026_08_10_181631_add_status_to_lotes_table',11),(28,'2026_08_11_120124_add_fazenda_id_to_racoes_table',11),(29,'2026_08_11_141827_create_lote_ocorrencias_table',11),(30,'2026_08_11_154706_add_doenca_fields_to_lote_ocorrencias_table',11),(31,'2026_08_11_160404_add_lote_origem_id_to_lotes_table',11),(32,'2026_08_11_163135_add_causa_to_lote_ocorrencias_table',11),(33,'2026_08_11_172500_add_valor_kg_to_racaos_table',11),(34,'2026_08_11_175642_add_sexo_idade_to_racaos_table',11),(35,'2026_08_11_180426_add_finalidade_to_racaos_table',11),(36,'2026_08_17_082800_create_codigos_verificacao_table',11),(37,'2026_08_17_102301_create_planos_table',11),(38,'2026_08_24_120000_add_racao_id_to_lotes_table',11),(39,'2026_08_24_130000_create_lote_consumo_snapshots_table',11),(40,'2026_08_25_090000_create_acessibilidade_preferencias_table',11),(41,'2026_08_25_091000_create_desempenho_lotes_table',11),(42,'2026_08_25_102713_create_personal_access_tokens_table',12),(43,'2026_09_08_090000_add_peso_e_quantidade_to_desempenho_lotes_table',12),(44,'2026_09_08_100000_add_email_verificado_em_to_cadastros_table',12),(45,'2026_09_08_110000_add_raca_to_lotes_table',13),(46,'2026_09_08_120000_add_quantidade_cochos_to_lotes_table',14),(47,'2026_09_08_120100_reestruturar_cochos_para_geracao_automatica',14),(48,'2026_08_01_000000_create_tabelas_base_agrifarm',15),(49,'2026_10_05_100000_corrigir_estrutura_agrifarm',15),(50,'2026_10_05_110000_corrigir_tipos_acessibilidade_preferencias',16),(51,'2026_10_05_140000_add_nutricao_e_controle_to_racaos_table',17),(52,'2026_10_06_100000_create_sensores_e_leituras_iot',18),(53,'2026_10_06_150000_espacar_numero_no_nome_dos_cochos',19),(54,'2026_10_06_150100_remover_fornecedor_reposicao_e_data_entrada',19),(55,'2026_10_20_100000_add_presenca_animal_to_leituras_iot_table',20),(56,'2026_10_20_110000_create_cocho_consumos_table',21),(57,'2026_10_20_120000_add_data_entrada_fornecedor_e_localizacao',22),(58,'2026_10_20_130000_restricoes_quantidades_nao_negativas',23);
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
  `turno` varchar(255) NOT NULL,
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
  CONSTRAINT `ordenhas_litros_positivos` CHECK (`quantidade_litros` > 0)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ordenhas`
--

LOCK TABLES `ordenhas` WRITE;
/*!40000 ALTER TABLE `ordenhas` DISABLE KEYS */;
INSERT INTO `ordenhas` VALUES (6,23,'2026-09-29','09:00:00','Manhã',30.00,1,NULL,'2026-09-29 14:04:12','2026-09-29 14:04:12'),(7,34,'2026-09-29','08:00:00','Manhã',120.00,16,'mafalda está machucada','2026-09-29 14:32:12','2026-09-29 14:32:12'),(8,26,'2026-10-06','10:50:00','Manhã',120.50,1,NULL,'2026-10-06 16:50:44','2026-10-06 16:50:44'),(9,23,'2026-10-06','11:00:00','Manhã',105.00,1,NULL,'2026-10-06 16:51:17','2026-10-06 16:51:17'),(12,36,'2026-10-19','13:43:00','Tarde',220.00,20,NULL,'2026-10-19 19:43:57','2026-10-19 19:43:57');
/*!40000 ALTER TABLE `ordenhas` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (4,'App\\Models\\Cadastro',1,'app-flutter','146451e98a07a838175afb1aa2eaa27768af680275daa63a6639a48f6cb173d6','[\"*\"]','2026-09-21 20:30:48',NULL,'2026-09-21 19:48:22','2026-09-21 20:30:48'),(9,'App\\Models\\Cadastro',1,'app-flutter','6e9db1c49d933942f2ecd0ebe1d9b6d660e82850d10f422a29a05a4c12a9900b','[\"*\"]','2026-09-22 15:57:13',NULL,'2026-09-22 14:04:20','2026-09-22 15:57:13'),(11,'App\\Models\\Cadastro',1,'app-flutter','7fc80f6910191c8e47bb75a23e0cdee2b4136ab9ef5dd2462c37a49d3b93ca0e','[\"*\"]','2026-09-22 17:23:16',NULL,'2026-09-22 16:22:24','2026-09-22 17:23:16'),(12,'App\\Models\\Cadastro',1,'app-flutter','70b70ce1c3a786bb62a5505db1539c1780faea81d21a70b2998e19a6e9d1a80a','[\"*\"]','2026-09-22 17:31:00',NULL,'2026-09-22 17:29:49','2026-09-22 17:31:00'),(13,'App\\Models\\Cadastro',1,'app-flutter','a90e1b5846b1b1beb4072f2e6b5c8f472948eaec9fbe7e3c5c52e3f7b8748401','[\"*\"]','2026-09-22 19:05:03',NULL,'2026-09-22 19:04:35','2026-09-22 19:05:03'),(14,'App\\Models\\Cadastro',1,'app-flutter','5b688f2ea40bf98b91aa2831fb162ece3de9667e2b7c77130c49b4f42be8840d','[\"*\"]','2026-09-28 16:30:50',NULL,'2026-09-28 16:30:09','2026-09-28 16:30:50'),(15,'App\\Models\\Cadastro',1,'app-flutter','0cdd934aed7db5d3448ad30aebfe6188bde5977f90851b129bb23b39d9799895','[\"*\"]','2026-09-28 16:32:59',NULL,'2026-09-28 16:31:40','2026-09-28 16:32:59'),(16,'App\\Models\\Cadastro',1,'app-flutter','ae6e313b0e39c516da2962dd9f4c0da32a70aad15b0551070c951856b3697088','[\"*\"]',NULL,NULL,'2026-09-29 13:26:33','2026-09-29 13:26:33'),(18,'App\\Models\\Cadastro',1,'app-flutter','200944805ebffd9349fc11dfb27262e4f5d1b4a936e690ad6486fe909eede889','[\"*\"]','2026-09-29 14:34:06',NULL,'2026-09-29 14:31:36','2026-09-29 14:34:06'),(19,'App\\Models\\Cadastro',1,'app-flutter','48749de8969dac397f874480c18103c5f1a7fae04a87dc147434196f71f4f51a','[\"*\"]','2026-09-29 14:43:26',NULL,'2026-09-29 14:42:53','2026-09-29 14:43:26'),(20,'App\\Models\\Cadastro',1,'app-flutter','181e01985e67e1b471e9473e3164f28835d7802d2adf4d87d9f16991ecb80769','[\"*\"]','2026-09-29 14:53:47',NULL,'2026-09-29 14:48:06','2026-09-29 14:53:47'),(21,'App\\Models\\Cadastro',17,'app-flutter','415c743f4653d0eec1441f2ffeeefd771fd359176f43cf877e2221870c70f271','[\"*\"]','2026-09-29 17:12:20',NULL,'2026-09-29 14:50:12','2026-09-29 17:12:20'),(29,'App\\Models\\Cadastro',1,'app-flutter','b31b401096d42d9265a128e56d5eb85a1a0dba0e64c8de637950b69a903daa97','[\"*\"]','2026-09-29 17:44:32',NULL,'2026-09-29 16:55:43','2026-09-29 17:44:32'),(31,'App\\Models\\Cadastro',1,'app-flutter','f501ac5cff6c178a4fde3a1c46c783e164d1d7ab67e2862cacca76e6f0beffe2','[\"*\"]','2026-09-29 17:45:17',NULL,'2026-09-29 17:45:00','2026-09-29 17:45:17'),(32,'App\\Models\\Cadastro',1,'app-flutter','5056a649d5ea0fa9db2fe3555bc5718742ca1be51b7f541cdaacce04fe36fe0c','[\"*\"]','2026-09-29 17:58:15',NULL,'2026-09-29 17:45:36','2026-09-29 17:58:15'),(33,'App\\Models\\Cadastro',1,'app-flutter','7d706e6f0838c3eade1600e2f4d3b6f56cc223e6047d49954f4560f09d0b3cc0','[\"*\"]','2026-09-29 20:02:49',NULL,'2026-09-29 19:06:29','2026-09-29 20:02:49'),(36,'App\\Models\\Cadastro',1,'app-flutter','6d0e4d9bd2acdb4ee14be2008e1c33f823adbe3940a4a9b35a0befa19b2c08fe','[\"*\"]','2026-09-29 20:26:30',NULL,'2026-09-29 20:06:13','2026-09-29 20:26:30'),(38,'App\\Models\\Cadastro',1,'app-flutter','725152d60ccec33f44287655182c336f711ba5f20a664e42cdb75f6977eb8b60','[\"*\"]','2026-09-29 21:06:12',NULL,'2026-09-29 21:01:49','2026-09-29 21:06:12'),(39,'App\\Models\\Cadastro',1,'app-flutter','8204705d11ff26d2c4c7165cfc3e66b8b3623907094e291cccb5012ddefa1347','[\"*\"]','2026-10-05 15:08:17',NULL,'2026-10-05 15:03:22','2026-10-05 15:08:17'),(40,'App\\Models\\Cadastro',1,'app-flutter','688fc45954281f92b6b9a0a0dd45dc84a78ec806634df0fb5239a840809a1eb0','[\"*\"]','2026-10-05 15:44:02',NULL,'2026-10-05 15:34:36','2026-10-05 15:44:02'),(41,'App\\Models\\Cadastro',1,'app-flutter','79c4462a7bc733efe6e0c0b00a0d34237a5250cfdf6b2ceb27c99b2735f80565','[\"*\"]','2026-10-05 15:57:54',NULL,'2026-10-05 15:52:38','2026-10-05 15:57:54'),(45,'App\\Models\\Cadastro',1,'app-flutter','829d86170769bd41d15e3dc472336784224098d3a0c312f2da0dade89827e6f1','[\"*\"]','2026-10-05 16:23:55',NULL,'2026-10-05 16:23:36','2026-10-05 16:23:55'),(46,'App\\Models\\Cadastro',1,'app-flutter','2eeac352c424eaacfb02efbf2695853e3761583526943b7afac4ca1faaab90a0','[\"*\"]','2026-10-05 17:50:34',NULL,'2026-10-05 17:43:50','2026-10-05 17:50:34'),(47,'App\\Models\\Cadastro',1,'app-flutter','e2fec59477039b907b36b71331a0aac888b4e23767a19529c29026c09df39932','[\"*\"]','2026-10-05 20:49:09',NULL,'2026-10-05 20:16:55','2026-10-05 20:49:09'),(48,'App\\Models\\Cadastro',1,'app-flutter','781e40a09a765cce694b2beed54b418d771cf3fd8383f882064e34818a6aeacb','[\"*\"]','2026-10-06 17:22:42',NULL,'2026-10-06 17:22:19','2026-10-06 17:22:42'),(49,'App\\Models\\Cadastro',1,'app-flutter','0fe7ff332173f09bd4e7e5ed5a1900b06ec17924bd26b81390e8a70f0960aa30','[\"*\"]','2026-10-06 19:37:53',NULL,'2026-10-06 17:23:27','2026-10-06 19:37:53'),(51,'App\\Models\\Cadastro',20,'app-flutter','5c54b3d90b3c961e2396606d13a36c07b7d6f19f8b745f25fc3b791831ab07f4','[\"*\"]','2026-10-19 20:13:59',NULL,'2026-10-19 20:13:11','2026-10-19 20:13:59'),(52,'App\\Models\\Cadastro',20,'app-flutter','3c1b63047a3281ec80c167a9fd6b206e653c92cf91dca57bf7302084cb4ac7b2','[\"*\"]','2026-10-19 20:18:30',NULL,'2026-10-19 20:15:52','2026-10-19 20:18:30');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `planos`
--

LOCK TABLES `planos` WRITE;
/*!40000 ALTER TABLE `planos` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `racaos`
--

LOCK TABLES `racaos` WRITE;
/*!40000 ALTER TABLE `racaos` DISABLE KEYS */;
INSERT INTO `racaos` VALUES (4,5,'Bovino','Fêmea','Acima de 3 anos','Leite',NULL,'kg','raçao magnus',500.00,1000.00,25.00,90.00,NULL,'2026-08-25 14:16:17','2026-08-25 14:16:17',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(7,11,'Bovino','Macho','6-12 meses','Corte',NULL,'kg','Ração Premium',500.00,NULL,30.00,3.00,NULL,'2026-08-25 17:09:19','2026-08-25 17:09:19',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(8,11,'Bovino','Macho','6-12 meses','Corte',NULL,'kg','Ração Premium 2',222.00,NULL,22.00,222.00,NULL,'2026-08-25 17:52:34','2026-08-25 17:52:34',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(12,NULL,'Bovino','Fêmea','2-3 anos','Leite',NULL,'kg','R1',100.00,500.00,10.00,2.00,NULL,'2026-09-21 19:05:06','2026-09-21 19:05:06',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(13,NULL,'Bovino','Fêmea','2-3 anos','Leite',NULL,'kg','R1',100.00,500.00,10.00,2.00,NULL,'2026-09-21 19:12:01','2026-09-21 19:12:01',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(14,18,'Bovino','Fêmea','1-2 anos','Reprodução',NULL,'kg','premio',500.00,1000.00,20.00,2.40,NULL,'2026-09-29 13:41:17','2026-09-29 13:41:17',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(15,18,'Bovino','Fêmea','1-2 anos','Reprodução',NULL,'kg','premio',500.00,1000.00,20.00,2.40,NULL,'2026-09-29 13:41:18','2026-09-29 13:41:18',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(16,18,'Bovino','Fêmea','1-2 anos','Reprodução',NULL,'kg','premio',500.00,1000.00,20.00,2.40,NULL,'2026-09-29 13:41:19','2026-09-29 13:41:19',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(17,18,'Bovino','Fêmea','1-2 anos','Reprodução',NULL,'kg','premio',500.00,1000.00,20.00,2.40,NULL,'2026-09-29 13:41:19','2026-09-29 13:41:19',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(18,18,'Bovino','Fêmea','1-2 anos','Reprodução',NULL,'kg','premio',500.00,1000.00,20.00,2.40,NULL,'2026-09-29 13:41:20','2026-09-29 13:41:20',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(19,18,'Bovino','Fêmea','1-2 anos','Reprodução',NULL,'kg','premio',500.00,1000.00,20.00,2.40,NULL,'2026-09-29 13:41:21','2026-09-29 13:41:21',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(20,21,'Bovino','Macho','0-6 meses','Corte',NULL,'kg','Ração Magnus',180.00,400.00,30.00,6.00,NULL,'2026-09-29 14:09:20','2026-09-29 14:38:40',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(21,25,'Bovino','Misto','Acima de 3 anos','Reprodução',NULL,'kg','raçao magnus',200.00,600.00,30.00,2.30,NULL,'2026-09-29 14:14:42','2026-09-29 14:14:42',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(22,23,'Suíno','Fêmea','6-12 meses','Reprodução',NULL,'kg','Ração Premiun',130.00,400.00,50.00,5.00,NULL,'2026-09-29 14:18:57','2026-09-29 14:25:12',NULL,NULL,NULL,NULL,NULL,'ativa',NULL),(23,1,'Bovino',NULL,'0-6 meses',NULL,NULL,'kg','Ração Bezerros Inicial',100.00,199.98,25.00,1.50,NULL,'2026-10-06 15:35:20','2026-10-06 15:35:20',20.00,3.00,8.00,NULL,NULL,'ativa','Fase inicial (0 a 6 meses): fácil digestão para o desenvolvimento do rúmen.'),(26,30,'Bovino','Macho','2-3 anos','Corte',NULL,'kg','Ração Bovino Corte',3000.00,3000.00,850.00,2.50,NULL,'2026-10-19 14:26:54','2026-10-19 19:32:42',14.00,2.90,12.00,NULL,NULL,'ativa','Engorda e terminação: energia elevada para ganho de peso rápido.'),(27,30,'Bovino','Fêmea','1-2 anos','Leite',NULL,'kg','Ração Bovino Leite',3000.00,3000.00,810.00,1.00,NULL,'2026-10-19 14:35:01','2026-10-19 19:33:43',22.00,1.75,16.00,NULL,NULL,'ativa','Para vacas em lactação: proteína alta para sustentar a produção de leite.'),(28,30,'Equino','Macho','2-3 anos','Esporte',NULL,'kg','Ração Equino Esporte',2000.00,2000.00,70.00,3.00,NULL,'2026-10-19 19:25:23','2026-10-19 19:30:34',13.00,3.00,12.00,NULL,NULL,'ativa','Alto desempenho: gordura e eletrólitos para treino e competição.'),(29,30,'Suíno','Macho','2-3 anos','Corte',NULL,'kg','Ração Suíno Terminação',4000.00,4000.00,490.00,1.20,NULL,'2026-10-19 19:34:38','2026-10-19 19:37:23',16.00,3.25,4.00,NULL,NULL,'ativa','Crescimento e terminação: energia e aminoácidos para carcaça magra.');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sensores`
--

LOCK TABLES `sensores` WRITE;
/*!40000 ALTER TABLE `sensores` DISABLE KEYS */;
INSERT INTO `sensores` VALUES (1,20,'TESTE-BAL-999','balanca',NULL,NULL,1,'2026-10-06 16:30:54',123.45,NULL,NULL,'2026-10-06 19:32:54','2026-10-06 19:32:54'),(2,20,'TESTE-DIST-999','distancia',60.00,10.00,1,'2026-10-06 16:30:59',29.14,NULL,NULL,'2026-10-06 19:32:54','2026-10-06 19:32:54');
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
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('0cAd6dX4WOBRRtlYP7Gsnvs4qoo8gYMxHjYWXZcl',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoicERoU2hqRlFOZFdGNWpCZFZoMDI4UWh0M3Z2Z2REUzFjZUtKWGxYUyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433438),('0RfP08B0EGHmp4nExmXknYng3OqQAxWiqSeS8GHh',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTXpRYVN3aGxZNWhJWVgwOGZqYVdHcmNadXlvanhWWWV3Y1NYUUFVbCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431186),('1Ib3SnYbOWtGRPaJM6jFltX6jZVj6iIDMvEyqpc2',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoia2oxS1BoaVhNcXBLVkR6TnR6d2hhV2gwSkFoQjhiNVZoeVpmcFlKMiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433430),('1JoUBEA8FtaEVRPuzmZSTOUzBlPEEz8omtiSm3Fh',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNm1xV0F1b21ZNEZwdm54cmxhMEo0aGFzSXFoOTZzMEpVYjFQM0h0MCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792434435),('1QQNEweXXFCaOVA8qJzfJvObVzdlAPId2WD4w9YV',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNTJSZEE2dVg3OU80VElsY1QyVEtoV2Y0RXBTemNjOWpUaGpZWDBCYiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432606),('1TylGQAq1r2nOB6ZWphTjeclMKqAVnbGqa7M0o2k',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRDdmeWdDRUJFTDJrMkdETWsxTWhZT0lBVURzQkEwRk9pVUFYUzZDOCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvZXNxdWVjZXUtc2VuaGEiO3M6NToicm91dGUiO3M6MTY6InBhc3N3b3JkLnJlcXVlc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433869),('1xf8ovdk4KNu22yGI27SHMNxW4arLNaPyrL3Ogm2',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWHlaT2s3djh4WkM5djF4WGExSk5YbG1HekdLQThSbzZ3RHpYdVIwWSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvcHJvYmxlbWEiO3M6NToicm91dGUiO3M6MTY6ImxhbmRpbmcucHJvYmxlbWEiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432701),('2dM7clOLlXTIuW92p1JmQoX1UYwAM84wjTDavCBG',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOFEyTEdxZ0tKVmFraEU0QUNNUnM3TnpleEZORGtORFRWZ3ZEempOSCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431032),('2Hcu5qP5a3dNuOkDMxBFdOkADxiRsmpn40RbPYfF',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiYmFhZTU2NE1pRXQwcFVrRFJsazRqcGZ3dXl3NDFCUUluamdvSWR4RCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431214),('2wmBEaxqEhQtlgQzQrS6mR5TFW1e8LUwx5r1R7QM',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWFQ1SWtiN0RTY0QzaW5MZU8xZEh6VjNYZ1h3dXZ6RHZKekpzU0V2ayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433652),('3bxE9xOtPLCFSbDTb1t7E7pDkFdqPSEMh06r3kgD',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiV0tmaDRVRmt0OThpOXlBaUs5MUp5bk9vT2ZnNFZyZHZKMWlyc1d4VSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvY2FkYXN0cm8iO3M6NToicm91dGUiO3M6MTc6ImNhZGFzdHJvLmNhZGFzdHJvIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792433782),('3Jv4SnH6lJpv1jGciDTGGUyBgmw8RuahhErZtjDt',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiYTdjMzNVUURMclBuUllxcEYxNTlzcFU1OU4wRFNnUkRnUlJ4TU5BTSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433100),('3KLtJt0QpVGcuxNvsIgjWcRAB420WuH38br0mbnH',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoid3dmSmk4TlBsQ3VNc25sRU9qbTNValliREZuQ1J4VU1MclNsOVl2cSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433610),('3XKFJongNuPg6tI7XS0kgYA0N1ltJc09cXad2Tar',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWUJnUjJka1g3UzFSbno4RW5kRTkyMDlIY3luT3Z4VGNLVUZpU0pNQSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvY2FkYXN0cm8iO3M6NToicm91dGUiO3M6MTc6ImNhZGFzdHJvLmNhZGFzdHJvIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792433778),('4HLkHxMvNKe55UgfTIHquCySbh4uOPqL4h5iMtLa',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZU05WWFURHF6d09kWTdqMFdXOVRFTEVjVDc2ekpHYVN3eTZ4VzFlWiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433236),('4IocoV78yz5uYbYvsA1CNzlKXj5gWLvhctfGZmQ7',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUzcwTXE2dkxHU0dmTmt0OWdMT0x4ZnQzWGZUN0VmeVhvVGZrcWdlSyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433495),('4xraAgw2oXYIMvB62m9FOu6kUXJFTZUvAHK9rUjX',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoicUE1SUdoaDVBRWpEWmZKQ1FncmNncUZuTWdxSnduRVVHWmJlRkdwQiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433227),('5pD7lsqFZX7KIgK9Cilc5bWNqc3NlY0P1cr9qkx5',NULL,'10.141.131.99','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36 Edg/155.0.0.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiem1ySmU2ZUR6YjZkTDBObW9HM3Z4MkhUSW5lSnFINm1HTW13MzVGVyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHA6Ly8xMC4xNDEuMTMxLjk5OjgwMDAvYW5pbWFpcy1wb3ItbG90ZSI7czo1OiJyb3V0ZSI7czoyMjoicHJvamV0by5hbmltYWlzUG9yTG90ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1792496012),('5WN9vNJN1rMZuLmQjQoeWate8AdQSIWyHYcJ5RY3',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQlUwdE41WXNLQW8xZE9LM2VOMmVJNGhqNmRzUTFGZ1FEaUM3MGdNayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvY2FkYXN0cm8iO3M6NToicm91dGUiO3M6MTc6ImNhZGFzdHJvLmNhZGFzdHJvIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792433803),('673ru2CMQh31CwWjWf2CQJHAkyReZOaxAmVAGNTe',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTTcxekI3NlF6RFFpOFdHbHFCQXA1VWltc1VXM3JoVm1iaEE0TzVEZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431227),('6a4nlcW3DlwVg00HvH3vTYggaKhXsVbwTAbghjdT',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRFRmR0dNYzR3VW45Y0xrR1VueHlzTGNJcDNGSHZPdjVJUGpOVlh3UyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvZXNxdWVjZXUtc2VuaGEiO3M6NToicm91dGUiO3M6MTY6InBhc3N3b3JkLnJlcXVlc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433914),('6B1HerFnqLn4ZjbItqZp8cUVq4UvDG57Bgmb7arF',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiSXhOQm8yN2Z5RmZ5ZzFPMERHZUpMaWlCcHNYQkNHaDZlamZyR3J2TSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431131),('6N4KiiQQbV0Pof2BXCCGQXAI9cURckeVSi5KPTpl',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRHNqSWdaNVpqbWNxalJDcHlhWU5oVVlXYU10QVVweFQwMUdqTmlTdyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433138),('6Q6Wl1xS9QWxhfeHPLlH1haQkvT0D8NaUTLDlxR9',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUnFVYklIUE9LcElHZmZ2TmJKc0QzdndibktMdG1Rc3lORGM3SlVhZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6MTE6ImxvZ2luLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792432796),('6WjmtXoRDF4DpLb5mgQGs71XxU8GPIQmntzGkUKP',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRml1S3VTWWxVM1R3QTNCUWt0dDFzcjN2TTk4VzBnN1lkZkhTQ3RMUSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792434472),('6xkwpqlRVHF4il4v3ytM6RPOnXB6FSsmpAPihq4Q',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiYzlWU042VXprYXlhZzFVaU9ZTW9tUnJobGJjaUJXVGpXSzBHRkYwZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792434575),('76LEKyObWaGKeUu0zPZjU1YXFbLg0DBkJ0kCuTpQ',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWGRVaGI1QVpZWWRSbFpvMkE0Z1JEbncwQkhhRHVweG1Ib3FQdnV1bSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6MTE6ImxvZ2luLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792434546),('7DGtyO303yC0uwN5iAD3Y3DHqKS9n0I43R6McrKL',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiU2dyYngzQjcxRFpJbFNzQ2wzaUgzRklTZTBReDJyV2ZKeE5Hc0dxUyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433392),('7etm6YBynmlGb1WwdbePY4AI0GqocMkbf0J2e57e',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoia2c2VU1TMUVIQmpxaXpENk1uUjVyV3JWWmhRUGYxeEJMNE5UN29MSSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433110),('7fny7t8Xynggk3KNm5YKPobUVOzo4FWewRNLV2NB',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWHRjSW1PM0ZWdWZaOXA2VHBVd2gwRWZaUDVxcE1UZjNwRmJzeUN1diI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433566),('7p3dj41nhgl91V479Aq2PSOZ2whuO22GKzofse14',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQk16Uk1Tbk54U1dkbE5KZ0RoMXdXejc5ZjRqUGUwMjE5ZzVuNG54SCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433387),('7Xn9CY4kf9CoPqhPdpOn9jK1ZKqTXx9Nsl3ss2DC',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoieW9LYjJWQW4xclk1WkdwRUVUcmFZN05xSVhaY25HQzhldWhZa1ZwViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvY2FkYXN0cm8iO3M6NToicm91dGUiO3M6MTc6ImNhZGFzdHJvLmNhZGFzdHJvIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792434733),('7zTzuul7nRLFqb4RIrvkyaVx5EK2VePuNKcl0YUK',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoicGR2UWdMVWdvSW9Da3VpSENJZUtZUWcxSGxab3RkdER4YzFITk1rRCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433556),('8q5nxRj1zmBopZKeZNd1GejLzBfXACyR4j5qGXnG',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTmpxWkUzWHQyWDRPdHBoODN4TFFSTzRaSkRzUFJXRm51dndJb3BkOSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvY2FkYXN0cm8iO3M6NToicm91dGUiO3M6MTc6ImNhZGFzdHJvLmNhZGFzdHJvIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792433840),('92qW4hmnhHrY5VA2NQ1mtZ3AMhG4lf4Fxs65w2oG',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOFNuZ3VpQkx5VzVUYjJMcWlLVkQ3Uks0ZnR2TXNkNEZnOUxrWjM4VCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433261),('9C9gLqITODTl6MhUdHSKWQNmYeuwFwjrHrSKXx7I',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiMjN5ZDNWa3o0ajRkOW1vMWdNSUtlRmluODJMTTI4TWsxbGtSVkdJeSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432685),('9F4ZlHvHzYRr5CT0eplC3odcWkiWKAAsFr123Qm8',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiMzJUOFJMeXF3Nm9lYW9kYm5yaWMzTzR3eGNUQkNjaXZFdzNSQzlSWiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792430901),('9GSU8wvQboTjUH04WKxE3ZjwGdWzL9FWjW0m8DFW',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoidTNEUjVJYkJrejRSNDdMRFRJRGxXaVdzaVAzWGRSQ2Vic3ZIdVlRYiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433212),('9l4vxc9onBJEsfAhGFej95XVzMDTKwlex8f2Kmn0',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTVZONG96cVZJcUZVeFhvQ2JOVlh1R2M1MklQQU5DdmtDbHdJNEQ3SCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431287),('9NKDC12NxVcPZqLPwD7WUFRPmw2ecZDhmne40CoU',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ3ExWllQM1JrRlVCYjF1ODVBdkNoNGx3R2lqYVFCWmF3bHpFMzl1RiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6MTE6ImxvZ2luLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792432767),('9vUWQ9gZTIxGTw2QSXB2ymwEE9hPlDHTBabGzdVK',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiR05FRFlBQmkwS0xpTU45RDhIS0dDRXdmT1R0VmZ2ZHpxS09zWkpKZSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433267),('A2Y1OOJqBPTM3nXdUFt7Dd92VU34qzN88PLOhaXJ',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoicUR0NmYzUHpzZk90a3JUR1c2RlU5SXQ5VjRzSzJlc3BYamRXYzV3ZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433270),('AAmSBD5aERPKctqYYeEzCtpulkOKZWuS9gcjbVjT',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZDZ0VG5jSVpVYnpETkhnaXZnb2sxaThybkwzVVVEdU1HblFwbUwyNCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792430988),('AdzLaXSVYHswldBOlBZ9Zg6wjtnSav6OsCnkn8LR',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ09sTURQa1RUVktXRmZSbUlzQnVqVGZoSk0ySjlpaEJab1JCZko2eSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvY2FkYXN0cm8iO3M6NToicm91dGUiO3M6MTc6ImNhZGFzdHJvLmNhZGFzdHJvIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792433818),('AJfVUIVCbw2LRJhOzLAqUZntUVaHd7XE6lGEfD6A',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUDVZbEFiME1JRk1jNmJGc1JGM2RnWmw3U2l5Z0pjSklZNWlCY1dURiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433657),('AoeNIdV8ufT3QlIcwrrn9h84Ps9YHO0UZTpTaEIE',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoib05pMUUzQVJNc1JxMTNLNUhacnpZdHZ0Y0FOYlBLOW9aNWRTNDBjZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432636),('Aqypc50dPP8mT7PeXGFZYuyDuA3wCVvLPthVB1SJ',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNUlPU2U2SmJ6dTJCYWtEWmZqdncxSWtGbEYwR3BpeUZVMHg1OGZPQiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433535),('awmW471ucgp9k9wJGKJCYOPFYRv2nlvoqJHv2tdE',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiR0hKbUFPVnVHUmJDekhqUk1HUTNUQW5ObklmT09PelE0RjZzRnZjZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432593),('awPCSo14sCYxunf4qLCqFOm97wn5WZqyseej7TNl',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoia2dNeVo2aE13TXZpRVdhTlpSNUdybjNENzVOdnh3SWVYZ0tzQlZobyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvY2FkYXN0cm8iO3M6NToicm91dGUiO3M6MTc6ImNhZGFzdHJvLmNhZGFzdHJvIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792433797),('Ayor9axbYAsXyeAgQtwFgpUB9fdKJVRaxXbdRLY4',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZW5wSUR1MmZMVFFXTXR5TmtPZTl2TjlTemN1c1VldlVuYVUzdUN5VyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433643),('Be1E4zO9Z0W0VWmqpsIOguiz5AOEEXWdQA4ZtcLG',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiN2VDWEF0SWdLSEpaQUhhalhFUXE5WG1xRlFlcE1oR21ZUzJCZk5IUiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvZXNxdWVjZXUtc2VuaGEiO3M6NToicm91dGUiO3M6MTY6InBhc3N3b3JkLnJlcXVlc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792434748),('bgzd0GfgGJXWHsvgBWGvVGNOnyYNVkWNRHe9qTLL',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTWlub1lCeTFnUFlCZG1PcXp3eEdFdFFDYnVMRU9FeUxhVlVNaVltTSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433128),('BKi2MMOldsqVYN1k8b2tDkSfOLvzHSyS1UQIdRWQ',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiV0NwZG5kaUdpYlRqR3dlUWVkcEZoeXYyZEowR2NmeWFOVGtrZUZtMSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433416),('BlKyKhp24IqMHGULrH5kj01bDxFk5ewx7UcrwLb9',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoibE85UVZadVVDRUR2ZVJPeUtIRW9vS0luc0lYdWR1b0s5MmRIdjBqTSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432553),('bppj5pxQycHbHbfyMmQAOBDKYqaWVqiIlx6H1WU1',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ0x0cUo2bHFJM2VhSzFFeXI1NVlIZXdiQW9GNEd1SzdZa1ozc0FlVyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431274),('BS3mIa9wX5Ugk7TrexOP098CnIryaTn9QyOfQBIR',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiczYzcmdZVG5taHdHdlNraFBRUldwNWZlSW9Db0d1MlNxeHdtcWhlTyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvZXNxdWVjZXUtc2VuaGEiO3M6NToicm91dGUiO3M6MTY6InBhc3N3b3JkLnJlcXVlc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433844),('bWifYmRKfqtfjO0MuJZZNxBsUx6EeKJdk6ze5iFN',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoibWtxSWJXcU01dzlGWnFIbkc3TFdTUmVSWjdsV1RDbzdRYUNpRFJmbyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431314),('ce1YDFuV9A9DnuTIi6yo4mpht25Co3utrRYQcZGf',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiMTJiOGxHUzhOam4wODJrbmJKRnJjejNDdlZhaEFOemZLblFzcG9haiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432531),('ceX5iYwcNnx7ywRD6uxpaCZ0TljF4Hj2WVXCt9C1',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoid1RySUFFdG1JWU1CcG44enRobzR5R0NvTkFyWHFhZk5QUGxGT3h3ViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431158),('chSjwHOw98dhEpRMlCwhCwUyXrMzyB4wcQUDuwYQ',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiSzRCMG5TZmt6b2tacTZqamRDbU0wVEJ4NDdYUlAzcE00OWdLSDJLbSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792430878),('crZ38EvlNtaAMhHfd5SikBhim4yBMDpa2jiB2Eqx',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVk9xZ0NZbHRzTW94Mml0YjBJNGlWb05aUExmaUJqMWpqYnJQZmNNMCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433700),('cuTWdWU6vpQI3MIndtmTuW1u8jmxyw0BpPSVHVTi',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWU11NzdOcG5hc1plWVVRb0NhRVlINU5jTXlnbkxXYk5iNWNwZjJJdCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433447),('CYMZVmRFRMpJuu0UkKCv4GYzESb4LpYfYbx05nG3',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiT1hEYURzTzNOVFZRajhpMkVRR3d0S3BQN0VncE5CdkZ6TlROSEFHRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433755),('D1DYrrW1UicPM1Mkj4WysWIqn8B6fxhTjmLrvNsv',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOG1jZ3VIblBYS05EVnFVU2ZIa0p2REdKTVJTdWFTdzhWV213QXVPNiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6MTE6ImxvZ2luLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792435375),('D3kQMp0Zvess5e9G1FTHPUXjgiARTSurDot30WCM',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiYlB1QndYZnBxOUxHVkVDMjZIYTFDNlhoZDREN2VRemVjMng5YnZUSiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431180),('D64lho7kzyYEWKmkebes2ph6MFMi8yMEtW6TrXHZ',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiT3YyZWtZQkRtV0xlT1hyN0l3Q3ZJY2RWRms4YTloQzhTYVRLTHJrYSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433520),('ddkrtwFQacod1Plr9aCgwbgh2AXO2h0bDO9h4Zy8',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUnVXaHFhRDA4ZUI1d1JOQlNtaFMxUzdnYXpVdVJ1dFJvRkJwa0swayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6MTE6ImxvZ2luLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792434782),('DeYzsAPVWc8PEgttKE12aQdSSDreOUx9Jf5lwvPD',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0','YTo3OntzOjY6Il90b2tlbiI7czo0MDoiY2xQNjFRTXZkb0Yzek92d1BWbkx1am1oSG9mZUY4MDg1MGlBdExKciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hbGVydGFzL3Jlc3VtbyI7czo1OiJyb3V0ZSI7czoxNDoiYWxlcnRhcy5yZXN1bW8iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjEwOiJ1c3VhcmlvX2lkIjtpOjIwO3M6MTI6InVzdWFyaW9fbm9tZSI7czoyMDoiQnJ1bmEgRmlndWVpcmEgU2lsdmEiO3M6MTM6InVzdWFyaW9fZW1haWwiO3M6MzA6ImJydW5hLmYuc2lsdmEyMkBhbHVuby5zZW5haS5iciI7czoyMjoiZmF6ZW5kYV9zZWxlY2lvbmFkYV9pZCI7aTozMDt9',1792428409),('E36VZoCxlaS5dRdzuHvY6eMjRcuWKVTmzfnvaBoV',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNVBoM2ZjZUVGUGRsTXRtc1UzTngyQmpacThQdXlVWEZrT1FQVU93WSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvY2FkYXN0cm8iO3M6NToicm91dGUiO3M6MTc6ImNhZGFzdHJvLmNhZGFzdHJvIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792433814),('E4skgjgV9knE62PHc5OgbtUb8RxUfx4zM57uwd6K',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUFY2UU9oeXQ5WEtLYzkya0tzWURjVFlrRTlsQ3laV2s1aGU1RGw4TyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792430961),('E6H0xQK62PHMvuv3IhU5o92zDaaGtJIpc6HARNKl',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiU0xWS0ZRdm0wamdXeE1EaGdkb3lPUmxRYVF2MXNvSVBVdWlBUnpBZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431210),('e9BuQYhdUpN2xUrINP0rYvpkA0XJB7LjeyONRlS7',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoib29hS0Y4VkxJbXRCdjlFNXEyZ1JmSE9WcW12NUc2MVR3T01vNE5TciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433481),('ed0Dya4S6q53geAe2uJ2HmBa3iCLBL6Cxg2KXHl2',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVnFHMHJDWUtTQTNyZUxEb1lvS0YxRDlTVk4wT21Rc3psa0ZEQmdTSiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432587),('EEtjDmLwoemHlTMnx9VHrUpKDxtaA7r1ypdNYojh',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoidWNSTFpFYmFmRmg0dmdHQm9zTUxpaFRVSThqSjRkVExDZURBMExQOSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431349),('EFEVdsWq2a0qCo1jFbNVNCqbxxL7gmEx3QwmERGq',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiaEZNdGhnSGVMcDlXeXN4dElOWE9kMGpRMlplYnNQdTFmajJ1UEpORSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvZXNxdWVjZXUtc2VuaGEiO3M6NToicm91dGUiO3M6MTY6InBhc3N3b3JkLnJlcXVlc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433847),('EJzypFi8dflvungW5mup8Pu7LYxN5IhkgNsStPVx',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQVAzYWtzcEE0TVMzVDVRTktqM21Ea1AxY09FdjdEU29kZ1VOdUE4NiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6MTE6ImxvZ2luLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792433259),('Ek2FGj91wLbPH7bRE8nFcIcB90y7sizpVKL44wQ6',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoibnRZdUJacU51cmpJT0RYUENYZXhSSHJJSVlFNEFvdzRHNGZFV3ZRSiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433300),('eucmE5oFmgKKyPnhRtdO8EPgYjZJ1hnvihvrT4Rw',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiR0hLYjlsc2VRV2d6Y0J2OWpla2JuSEo3eFlhRnJhRDhRVUszZnZkbSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6MTE6ImxvZ2luLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792432848),('ewVe0hRUO3KKbTSG8yusN0qPwTMTAluonXkaz8mF',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOERQb2xYaEEzbnFzdFA3MmF6Wndsa1gzWmxTMmNMZG12cUJKTlBaciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432541),('fi4pkKTxqb5hG3QDPNeyOkIhFt15IKoa5tTJ1qvP',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoidVUxZkpxeldUSmptbzIzOFhka0dEVVdQODFhdFBmUGlrVzdyRWR5ZSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433073),('FntXyZ0rQW7ROX3jmkdw76xWZMCr4QmvtumfZdB8',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoicEI5TzVvS1g4T20yb0dzckYwU21wdkl3eWJvQWtWOXlGMUg2Um5ZdiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433442),('fP6W5FOg25SYoM92b1BblK7sGsS8zwOL38IdtL0P',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWXlpZ3g4V2hFajFIZ3dFMlpUVmVLMGFwaFBsVUMwcmE0WGdTcnVWaSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432659),('Ftu9urL9OViTYVO60RMePp3963c25o3x9qFRxHWF',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZWRKTkVrSFdFWG40a1NRZXEwWHRDV3U5bkxzRGNaRGV1NjhYenJ5TyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433314),('fVtzgM0auRqHE2F1oLnSe7xRbsmKwYfMOrUJaOqk',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRWFTTDhnbENrdGVJTDczd0RCQzc0cW5VSWlUTnFwdEhYaGF6MWtoTiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433561),('Fy1AVyiyqE8I3529OlgQpuin7YYu0IUP1JOH3F4v',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRnI1cDY3WUw1ZXNYbUZhbjkxbDUzTld4NkpZWWI0aGZIdlhrM3B6VyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431337),('ggvUOVqT1aM7HDUHI8Vo0c3bBjF9frmrReqhoYui',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoibjJHSmdQZ2pSa3FUVVRiNnJ0SHFnNlppc012U3Y1bnB2ck1FTFNRTyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvY2FkYXN0cm8iO3M6NToicm91dGUiO3M6MTc6ImNhZGFzdHJvLmNhZGFzdHJvIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792433810),('GiG8T5y7rVyeG2ClqT60YovTmC04r4DSk6cyrDdH',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiT2xDVXJ6dDBBZmRhUEMwN3dpN0xRT1paTXZmUGs4NktDeFdsWDJ6YyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433231),('GPtS4Xnf1iHZVuy4lZl7aUsot9Fyge4W02MDmHA9',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoibzRjNXBrTTJTdVZ3bzdhUm1qbXlOOTBRSTVDaHRvdHM4MUVhVDZ2ViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431268),('GrKTI2yGGlNOKmucZloE2m92PZC0yZiV79ptOrz4',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoib21uTUFTWWpsZTY4a3Bpc2poOThNYVhnU2ZoMHBrb1VkUEY4YXRhMSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvY2FkYXN0cm8iO3M6NToicm91dGUiO3M6MTc6ImNhZGFzdHJvLmNhZGFzdHJvIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792433800),('GUObpYersN7FdnGjBkY8D8deXazaNakJ73GDQk4j',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQnVyM3hXUWFUcVIxdTlTQU95TTgwZTVEeXlGRWl5TE80b2x2QTg0UyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433105),('H3iyirY7LxEHqyaX1MaX7EN4JB8jxYJ79YGOafmV',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiM0FVdXl1NXpiRGtRd1F2b1NrVHBuSTNJOTlMWkl1Z1FuZ1R6WER1aiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432691),('H80PXosE4kU0RoA7EV0nmsP5irZyERUO27pHOfKK',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoibXFvRlJtVlJWZnBGTFhNUlhJTHB3UXNQVHJQVmJmYjZMTndkSFZJeiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433351),('hgAU40DRvBDOOYVb5cGlYtccy3RizKwCRiB9Roxv',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiM2xxbEc5TDBWTUFLZXFMQVllS3ByamJYeHpiRm52TUdESEh0eHc4UCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431280),('hO0agY24XRASZ7evDrGJZITJOIi8s22oKhDPLKTP',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRXUwT3pMU2xIMG9xRWI1ZWVWaTlLSkRIdGs0eWVTMjE4U0pQSDYxbSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433666),('hVRn5rtlnlpsDo6EACNfqVYhn7iQEEKn7iPOFD4S',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiekdRTFZrbnBaUjlPYkhNempuV2Q1S2dCTHl0UkNET2JNQWlDOWg3ZiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431221),('i7fgkuJalyVqkGwWlP0lgzKGFbvzWmHw26q9zD69',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiT09pZVRzQTRWRnNFbEllRGVTUTAwQlVtSnRWcnVydXA1VnhNMHdzbyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6MTE6ImxvZ2luLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792432815),('Iiv29Kgk8EJns6YGujJuqeVSlb5ls3vWJnYKE9GY',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoidGtXZFFTMkRDVVNmTndjSEw5dWZJbHVTWGpJVFpzaHhsRUpnSFdUZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvZXNxdWVjZXUtc2VuaGEiO3M6NToicm91dGUiO3M6MTY6InBhc3N3b3JkLnJlcXVlc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433874),('ilWJz8sEj60ctIzgVxzOULbkb235uFMIr3sMz5xn',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUFN0ZzBoekpBMm1icmxvdWFMbGpYVkE4WWt1WEZaZjlwNk1SS2F6ZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433327),('ImRRoJQIunG0YSKTIlYdaYcosHI9HO2qqIR3zrIJ',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiSzZIQkNjZ01sdm53Qno3aG54SkNPZ3dsR21ackhlNG15dFJjWlN1ciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433366),('io7xEpZNy5gYNN7kqclEcbipeNALIRQ0U1fXCGuB',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiY0lYcHZOZTI2V3dpVUo4a3ZheEg3SlNWSXczZkhRUVpacUE5UzFIaCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433739),('Ipy0hUa4SlEFOhd2CKS5oIxlDhC6yXOwl8vbgeoy',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOGhoQXhOU1BFTG9JeXRQelA1aUFXazFTVXBJbTlaSmwyZ3F1WEY4NyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433486),('IqJaB3V1neGmeEmRGcsOZiLo4vSGKOic2HoELj1g',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoicVRQaDR1aXdYSzhMV1ZkdVRERDlJZ0JmY2VVUnpxc3hPQVRDWkxSQyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433615),('IWkVLWG3QFo07eFm3I6zwElaRqYh60oOGgrzcMeh',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNXNwYUhPM25CeENadEIyMnBTT09JTEJDdDhhZ2pXM3daUkpDQmZiZiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvZXNxdWVjZXUtc2VuaGEiO3M6NToicm91dGUiO3M6MTY6InBhc3N3b3JkLnJlcXVlc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433878),('IZKdLDovWhcYJXeH96QZ6glMkj2zM58M51oQDwIN',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZDFwWFBzbDNNNzN2WHhCa2VEWVgyN2VPWDluYUtTOTF2cTlnM0l2MiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433671),('IZZ20IxeNAvAW83cwlskROTbgNSGL81bXZ4fkupg',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVE1xTGNDaXhjWFZsOHVrYWxMU0VYWHpxaG12d3dSd0JKYmxST1lyZiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433165),('j5ROGcCll0VVZpNCIWvHj9PInSXhj8lTFWf4Xy30',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVHpndm1DakN0UmsxMUlpbUhUOGRUenBrVjhjd0RxRUJxcTBHbVRzTSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433274),('J5xIF9QKic4fDmGeRHAF6SJ6VI5PafxNA3PflaHI',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0','YTo3OntzOjY6Il90b2tlbiI7czo0MDoia1dhSEVQYk1zV3JMZnhPZzVUQ1hhdExnc0RlRUhFYXJkdGloN3E5YyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWxlcnRhcy9yZXN1bW8iO3M6NToicm91dGUiO3M6MTQ6ImFsZXJ0YXMucmVzdW1vIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czoxMDoidXN1YXJpb19pZCI7aToyMDtzOjEyOiJ1c3VhcmlvX25vbWUiO3M6MjA6IkJydW5hIEZpZ3VlaXJhIFNpbHZhIjtzOjEzOiJ1c3VhcmlvX2VtYWlsIjtzOjMwOiJicnVuYS5mLnNpbHZhMjJAYWx1bm8uc2VuYWkuYnIiO3M6MjI6ImZhemVuZGFfc2VsZWNpb25hZGFfaWQiO2k6MzA7fQ==',1792433344),('JEiQrlLOzriZVUtw7xzXH0TVHyWLoMNbeNFTfzB3',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoicmw1Mkp3dTZXZ3VmYXhDU0R6UnRSN2w1NVl1Z0lsWm9MTVRDSWVJTSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433736),('jGO35F4tTNMe6MPJIY2tNwLhaDYeH7yY19hdbHRU',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWEhmc3JXTXN4MUdlNEpiUUxHSnBuRmlnN3laeDNVUGZwNzhGcHhiaSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432535),('jhwEzrxjumw2VPun1txFEoL0ZsX4tyfrZzLr1jyo',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQWV0cmJTNUFMazFZS2xGWUg5T1J0ZjZxSUNhZVlxWFh3d3l6UGcwciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431331),('JKpQgP41lO7EnMpP3uY8Y1IqIlTswIp2EZss4sfX',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoieUhCQXIyU204TFZrMjRWdVozcWg5UzV6Q0h6cXFNemJnd2dmQ1lmUSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432576),('JNxR01VHqjlzc6Qu6GjV0Q0LFheohkRL50sXeabG',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWEE4OVR3S0NPV2RYSUVYRm4ybW9BQ0VPSlVYdW1iTW9NdHp4c3ljUCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433750),('JoawoRoI4FIHldalNUu6LqV7H7ONcuVYdASA76dl',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoieFc0WXh6SHJaYkJSYk9mNTNFTWxVUmdRNW43M2xPYkR5dWRwQUg3RyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433305),('jsDjKRYZkxB28q5w0L9e6zhwlA2I5heiWJmrdhT3',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiMXBaWmlZTW9sOEtFTjdOaHlwQlVPTHNycjVJUm80V3dqcFNKRUJ6QSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433624),('JTS86sDWX17wq9ItfsRm6v3ft5II2L2FxpeWZK9d',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoidkpZZHNKck95b2ZPaEl2UTlacE5BN09FcUNzeFU2Q0lWaWVsWUcxQiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431075),('juvJamnt5kzMXfrgXw12FxiaMFdU9dUQWAeF1SEH',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRDU3U3dkSW1YRDY2M2VhQzZUYXZuQmdHSHM5QUoxVHN0SlRFV2VZUiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6MTE6ImxvZ2luLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792432810),('K1woYeAkf731py4CxlRvaWWYrn7UVD2pEwCwk4T4',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoieHhVdHBFdVlzNmJ5ZndvZWt3ZThQN3lYaEluUzdWWUlKZ2tjdlprcCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433181),('kkO2l9dX3U5PlJBn4drvtYvwa3HIVKLdUvZT97cZ',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoicjl2SXNTcThTcWVua1BFbGk2T3dkZlhNU05RQzNJczdnNVBldDFpMCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432697),('kTE9PgG3nSQMZK77Ejz4FHoBR7hU2fltpUtr0iiQ',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiekNFVE5sN3U0aHRtTnR2VUtQWXA0ZGV2ZUtCcDZ5NEQzSHowZzdGUSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433580),('kW9HXRtGu1bAAgwIRooAC0qw9g9Zo0nwRrAqJ9uG',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoieFNnVVB2NEloZEJXb09tUnZHQVdUQURETjhySmVpVEZkemJzRmtEeSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433401),('KwzUa9thbkme1LztAwIspQioZNQVV9QE34Imso3L',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiaVFLUVhSMThRQndDWjRWNnNJbTQzdFkyNGZ4eG9EeEtKS2Y3SXhVSiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792435236),('kxb0v1TYuSd5SmB55zTmrm03NJzAEjlz5sbo6YFQ',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiU2hROU5MTmhYTklYUGVGTFlQUEpUVzFnOVNnMHZkTUlxSFhvSGtvNCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvZXNxdWVjZXUtc2VuaGEiO3M6NToicm91dGUiO3M6MTY6InBhc3N3b3JkLnJlcXVlc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433911),('Kyo2JvI79ahrlIkT24g2dYXI05TSIHiGDHMsGnNu',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiN3RwTXNzaVdJd1dlYnZHblBHT1pQZmtqVUN3aldDM2owRXlLR1pLcCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433321),('l84DoDRzwMLoEou0yfaKnAv3Y12mwyI6hthRwTKj',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiaXZPUGpZelBkWkZvT1ZxbVNKVVp6M0FuNWlnYXZrSmRjYmJoR3RJZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvY2FkYXN0cm8iO3M6NToicm91dGUiO3M6MTc6ImNhZGFzdHJvLmNhZGFzdHJvIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792433807),('l9dHEb8c9miia6T8HZNKuXu3xK0yIdvhMwHTGn2M',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiV25YcFJxWnhhS2Z0R0tmVFdDc0tCM01jTVdrWEdQNWpUQ2o0ejFrRCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432681),('lkCPaoT8BKDqoCgIkrllKfMg2sZ0c6x0MIcomwh0',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQkluNVB4c05yZ2lnU1g2b3pqYjBZMGlMc1VraUJjQk9hWVlTcE1GQSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433296),('lm84XNgVq4jOz0lzA090tOnaJ1TccVPQ0rcixtjX',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOGdIOEFRTjhPelpDSlQxRW9RbFBYTFNYaGZIMUtGTzdBZWoxRUo2NCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6MTE6ImxvZ2luLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792432820),('LTQBg4bks2qbyb0mKDkh3AniBkyMZAIMFqCiSiNo',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTTlvcENMZ056Znd2SXo5alVVNmhHWTZ0aVM1bEVTRWZNd3dzZzZVaSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433217),('lXoeotvaFK3JdF14BwtffflG2mukdZjWtIFrNnYy',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoieEdqRjJobEpOVDR1dDJvRXZPbElLQjFlMXhWbmhqZXRUR3lJaG02dCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433705),('MgBqUnz1NQy52ZUuGtqsDIHEyxFeOferECcYoTCl',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWU4yeW9RWmhRaFowdUFTQW43dVRPN0h5eGQ2T1FCTmI4Y1VvVGpkTSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431293),('mowkgKZ5xaKlEBJTTfcGhBGMeYbnozsFBBBzZ42q',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNno2d25CTUg4RFBXU093cTc0RVFwREg1N0VHanFreGUzSk9SNFlWZSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvZXNxdWVjZXUtc2VuaGEiO3M6NToicm91dGUiO3M6MTY6InBhc3N3b3JkLnJlcXVlc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433882),('Mz8PLHCFlgKsu8N57gu3g0ozw77YTiQ1zegK8Cz7',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoidVdiNkQyaW1hMTJXdUxKN3p1QjVBeXR4YkFGM3Y2dnVoUkJXdlZCcSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433206),('NCdEwv1Z3dLFuAAUOmWE3KKfacFz4eEMLDHUQMgD',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRnk3NVl6eFI4V2pIdTB2Zmt4bDczWXVuR2h0TGloRWhxVFIwczZJaSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvZXNxdWVjZXUtc2VuaGEiO3M6NToicm91dGUiO3M6MTY6InBhc3N3b3JkLnJlcXVlc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433866),('nKkOvUPOYFLKAV3gi4CzsSTCXW0ZmzVIhfGvNgRD',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZElHTEtnaENtZWRoNzlJaGd0cFZMZ2N2OXRiMGJqREZrT3BqMFNXNiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvZXNxdWVjZXUtc2VuaGEiO3M6NToicm91dGUiO3M6MTY6InBhc3N3b3JkLnJlcXVlc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433918),('NN3Swa2u93ZI8Uchbej8TJ10GquW84SOELnw3o1B',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiN0hLNzVtYW5SMjBrekJiVG5TanZyQlJWT2ppUkNTcDZvOFdkVlkzMiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433620),('o8bvpaDSHyBWVETNJ2fhHy0oCKjoPwJRAdABMpBQ',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ0xJeHRTbFpzVjN6YnpkRURmUE9jaENJaHFtcW5uY2pPSEZSU3FYdCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433576),('OAWvq8aohKcn99CsklebX6OvbYEj8S58kfLELLFX',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoibzdHTTFpTTRtRGtMZkJScU9TOHc4WDlFVVBxQXVva0tCZ2IwSXM1TCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433743),('ofYUIXhVbt7Xjk22J8DpwUdNbaO8q5ob8AzBYNw9',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoidmFQWlZPVEhObUFxQ0VzOFM3R0NRYzdSemtUYXlxcTRRUXpqYXFTZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6MTE6ImxvZ2luLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792429950),('OqHEhdwK3p6echvIq0QLKtMkSahPXm9VeUJdBb3h',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTFkwTVV5NmFEMUl1TjkzUGFpVHVNZ01ZWTUyVWo0RHhuUXY5MXV5ZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433525),('os898K8JZLtTDe1oSGg1jCx1SD3R2qG4HbASwsQP',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRFhNNUZuRWVuRnZGOHY5eXRKVGlNTHhyN3NWOENvZ01Mb3VBOXJPaCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433079),('oUTLfNckKByIOQPn3ChBfDb5BqrGX4sxXcHYR67d',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNE9ObDdsQ2xhUHQ0UDVBWWdZS3B5OGJmYmdGb1JtdkxrSXBzZHJDdCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433355),('pCbnpJDbTzB5kmA8dxQqrkrW1ncqpqk3HaHo5VA0',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiT1hPTU56OWUwb1J3aGxud1VwTkFpeXBkejJZcVp4MXZTSGdQS2dubiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvY2FkYXN0cm8iO3M6NToicm91dGUiO3M6MTc6ImNhZGFzdHJvLmNhZGFzdHJvIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792433836),('PdbOKqYV4ayJ5xN0h3UPctoKYY7sjKPTMdMBILn7',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRTBHVVhibDN4UDFKVHByOXNXbnZZeW44Tjg3eENZMzlLRXRyNWxDZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433159),('PgrzuT2i8W1bVtMnAFSF8Nh3CxYuegN2WX0c4MhC',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVEZlZmRPT0libElEMEVBRVVEQ1Jnc2FFUHQ3WVM1NE1pVFZtWmtQQiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433175),('PnubzNc87C6yjoN1C7l8jmLwB0V24osc7DreQAPD',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoicmxOQ1V3TnNHRXlwMjZib3EwMm44WmduSmc2UFVOSVRVWXVnanBLVCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6MTE6ImxvZ2luLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792432801),('pT581I3KoENpaoasAekY2H4GVka1sXdp3etbOWMb',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiREFRNFJCY1hEb0d3N21BbGprSjk0SG9waUJmaHRKVlFFME5CaE5YWiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433278),('pWtKoQ6rfLIMahGpJtrBFZoJwXxgJRUdtcfOeXze',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNnJFcEJ2UEtkQ3ZFZkMyY1ZJcnNLRFlTQmdpMGdtclZoOW13ajFSMSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433411),('qEtquAE4nNcVj2zGZtW5mwfRFDwBPRJYrtzvvhiH',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoieVFocDRoM0lyc1NwalczSk96N2RVbnl3bUFZUzdmRkNvWVUyZDR3TSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6MTE6ImxvZ2luLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792432791),('qFvRcwU2aAImdTB8o5rGChBtjDlUQTpMNpa9NmdV',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoic0FoeXl0VE03UjV2d1hjRXpVd1FJVXBndWFJaXJYSDlxb1lmUjgweiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvY2FkYXN0cm8iO3M6NToicm91dGUiO3M6MTc6ImNhZGFzdHJvLmNhZGFzdHJvIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792435212),('R22zwHDYJcHOZzUlMOcT9HPtl1BZurKp3djvuKCA',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUXN4ajFMSklJSFFNR0g0dkNPT1dnUEtLRG9hNGpkV29mcnV0WFp3ayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433776),('R9BWOR9LGGPmqVe9kbjciPf9eWdDjHcQO96F8dOq',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoic2ZJTUF4M3cyandoQktUR3owSXQyVDVGOFFvTHByc21DWmtHdFZOMCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvY2FkYXN0cm8iO3M6NToicm91dGUiO3M6MTc6ImNhZGFzdHJvLmNhZGFzdHJvIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792433833),('rClgUQSSvxXI0uK4xgVjeAC7XbqMnpzUOTjoGKI2',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQVk3a1d1NWZnZlJsb2gzU3JuT1d4SldMbnB4Q24xclpxcmtMMk14QSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432599),('rEmzsCUQ2XbczA8QE6rKgyUGfVfNrl9wtgVKHNtn',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiU3pLdmVPTTVVVW5QdHh2Z2pZcFBTTm1hcG1CUnB4bDkySUtYWlRyOSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433406),('RePVmmRluJ4bX8CjL81rlpQDUt5QC9e7940lwTuR',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoib0hmVlBSclhGTTcwd1pGdzVmYjZVNVpCVHNlSEQwdW9FRzhJU1JyRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6MTE6ImxvZ2luLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792432771),('rXnHaZtRUvu6sJ6Rkp5XkenavrhTlOla3rp59Th7',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiYXFnZHZEcXhqM3o3d1RZN2VDblhDSGdnWFozdURaWUdIaVZsVENkYiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433571),('rxnYjaW2mnJaH4pZOBBZkB5NDZMnViuWlqoB4oWu',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoidEpJZlNkM0lCanBQNTVUbnFBYU9LdWY0V1dEOTY0NTlWWG1jWkNycSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433710),('ryXeYZKrNCAKjoMoRKhuB1D4CW0ruyDlcybHJrCD',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoibTNNcUczMjNQaTNoWWVPeERaU2Q2dUNFUnZ1VThLVkpiWGFFNkZXaiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433530),('S4dICBVE8nJq28ZHZ20Hs2asag81VcFTRJi8QnWw',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiU3ZXc3NOazl3Tml0dXk2SzJDa3lld3ZvZWI3dkJTRFhsVERQcEZrMSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432653),('SD0vNbTGGxYTqzNqmZrqOmeNIpW7FXxANHh2MzVV',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRDBEQzQ4QUR6dUI3VDM4UlhOVUNCb01kaUI0WndIeDU5dHFqN3NoQSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433516),('SEnzFFXRZUL5djyZuizcnFhtN5Vf0hyyTH2Z2ama',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoialhpNW9JbVY4QUZVY2Z3ZTdIbW1yMkt0bGdIYjNMTW9wbExINk5KUyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433731),('seO4GFHyK7t1j2Nhrf1QewiTzpnDOL7kLNCvxqSF',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoieXF0RDBWMzh5VFlhU2tySGpVUExLemNxaXJsWVFPS1JCQ2V2NVo0NiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433491),('SGeTc6enPC8aSwcoNTC80uHgf3MNFpmcfxZsa53c',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNVZKTTk1aTAyZ2tYUDg1Qng3eFVuMW8zTVVRMU5rYVpLdENoS3dqciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433471),('SiAVcZV4snEUHp3mfX3WZugedL5rp43pfrLfssA4',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoib1EwN2dBWEpBdnhSZGJFYWFJWGxYaVNoTmI5bHM5cUFOSlJMR2JwOSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvZXNxdWVjZXUtc2VuaGEiO3M6NToicm91dGUiO3M6MTY6InBhc3N3b3JkLnJlcXVlc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433891),('SzUOYm0uw6nr7bJ5JxdrsTqGCzMqysW90lLxAMFO',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOWRHSjM1Mko2TEFLVDdLZ2RTdnhjc3JsN3BMU1Q4NkpMb1o4Vk1HcyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433605),('T3uf6XfTvx9S7Vf3znMF0lEgMEFBl9EJjntcYS5j',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoicXdMVzd3MXd4UEc5TkVPVERtS2lNRzBYRFJyOXFBZFZBcUlwZ3U1OCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431053),('tcIQPfTOiVC2C8I9yI9Ooak4wDJehHm1fQnigCHv',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiME5ESXhjQ25FMGZ1NmJ4M05IN2dnZGZiRnl1TG5OWVNWOU45YTJWZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433397),('tdcFM81ag7krMsZse4SfL9EPl7XVh4me5GzIbbet',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoidUtaQVRaQmlJdmtKbFM4NEp0UGhxYldhdkdidUppVnUzRXEzREdvMyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433122),('u9DqVrI075QifaLYBb5GcTBfyHfUmJASeVqFkBmu',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUDlpcWRvbTRpNjd3WGc0M212c3NLN1BLM0owZHRXSG1HRUZFWjNSMSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432647),('UEKQy53wB3hfR1DaXq1sdnxAMMQHPPg4u1XlA3F5',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVnZ6N1BPZ1VneUJUTkd5eXVjWm43bGYxT0RzUlF3NHczM0g2Y2hXUSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433363),('UMnKZXNftwgd2VdkFE3uKwZ3u6BpY46tSQs6b1zi',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoicnpLWDZGZHVYaGx2empvYjAyQVdlTFJDTWUwSWgzUjJiQmRWUVpWOCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6MTE6ImxvZ2luLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792432839),('urAMeCe4vttVithVKWGBBnkausKLMXsLwrxis03k',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZGJDb3hudDZ4UUZFN2o3RnNHQ05ySUtrMmRHNXVZUG14ek9JQTllSyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433476),('uYYXkgjqUYplqIV0a0IKxjaz3xo67Y0653AquGBh',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoicExBYW93NmdQMmlzNFpJckhNdzRFWnR1aFBQYjl6NG1jNEt5dFg5MSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432547),('v5a5NhWPIEsVj8YQYMsYFueoo0BUrgLlNfEQAwMv',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQk9YMzhOU3JmcEc1RnFFNjZoZnpuV2FVRENKVzNGMWdzRHpYaDRjOCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433661),('VHcztdClaeKV0M2PT1fioGVJSDXSOxBgwbI1yVoK',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWWhuTEt3QWZTTWdsclg4UVF0b2d1dVNsRlp4ZGRYSE1xbjloZGNJMSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6MTE6ImxvZ2luLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792434774),('vKJbbSIAiFp4QyOYY8eUSRkV8DVpvDCiu3Nx1Z1U',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoicjJNeUVUaFhYbzhyVXhpdFByd2hseG1BalpFc2NVQU0yOFhHV2RweSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431239),('VljK0sOiZcE4mCexxbja7C3p4F4ZviXU45dOL0ES',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUWliTWFaRDNvQnBqUDNzZkIzUVA2d01IVjNxempXYlN6VFdRMUN0NCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433310),('VnMJkhLmH3i4h8iynhSk4LJNGJ06YgUTXUbz2ONH',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoia0loQXowZE9MWlVJOGp1cDZYeWRQNk9LdEtDYjI4SHYyZktieTdVRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433585),('vuVFq0rN5veaPOFbx08325iiwEsHnmVpUsGWp5dQ',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiSHprUXNXR2hUd2RSdEt5T0hFQkdVb1ZhelNXQTZmUmcyZEpqN25CaSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431343),('vygz5ROx6xhRj58kzQx9ERpsNxeBE8sT5ZKFuCDs',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoidXI3VHdqbWpMNnU2cDVHSUk0czY4NEVOb1pEZnViMmtlR2xwR2poQSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433433),('WCmcsFMJzgFYXRGImKsWX6OIRIQB3oYpUmQyGi05',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiMUFtd1d5OGtpNkkzTDZscVpBcVd4V0RoMXRVdFNCNUJkOHNPdUJqZiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431319),('WH8WlVpOz1KT3gY8dwsvB9b1lMRLF1amu2rS1xLW',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOUo3M3F3bjExb0VyNm0yRDNHYWJ1dUs5ME5vdzd6N0RKMUViWVM3ZiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792434769),('wHGIHz7MkFZ8rsXykVb8IX5WVgwh299ejMPmfFqY',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVzJmTlZEZUxTcmdkcFI0UXNReDI0Wldqbk1xRkZoa0tjSmNDQzhsbyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431136),('WiMPc822R4SSAftKHVBK20bEcNgj8h86NivAkwwG',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNWNxak5FNnM4RUtLM3VjamhFZUVDQXF0UE5NTnVGSzE0clMzWVJXaSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792434444),('wnZuHsLNUp8fU9uJqW87muLIcCqvOhIxqGfgSHMS',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiSG5DWGpvZkZDRU43dFNEQ3BpNm9rNVppNXBDRk5rZnJzVFY4UkdUZSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433170),('WtIwjqqaQTqiAlIDVG9A5priJQGJmD0dhlZytx14',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiY3NqaTNrUWJXYmRBZFdEYzJnYXo3QjZDRDl5WWkyc0Vaam5jelJsbyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433696),('WVLY25TNVJg1u6xd465xsek30pipuWWTqjrQ3d0b',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWE9CV3lMSVBGclowYXczeHN2WG1DdUpVYXpla1Q0ODA5WGZxbWd5ViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6MTE6ImxvZ2luLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792432844),('wxyA583rNtMo0NaSQXW4LF2h2qClroR86I90pf4l',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/155.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZVZrRERhbjFhYVZMWG0zbVVqaXVIWFQxdXpPdGhUY1dDRWw2MjVwcSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792435395),('X2nc0qt1tkLXHC2xV8HODHW1mmejXwEyfCc6CNG8',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQjVkSndvNXMxdmlPZ3BhcW5BeUtpaWVCbXNMNWdBMWw1eFA5SER4OSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431233),('xBRPgem57OAFxKjKv5vJlIvvGU7k1YstkeZDse0S',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoialpBZ2pyWG40N0N4aWtDTUc1VVBtaFIxZWpEQnNLV1NCaHFVWGZPbCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433359),('xc82rmK82H2WtsoCtXNYgE7l7gOJ5h8aQeMcpmiM',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoicFNtbWlaQ0lZQk5JSVlVbWxna3FBVEl6U0hEUDZsZDJVemVmalpnYiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433691),('xF6TShPrueRiR17Yv60L63HbIIe9FfzBko0EBr3K',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVUc1U2I1aG1LZnh6RnJQcXJJSGlTN2ZOSEtGMUpYbERpenRHZUx0YSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432613),('xUd6JINlNCCjLCTFL3ZgJb9MPJE8blL1qy4331LV',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoieUFHeTVGYjEyMm5KZWRjOGFsVWhmcWRra1J6Ums5MFpUUVczejFobCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6MTE6ImxvZ2luLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1792432805),('Y2IyU3TDCkwPv2epH5DeKdfu0HbvdvbUTR40zp69',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiN1JWalUwbDliMElBcTNWd0FzbHlrUW5saUw4Rld0VHN0cmdzaml6USI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433223),('y6fPCbrXCOP64E4hhvF4g2EYz4qRnjtYhdh6kPkX',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiMklUT2thMTFvdENLQXRoelNERE84Q1hhb2tQTTNoU3I0OFZsQ2FoSiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792430966),('yeggY3EPQkaX6Sx0Odv80vjUAZPT0aEYna7kQftY',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiYkxnOUlaMTZ6cUYwbEp6anJGRFp5WVFFZ052WlpoVkxWTTVlWUtGaiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432581),('yHo9OY60Uyka4RQrstUmTmEiUL698WlcbPD09Y9g',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiaFlCc3ZtN2g2TmgycnY1RVhBMXJ0bEtYM2JUNXlYNUhQSEpIYlJSMiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433747),('yLSafkdXKoxzG1ZMoo25tHtZQl5fsBcocYGa0hwj',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUGZHZG9HdDB5ajMwanFJUXRjcFEyWFBheHQ5QVExQVg4UkJqQWJPciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431010),('Z5XtGQdj7AUXbjQfag1KKb88PgUKrwKWwIM4io3P',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZDhpMW84Wk51UWNSNGtGSWxqUVAySnphTHNPdDBLdmxMMWRsN1FBVyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433648),('ZBZEhhhYBjtQKbRGV0mrv3czOoO6ks09qU4YFH43',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoic0dLa2xTZFY0ZDBQN1R0ZVdCWUFFdVhhSkZQaXVqNG9OeUZJQTdTMSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433116),('zeTcbVoDliKfpZWBFcxaPfRtYOVpOuR2n5XuGZx3',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ3B3WFNWVEFyalEzZGJkM3Q2dWZ4SmlmRzZDcVJWU0MwRDZYa3dRdiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvZXNxdWVjZXUtc2VuaGEiO3M6NToicm91dGUiO3M6MTY6InBhc3N3b3JkLnJlcXVlc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433887),('Zg5FTN4Bw8auabn21B7h5ulNAqt38pmu04Y5A7ZJ',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiYkVSRWVlc0RmTnEwaWIwSURkZlN1eG5ReFp5bXFMRFIwS1FIU0p0ZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433773),('zlOM7PFvx0kfWvhKdiaXCVwZ9Qqv6wCFH9VgFHiG',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiV2p4cXRKUEhuSm1zVHZWR3dwdkppZFdCRDliQWpVaWJUMW5kQ1l5byI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431325),('ZRUb9DDlBlpHNFqXtI1M4wVWNR72Vf7PqCJ0TWnh',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiMUdFUjYxVkdHTzNOSVE1a25Ndk1MaHcwQ2FLcGJvZXJaNkUwNHJncCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792431246),('zsgY7m8OS3D6w5vPEY94M5AMyYnjSAb946URkJfY',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoia3VsaG82bHNUOFl0YzBjd2w4VXlIWmdseEV4SlljTHhQMlFZc1BIUSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792432640),('ZTVgDVqFGJEN6FIIMZOFOnCtviiuSC9wkkSZQUQ3',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoib0pOdVAwOURQZU42QjI1dWVVQmFoVHhTR2d6UVRsUXI5Q1NZbmZsNiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433770),('ZZzCzi0JyH6hvPJs9HroVoQ8GixqiAvCrsO4wj5s',NULL,'10.141.131.73','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/154.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoidnhRaFNsR1QwS1JuUnBlSFFCTTI0VWc5SWpUQTZQU3diOVRrZTVWRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMC4xNDEuMTMxLjczOjgwMDAvYWNlc3NpYmlsaWRhZGUiO3M6NToicm91dGUiO3M6MTk6ImFjZXNzaWJpbGlkYWRlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1792433467);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

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

-- Dump completed on 2026-10-20  8:45:33
