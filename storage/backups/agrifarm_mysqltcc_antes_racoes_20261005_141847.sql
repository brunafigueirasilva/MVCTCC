-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: agrifarm_mysqltcc
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acessibilidade_preferencias`
--

LOCK TABLES `acessibilidade_preferencias` WRITE;
/*!40000 ALTER TABLE `acessibilidade_preferencias` DISABLE KEYS */;
INSERT INTO `acessibilidade_preferencias` VALUES (1,1,'light',100,1.00,NULL,1.00,'2026-08-25 14:17:55','2026-10-05 19:11:45'),(2,3,'dark',100,1.00,NULL,1.00,'2026-08-25 14:41:30','2026-08-25 15:01:20'),(3,6,'light',100,1.00,NULL,1.00,'2026-08-25 16:40:12','2026-08-25 17:19:14'),(4,2,'light',100,1.00,NULL,1.00,'2026-09-08 14:20:34','2026-09-08 14:20:34'),(6,13,'light',100,1.00,NULL,1.00,'2026-09-29 13:32:06','2026-09-29 13:45:10'),(7,14,'dark',100,1.00,NULL,1.00,'2026-09-29 13:45:39','2026-09-29 13:46:03'),(8,15,'light',100,1.00,NULL,1.00,'2026-09-29 13:54:44','2026-09-29 13:54:44'),(9,16,'light',100,1.00,NULL,1.00,'2026-09-29 14:12:41','2026-09-29 17:12:59');
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
INSERT INTO `cache` VALUES ('laravel-cache-11ae0b74cb6478d5363857eea810d6766cc88e80','i:1;',1788887224),('laravel-cache-11ae0b74cb6478d5363857eea810d6766cc88e80:timer','i:1788887224;',1788887224),('laravel-cache-34ed9725706e7c948857bece798ba9327bdab812','i:1;',1790691233),('laravel-cache-34ed9725706e7c948857bece798ba9327bdab812:timer','i:1790691233;',1790691233),('laravel-cache-5744064627119e7d32a7539d347be32218a02237','i:1;',1790679328),('laravel-cache-5744064627119e7d32a7539d347be32218a02237:timer','i:1790679328;',1790679328),('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba','i:1;',1791220677),('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba:timer','i:1791220677;',1791220677),('laravel-cache-8bc0e36e7d2fb83315dc98343916dcd33e664408','i:1;',1790701299),('laravel-cache-8bc0e36e7d2fb83315dc98343916dcd33e664408:timer','i:1790701299;',1790701299),('laravel-cache-a6b73da19d33a70d31237eeca9c6a51aadf1df02','i:1;',1788866344),('laravel-cache-a6b73da19d33a70d31237eeca9c6a51aadf1df02:timer','i:1788866344;',1788866344),('laravel-cache-a76d43d0647763ca218071bf62bd2b704fccf741','i:2;',1790677956),('laravel-cache-a76d43d0647763ca218071bf62bd2b704fccf741:timer','i:1790677956;',1790677956),('laravel-cache-c400ca3bc7620236bb3858ca60d4ec861abd06ce','i:1;',1790692711),('laravel-cache-c400ca3bc7620236bb3858ca60d4ec861abd06ce:timer','i:1790692711;',1790692711);
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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cadastros`
--

LOCK TABLES `cadastros` WRITE;
/*!40000 ALTER TABLE `cadastros` DISABLE KEYS */;
INSERT INTO `cadastros` VALUES (1,'Bruna Figueira Silva','49095793893','2008-04-09','brunafsilva.09@icloud.com',NULL,NULL,'19990019586','perfil/38I6p0ymI9fK4mzaqrTnVF2uhUiTo6IDqy7uTITz.png','$2y$12$/4h0uyMbLu2M98r1RCLk/e.WIL866foYZYic7mdG2T0qWvqQ5jzt6',NULL,'2026-08-25 13:56:20','2026-08-25 14:19:48'),(2,'Maria Eduarda Evangelista',NULL,NULL,'maria.e.evangelista7@aluno.senai.br',NULL,NULL,'(19) 99146-9672',NULL,'$2y$12$qEltB7/DE4bMuGPXHfhr6.Uq5j2YW6xsduz.Dep3ip0z38myqqppu',NULL,'2026-08-25 14:01:28','2026-08-25 14:01:28'),(3,'Yasmin',NULL,NULL,'yasmin.gentina@gmail.com.br',NULL,NULL,'(19) 99312-7375',NULL,'$2y$12$CpowZ.OGxihuBTI1PDFyFOxVun8v7I4areIcJzlpWpAOX1rnTj/oS',NULL,'2026-08-25 14:11:02','2026-08-25 14:11:02'),(4,'ARTHUR BARBOSA AZEVEDO',NULL,NULL,'arthur.b.azevedo6@aluno.senai.br',NULL,NULL,'(19) 99584-4882',NULL,'$2y$12$Gn1Bj2RGQj.0YEJFJw/Be.311x292JH9w0F3/3Ac1hLJZlWu1GMfC',NULL,'2026-08-25 16:21:12','2026-08-25 16:21:12'),(5,'Lorena',NULL,NULL,'lorena@gmail.com',NULL,NULL,'(19) 99999-9999',NULL,'$2y$12$mnDrSkZSY1Vq568gdL.YB.5LZWfZ4zlyzBjbKQQQigCCF/gXC4Vom',NULL,'2026-08-25 16:33:09','2026-08-25 16:33:09'),(6,'Arthur Povidelo','42090469811','2008-08-05','arthur.providelo@aluno.senai.br',NULL,NULL,'19991192992',NULL,'$2y$12$g7sC7JC9D7VGMnOMh4m0NepXnE.qsZzw0V5vfj04Ge3dnG1Z..Z06',NULL,'2026-08-25 16:39:46','2026-08-25 16:59:09'),(7,'Maria Fernanda Galdino de Souza Felipe',NULL,NULL,'maria.f.felipe@aluno.senai.br',NULL,NULL,'(11) 98765-4321',NULL,'$2y$12$uBjUORLIhQl8HJJMPx8xb.m4yiunatcz8RwMz/SOXKVAa8JSyNnLG',NULL,'2026-08-25 16:52:44','2026-08-25 16:52:44'),(8,'Bruna Figueira Silva',NULL,NULL,'bruna.f.silva22@icloud.com.br',NULL,NULL,'(19) 99001-9586',NULL,'$2y$12$St14H/mxng9AxdT9/N/LLewIk1TziGq1OWE0ezovy9FCPWJcEFiBy',NULL,'2026-08-31 16:04:47','2026-08-31 16:04:47'),(11,'Bruna',NULL,NULL,'brunafsilva.09@icloud.com.br',NULL,NULL,'19990019586',NULL,'$2y$12$UGQUqyHYLru/elO76x/Kfupd2ZAwEaKY1TxXjnlmL0XySJlVqRnG6',NULL,'2026-09-21 19:46:00','2026-09-21 19:46:00'),(12,'João Emanoel',NULL,NULL,'joao@gmail.com',NULL,NULL,'19994535902',NULL,'$2y$12$REYL8/uVILxOueUDqWPQmulaA8p6FmjOinVgYFntQyCr4XRcGQftq',NULL,'2026-09-22 16:10:30','2026-09-22 16:10:30'),(13,'Maria Eduarda Evangelista',NULL,NULL,'madu@gmail.com',NULL,NULL,'(19) 99146-9672',NULL,'$2y$12$HvmQ3NcVMsvyGmOFFnY5t.pMJf2B6pUQxNLz4PA2AjywzO9wh3AMy',NULL,'2026-09-29 13:31:36','2026-09-29 13:31:36'),(14,'JOÃO EMANOEL PORFIRIO DE SOUZA',NULL,NULL,'joao.e.souza12@aluno.senai.br',NULL,NULL,'19999999999','perfil/arYpmbOYBzKycr9ypdaYfuiacbWG7N756zHy1oYG.jpg','$2y$12$yBGXrqIhupJF3JiTOcO/Q.FQnPcA5mt8FPcGqgpFMJKxDLIS362/q',NULL,'2026-09-29 13:34:32','2026-09-29 13:52:00'),(15,'zaz',NULL,NULL,'princisi@gmail.com',NULL,NULL,'(12) 12344-5554',NULL,'$2y$12$oxeZaYig2gSag1XFsdpNBO7BtcZk9qz.FMixOulmbYKshwaalufUm',NULL,'2026-09-29 13:54:12','2026-09-29 13:54:12'),(16,'Yasmin','45290641823','2008-05-20','yas@gmail.com',NULL,NULL,'19993127375',NULL,'$2y$12$o8aYWxLoYjxMq2BajcD1qu3F0EtC3HvmhOmXjP.jjSXyGnPZ4q.Jy',NULL,'2026-09-29 14:09:00','2026-09-29 14:39:27'),(17,'Yasmin',NULL,NULL,'yas@gmail',NULL,NULL,'19993127375',NULL,'$2y$12$41k39LqJJh.yDnVMZrH2rO2qopH5xCXQaM9C/IfWv4jcECrgy9cFS',NULL,'2026-09-29 14:50:12','2026-09-29 14:50:12'),(19,'Murilo',NULL,NULL,'murilo@gmail.com',NULL,NULL,'77777777777','perfil/PRxeITo9pWyLnDNtmEyCjEYfI7aOXq2xPNPoSnc4.png','$2y$12$ylt3LAEbS/qNy/448VLyEekrNjQM6JZLCrEEASvScFVOdtTy3qgH2',NULL,'2026-09-29 17:36:21','2026-09-29 17:37:31');
/*!40000 ALTER TABLE `cadastros` ENABLE KEYS */;
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
  `capacidade` decimal(10,2) NOT NULL DEFAULT 100.00,
  `nivel_atual` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sensor` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_cochos_lote_id` (`lote_id`),
  CONSTRAINT `fk_cochos_lote_id` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=145 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cochos`
--

LOCK TABLES `cochos` WRITE;
/*!40000 ALTER TABLE `cochos` DISABLE KEYS */;
INSERT INTO `cochos` VALUES (19,23,1,'Lote A1-01',200.00,200.00,NULL,'2026-09-22 17:03:13','2026-09-22 17:03:13'),(20,23,2,'Lote A1-02',200.00,200.00,NULL,'2026-09-22 17:03:13','2026-09-22 17:03:13'),(21,23,3,'Lote A1-03',200.00,200.00,NULL,'2026-09-22 17:03:13','2026-09-22 17:03:13'),(22,23,4,'Lote A1-04',200.00,200.00,NULL,'2026-09-22 17:03:13','2026-09-22 17:03:13'),(23,23,5,'Lote A1-05',200.00,200.00,NULL,'2026-09-22 17:03:13','2026-09-22 17:03:13'),(24,23,6,'Lote A1-06',200.00,200.00,NULL,'2026-09-22 17:03:13','2026-09-22 17:03:13'),(25,23,7,'Lote A1-07',200.00,200.00,NULL,'2026-09-22 17:03:13','2026-09-22 17:03:13'),(26,23,8,'Lote A1-08',200.00,200.00,NULL,'2026-09-22 17:03:13','2026-09-22 17:03:13'),(27,23,9,'Lote A1-09',200.00,200.00,NULL,'2026-09-22 17:03:13','2026-09-22 17:03:13'),(28,23,10,'Lote A1-10',200.00,200.00,NULL,'2026-09-22 17:03:13','2026-09-22 17:03:13'),(29,24,1,'Lote B1-01',200.00,50.00,NULL,'2026-09-22 17:06:08','2026-09-22 17:06:08'),(30,24,2,'Lote B1-02',200.00,50.00,NULL,'2026-09-22 17:06:08','2026-09-22 17:06:08'),(31,24,3,'Lote B1-03',200.00,50.00,NULL,'2026-09-22 17:06:08','2026-09-22 17:06:08'),(32,24,4,'Lote B1-04',200.00,50.00,NULL,'2026-09-22 17:06:08','2026-09-22 17:06:08'),(33,25,1,'Lote C1-01',10.00,5.00,NULL,'2026-09-22 17:10:36','2026-09-22 17:10:36'),(34,25,2,'Lote C1-02',10.00,5.00,NULL,'2026-09-22 17:10:36','2026-09-22 17:10:36'),(35,25,3,'Lote C1-03',10.00,5.00,NULL,'2026-09-22 17:10:36','2026-09-22 17:10:36'),(36,25,4,'Lote C1-04',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(37,25,5,'Lote C1-05',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(38,25,6,'Lote C1-06',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(39,25,7,'Lote C1-07',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(40,25,8,'Lote C1-08',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(41,25,9,'Lote C1-09',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(42,25,10,'Lote C1-10',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(43,25,11,'Lote C1-11',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(44,25,12,'Lote C1-12',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(45,25,13,'Lote C1-13',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(46,25,14,'Lote C1-14',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(47,25,15,'Lote C1-15',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(48,25,16,'Lote C1-16',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(49,25,17,'Lote C1-17',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(50,25,18,'Lote C1-18',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(51,25,19,'Lote C1-19',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(52,25,20,'Lote C1-20',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(53,25,21,'Lote C1-21',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(54,25,22,'Lote C1-22',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(55,25,23,'Lote C1-23',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(56,25,24,'Lote C1-24',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(57,25,25,'Lote C1-25',10.00,5.00,NULL,'2026-09-22 17:10:37','2026-09-22 17:10:37'),(58,26,1,'Lote D1-01',100.00,20.00,NULL,'2026-09-22 17:16:59','2026-09-22 17:16:59'),(59,26,2,'Lote D1-02',100.00,20.00,NULL,'2026-09-22 17:16:59','2026-09-22 17:16:59'),(60,26,3,'Lote D1-03',100.00,20.00,NULL,'2026-09-22 17:16:59','2026-09-22 17:16:59'),(61,26,4,'Lote D1-04',100.00,20.00,NULL,'2026-09-22 17:16:59','2026-09-22 17:16:59'),(62,26,5,'Lote D1-05',100.00,20.00,NULL,'2026-09-22 17:16:59','2026-09-22 17:16:59'),(63,27,1,'Lote A2-01',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(64,27,2,'Lote A2-02',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(65,27,3,'Lote A2-03',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(66,27,4,'Lote A2-04',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(67,27,5,'Lote A2-05',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(68,27,6,'Lote A2-06',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(69,27,7,'Lote A2-07',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(70,27,8,'Lote A2-08',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(71,27,9,'Lote A2-09',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(72,27,10,'Lote A2-10',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(73,27,11,'Lote A2-11',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(74,27,12,'Lote A2-12',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(75,27,13,'Lote A2-13',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(76,27,14,'Lote A2-14',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(77,27,15,'Lote A2-15',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(78,27,16,'Lote A2-16',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(79,27,17,'Lote A2-17',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(80,27,18,'Lote A2-18',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(81,27,19,'Lote A2-19',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(82,27,20,'Lote A2-20',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(83,27,21,'Lote A2-21',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(84,27,22,'Lote A2-22',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(85,27,23,'Lote A2-23',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(86,27,24,'Lote A2-24',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(87,27,25,'Lote A2-25',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(88,27,26,'Lote A2-26',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(89,27,27,'Lote A2-27',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(90,27,28,'Lote A2-28',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(91,27,29,'Lote A2-29',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(92,27,30,'Lote A2-30',400.00,20.00,NULL,'2026-09-28 13:44:47','2026-09-28 13:44:47'),(93,28,1,'a1-01',122.00,100.00,NULL,'2026-09-29 13:38:32','2026-09-29 13:38:32'),(94,28,2,'a1-02',122.00,100.00,NULL,'2026-09-29 13:38:32','2026-09-29 13:38:32'),(95,28,3,'a1-03',122.00,100.00,NULL,'2026-09-29 13:38:32','2026-09-29 13:38:32'),(96,29,1,'Lote A1-01',200.00,180.00,NULL,'2026-09-29 13:55:16','2026-09-29 14:25:40'),(97,29,2,'Lote A1-02',200.00,190.00,NULL,'2026-09-29 13:55:16','2026-09-29 14:26:02'),(98,29,3,'Lote A1-03',200.00,170.00,NULL,'2026-09-29 13:55:16','2026-09-29 14:31:28'),(99,29,4,'Lote A1-04',200.00,150.00,NULL,'2026-09-29 13:55:16','2026-09-29 13:55:16'),(100,29,5,'Lote A1-05',200.00,180.00,NULL,'2026-09-29 13:55:16','2026-09-29 14:31:57'),(101,29,6,'Lote A1-06',200.00,150.00,NULL,'2026-09-29 13:55:16','2026-09-29 13:55:16'),(102,29,7,'Lote A1-07',200.00,150.00,NULL,'2026-09-29 13:55:16','2026-09-29 13:55:16'),(103,29,8,'Lote A1-08',200.00,150.00,NULL,'2026-09-29 13:55:16','2026-09-29 13:55:16'),(104,29,9,'Lote A1-09',200.00,150.00,NULL,'2026-09-29 13:55:16','2026-09-29 13:55:16'),(105,29,10,'Lote A1-10',200.00,150.00,NULL,'2026-09-29 13:55:16','2026-09-29 13:55:16'),(106,30,1,'Lote A2-01',100.00,100.00,'4545','2026-09-29 13:57:02','2026-09-29 14:25:12'),(107,31,1,'Lote A3-01',80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(108,31,2,'Lote A3-02',80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(109,31,3,'Lote A3-03',80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(110,31,4,'Lote A3-04',80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(111,31,5,'Lote A3-05',80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(112,31,6,'Lote A3-06',80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(113,31,7,'Lote A3-07',80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(114,31,8,'Lote A3-08',80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(115,31,9,'Lote A3-09',80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(116,31,10,'Lote A3-10',80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(117,31,11,'Lote A3-11',80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(118,31,12,'Lote A3-12',80.00,29.98,NULL,'2026-09-29 14:00:46','2026-09-29 14:00:46'),(119,33,1,'lote 1-01',300.00,200.00,NULL,'2026-09-29 14:12:19','2026-09-29 14:12:19'),(120,33,2,'lote 1-02',300.00,200.00,NULL,'2026-09-29 14:12:19','2026-09-29 14:12:19'),(121,33,3,'lote 1-03',300.00,200.00,NULL,'2026-09-29 14:12:19','2026-09-29 14:12:19'),(122,33,4,'lote 1-04',300.00,200.00,NULL,'2026-09-29 14:12:19','2026-09-29 14:12:19'),(123,33,5,'lote 1-05',300.00,200.00,NULL,'2026-09-29 14:12:19','2026-09-29 14:12:19'),(124,33,6,'lote 1-06',300.00,200.00,NULL,'2026-09-29 14:12:19','2026-09-29 14:12:19'),(125,33,7,'lote 1-07',300.00,200.00,NULL,'2026-09-29 14:12:19','2026-09-29 14:12:19'),(126,33,8,'lote 1-08',300.00,200.00,NULL,'2026-09-29 14:12:19','2026-09-29 14:12:19'),(127,33,9,'lote 1-09',300.00,200.00,NULL,'2026-09-29 14:12:19','2026-09-29 14:12:19'),(128,33,10,'lote 1-10',300.00,200.00,NULL,'2026-09-29 14:12:19','2026-09-29 14:12:19'),(129,34,1,'lote 2-01',400.00,350.00,NULL,'2026-09-29 14:29:48','2026-09-29 14:29:48'),(130,34,2,'lote 2-02',400.00,350.00,NULL,'2026-09-29 14:29:48','2026-09-29 14:29:48'),(131,34,3,'lote 2-03',400.00,350.00,NULL,'2026-09-29 14:29:48','2026-09-29 14:29:48'),(132,34,4,'lote 2-04',400.00,350.00,NULL,'2026-09-29 14:29:48','2026-09-29 14:29:48'),(133,34,5,'lote 2-05',400.00,350.00,NULL,'2026-09-29 14:29:48','2026-09-29 14:29:48'),(134,34,6,'lote 2-06',400.00,350.00,NULL,'2026-09-29 14:29:48','2026-09-29 14:29:48'),(135,34,7,'lote 2-07',400.00,350.00,NULL,'2026-09-29 14:29:48','2026-09-29 14:29:48'),(136,34,8,'lote 2-08',400.00,350.00,NULL,'2026-09-29 14:29:49','2026-09-29 14:29:49'),(137,34,9,'lote 2-09',400.00,350.00,NULL,'2026-09-29 14:29:49','2026-09-29 14:29:49'),(138,34,10,'lote 2-10',400.00,350.00,NULL,'2026-09-29 14:29:49','2026-09-29 14:29:49'),(139,34,11,'lote 2-11',400.00,350.00,NULL,'2026-09-29 14:29:49','2026-09-29 14:29:49'),(140,34,12,'lote 2-12',400.00,350.00,NULL,'2026-09-29 14:29:49','2026-09-29 14:29:49'),(141,34,13,'lote 2-13',400.00,350.00,NULL,'2026-09-29 14:29:49','2026-09-29 14:29:49'),(142,34,14,'lote 2-14',400.00,350.00,NULL,'2026-09-29 14:29:49','2026-09-29 14:29:49'),(143,34,15,'lote 2-15',400.00,350.00,NULL,'2026-09-29 14:29:49','2026-09-29 14:29:49'),(144,34,16,'lote 2-16',400.00,350.00,NULL,'2026-09-29 14:29:49','2026-09-29 14:29:49');
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `desempenho_lotes`
--

LOCK TABLES `desempenho_lotes` WRITE;
/*!40000 ALTER TABLE `desempenho_lotes` DISABLE KEYS */;
INSERT INTO `desempenho_lotes` VALUES (9,23,478.00,40,'2026-10-05',0.00,0.00,0.00,0.00,0.00,'2026-10-05 17:49:50','2026-10-05 17:49:50');
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
  CONSTRAINT `estoque_movimentacoes_racao_id_foreign` FOREIGN KEY (`racao_id`) REFERENCES `racaos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estoque_movimentacoes`
--

LOCK TABLES `estoque_movimentacoes` WRITE;
/*!40000 ALTER TABLE `estoque_movimentacoes` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fazendas`
--

LOCK TABLES `fazendas` WRITE;
/*!40000 ALTER TABLE `fazendas` DISABLE KEYS */;
INSERT INTO `fazendas` VALUES (1,1,'Fazenda Talismã','Bruna Figueira Silva','Tambaú - SP','13710-224','Tambaú','SP',-21.7021054,-47.2652065,70.00,'2026-08-25 13:57:05','2026-08-25 13:57:05'),(2,1,'Fazenda Morandin','Bruna Figueira Silva','Tambaú - SP','13710-224','Tambaú','SP',-21.6931734,-47.2739316,56.00,'2026-08-25 13:57:26','2026-08-25 13:57:26'),(3,1,'Fazenda São João','Bruna Figueira Silva','São João da Boa Vista - SP','13870-020','São João da Boa Vista','SP',-21.9704416,-46.7953369,80.00,'2026-08-25 13:58:04','2026-08-25 13:58:04'),(4,2,'Fazenda Santa Helena','Maria Eduarda Evangelista','Adolfo - SP','35609-00','Adolfo','SP',NULL,NULL,10.00,'2026-08-25 14:02:35','2026-08-25 14:02:35'),(5,3,'Fazenda del gado','Yasmin','Tambaú - SP','13710-083','Tambaú','SP',NULL,NULL,10.00,'2026-08-25 14:13:51','2026-08-25 14:13:51'),(8,4,'ARTHUR BARBOSA AZEVEDO','ARTHUR BARBOSA AZEVEDO','Araruna - PB','92480-00','Araruna','PB',NULL,NULL,1.29,'2026-08-25 16:51:26','2026-08-25 16:51:26'),(9,5,'São José','Lorena','São José do Rio Pardo - SP',NULL,'São José do Rio Pardo','SP',NULL,NULL,1.01,'2026-08-25 16:51:56','2026-08-25 16:51:56'),(10,7,'Fazenda São Miguel','Maria Fernanda Galdino de Souza Felipe','Tambaú - SP','13710-000','Tambaú','SP',NULL,NULL,12.06,'2026-08-25 16:58:34','2026-08-25 16:58:34'),(11,6,'FAZENDA SENAI','Arthur Povidelo','Tambaú - SP','13710-290','Tambaú','SP',-21.7126435,-47.2442523,210003.00,'2026-08-25 17:05:00','2026-08-25 17:05:00'),(12,8,'Fazenda Talismã','Bruna Figueira Silva','Tambaú - SP','13711-244','Tambaú','SP',-21.7105437,-47.2748834,56.00,'2026-08-31 16:09:14','2026-08-31 16:09:14'),(18,14,'bexe','JOÃO EMANOEL PORFIRIO DE SOUZA','Santo Antônio de Jesus - BA','44444-444','Santo Antônio de Jesus','BA',-12.9555997,-39.2609139,10.00,'2026-09-29 13:36:22','2026-09-29 13:36:22'),(21,13,'Fazenda Santa Helena','Maria Eduarda Evangelista','Areias - SP','12820-000','Areias','SP',NULL,NULL,10.00,'2026-09-29 13:47:43','2026-09-29 13:52:18'),(22,13,'Fazenda Vila Nova','Maria Eduarda Evangelista','Areado - MG','37140-000','Areado','MG',NULL,NULL,20.00,'2026-09-29 13:49:06','2026-09-29 13:51:38'),(23,13,'Fazenda São João','Maria Eduarda Evangelista','Areial - PB','58140-000','Areial','PB',NULL,NULL,30.00,'2026-09-29 13:49:48','2026-09-29 13:52:52'),(24,15,'zazafabu','zaz','Tambaú - SP','13711-412','Tambaú','SP',NULL,NULL,34444.00,'2026-09-29 13:55:26','2026-09-29 13:55:26'),(25,16,'Fazenda del gado','Yasmin','Tambaú - SP','13710-083','Tambaú','SP',NULL,NULL,10.00,'2026-09-29 14:10:39','2026-09-29 14:10:39'),(26,16,'Fazenda del gado','Yasmin','Tambaú - SP','13710-083','Tambaú','SP',NULL,NULL,10.00,'2026-09-29 14:27:48','2026-09-29 14:27:48'),(29,19,'JOÃO EMANOEL PORFIRIO DE SOUZA','Murilo','Santo Antônio de Jesus - BA','44444-444','Santo Antônio de Jesus','BA',NULL,NULL,9999999.00,'2026-09-29 17:38:32','2026-09-29 17:38:32');
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
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lote_consumo_snapshots`
--

LOCK TABLES `lote_consumo_snapshots` WRITE;
/*!40000 ALTER TABLE `lote_consumo_snapshots` DISABLE KEYS */;
INSERT INTO `lote_consumo_snapshots` VALUES (7,9,NULL,'2026-08-25',65,0,'status-warning','2026-08-25 16:55:51','2026-08-25 16:55:51'),(8,11,7,'2026-08-25',899,30,'status-success','2026-08-25 17:10:04','2026-08-25 17:10:04'),(9,10,NULL,'2026-08-25',100,0,'status-warning','2026-08-25 17:39:08','2026-08-25 17:39:08'),(10,12,NULL,'2026-08-31',600,0,'status-warning','2026-08-31 16:18:09','2026-08-31 16:18:09'),(11,13,NULL,'2026-08-31',300,0,'status-warning','2026-08-31 16:18:10','2026-08-31 16:18:10'),(70,23,NULL,'2026-09-22',40,11,'status-warning','2026-09-22 17:11:16','2026-09-22 17:11:16'),(71,24,NULL,'2026-09-22',10,13,'status-warning','2026-09-22 17:11:16','2026-09-22 17:11:16'),(72,25,NULL,'2026-09-22',100,5,'status-warning','2026-09-22 17:11:16','2026-09-22 17:11:16'),(73,26,NULL,'2026-09-22',20,0,'status-warning','2026-09-22 17:33:22','2026-09-22 17:33:22'),(74,23,NULL,'2026-09-28',40,11,'status-warning','2026-09-28 13:42:32','2026-09-28 13:42:32'),(75,24,NULL,'2026-09-28',10,13,'status-warning','2026-09-28 13:42:32','2026-09-28 13:42:32'),(76,25,NULL,'2026-09-28',100,5,'status-warning','2026-09-28 13:42:32','2026-09-28 13:42:32'),(77,26,NULL,'2026-09-28',20,0,'status-warning','2026-09-28 13:42:32','2026-09-28 13:42:32'),(78,27,NULL,'2026-09-28',100,15,'status-warning','2026-09-28 13:46:50','2026-09-28 13:46:50'),(79,23,NULL,'2026-09-29',40,11,'status-warning','2026-09-29 13:22:43','2026-09-29 13:22:43'),(80,24,NULL,'2026-09-29',10,13,'status-warning','2026-09-29 13:22:43','2026-09-29 13:22:43'),(81,25,NULL,'2026-09-29',100,5,'status-warning','2026-09-29 13:22:43','2026-09-29 13:22:43'),(82,26,NULL,'2026-09-29',20,0,'status-warning','2026-09-29 13:22:43','2026-09-29 13:22:43'),(83,27,NULL,'2026-09-29',99,15,'status-warning','2026-09-29 13:22:43','2026-09-29 21:06:12'),(84,28,19,'2026-09-29',300,20,'status-success','2026-09-29 13:41:52','2026-09-29 13:41:52'),(85,29,20,'2026-09-29',50,30,'status-success','2026-09-29 14:05:15','2026-09-29 14:12:31'),(86,31,NULL,'2026-09-29',79,0,'status-warning','2026-09-29 14:05:15','2026-09-29 14:05:15'),(87,30,22,'2026-09-29',30,50,'status-success','2026-09-29 14:05:15','2026-09-29 14:19:17'),(88,33,21,'2026-09-29',200,30,'status-success','2026-09-29 14:14:56','2026-09-29 14:14:56'),(89,23,3,'2026-10-05',40,11,'status-success','2026-10-05 14:21:29','2026-10-05 15:59:31'),(90,24,NULL,'2026-10-05',10,13,'status-warning','2026-10-05 14:21:29','2026-10-05 14:21:29'),(91,25,NULL,'2026-10-05',100,5,'status-warning','2026-10-05 14:21:29','2026-10-05 14:21:29'),(92,26,NULL,'2026-10-05',20,0,'status-warning','2026-10-05 14:21:29','2026-10-05 14:21:29'),(93,27,1,'2026-10-05',99,15,'status-success','2026-10-05 14:21:29','2026-10-05 16:00:11');
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
  CONSTRAINT `lotes_racao_id_foreign` FOREIGN KEY (`racao_id`) REFERENCES `racaos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lotes`
--

LOCK TABLES `lotes` WRITE;
/*!40000 ALTER TABLE `lotes` DISABLE KEYS */;
INSERT INTO `lotes` VALUES (2,4,NULL,NULL,'Lote A5','Bovino',NULL,'Leite','Fêmea','Acima de 3 anos',20,1,600.00,'2026-08-25','Ativo','2026-08-25 14:03:08','2026-08-25 14:03:08'),(8,5,NULL,NULL,'lote 1','Bovino',NULL,'Leite','Fêmea','Acima de 3 anos',200,1,370.00,'2026-08-25','Ativo','2026-08-25 14:14:40','2026-08-25 14:14:40'),(9,8,NULL,NULL,'arthur','Bovino',NULL,'Leite','Fêmea','0-6 meses',65,0,15.00,'2026-09-02','Ativo','2026-08-25 16:53:24','2026-08-25 16:54:58'),(10,9,NULL,NULL,'12','Bovino',NULL,'Reprodução','Misto','1-2 anos',100,1,44.98,'2026-08-25','Ativo','2026-08-25 16:54:33','2026-08-25 16:54:33'),(11,11,8,NULL,'LOTE 1','Bovino',NULL,'Corte','Macho','6-12 meses',899,1,555.00,'2026-08-25','Ativo','2026-08-25 17:06:04','2026-08-25 17:52:34'),(12,12,NULL,NULL,'Lote A1','Bovino',NULL,'Corte','Macho','1-2 anos',600,0,860.00,'2026-08-31','Ativo','2026-08-31 16:13:04','2026-08-31 16:13:04'),(13,12,NULL,NULL,'Lote A2','Bovino',NULL,'Leite','Fêmea','6-12 meses',300,0,690.00,'2026-08-31','Ativo','2026-08-31 16:15:16','2026-08-31 16:15:16'),(23,1,3,NULL,'Lote A1','Bovino','Nelore','Leite','Fêmea','1-2 anos',40,10,478.00,'2026-09-22','Ativo','2026-09-22 17:03:13','2026-10-05 15:59:29'),(24,1,NULL,NULL,'Lote B1','Equino','Manga Larga','Esporte','Macho','2-3 anos',10,4,780.00,'2026-09-22','Ativo','2026-09-22 17:06:08','2026-09-22 17:06:08'),(25,1,NULL,NULL,'Lote C1','Avicultura','Leghorn','Ovos','Fêmea','6-12 meses',100,25,3.00,'2026-09-22','Ativo','2026-09-22 17:10:36','2026-09-22 17:10:37'),(26,1,NULL,NULL,'Lote D1','Ovino','Santa Inês','Leite','Fêmea','1-2 anos',20,5,112.00,'2026-09-22','Ativo','2026-09-22 17:16:59','2026-09-22 17:16:59'),(27,1,1,NULL,'Lote A2','Bovino','Nelore','Corte','Macho','2-3 anos',99,30,890.00,'2026-09-28','Ativo','2026-09-28 13:44:47','2026-10-05 16:00:09'),(28,18,19,NULL,'a1','Bovino','nle','Reprodução','Fêmea','1-2 anos',300,3,350.00,'2026-09-29','Ativo','2026-09-29 13:38:32','2026-09-29 13:41:21'),(29,21,20,NULL,'Lote A1','Bovino','..','Corte','Macho','0-6 meses',50,10,500.00,'2026-09-29','Ativo','2026-09-29 13:55:15','2026-09-29 14:09:20'),(30,23,22,NULL,'Lote A2','Suíno','..','Reprodução','Fêmea','6-12 meses',30,1,200.00,'2026-09-29','Inativo','2026-09-29 13:57:02','2026-09-29 14:18:57'),(31,22,NULL,NULL,'Lote A3','Avicultura','..','Ovos','Fêmea','6-12 meses',79,12,8.00,'2026-09-29','Ativo','2026-09-29 14:00:46','2026-09-29 14:02:47'),(33,25,21,NULL,'lote 1','Bovino','Nelore','Reprodução','Misto','Acima de 3 anos',200,10,400.00,'2026-09-29','Ativo','2026-09-29 14:12:19','2026-09-29 14:14:42'),(34,26,NULL,NULL,'lote 2','Bovino','Nelore','Leite','Fêmea','2-3 anos',500,16,300.00,'2026-09-29','Ativo','2026-09-29 14:29:48','2026-09-29 14:29:49');
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
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026_08_25_110000_create_comentarios_table',1),(2,'2026_08_25_120000_add_nota_to_comentarios_table',2),(3,'2026_08_25_130000_add_categorias_to_comentarios_table',3),(4,'2026_08_25_140000_make_comentarios_publicos',4),(5,'2026_08_25_150000_add_estoque_total_to_racaos_table',5),(16,'2026_09_01_120000_create_racao_estoque_snapshots_table',9),(17,'2026_08_31_150400_create_ordenhas_table',10),(18,'0001_01_01_000000_create_users_table',11),(19,'0001_01_01_000001_create_cache_table',11),(20,'0001_01_01_000002_create_jobs_table',11),(21,'2026_08_04_180125_add_cpf_and_foto_to_cadastros_table',11),(22,'2026_08_04_181346_widen_telefone_on_cadastros_table',11),(23,'2026_08_04_183250_add_data_nascimento_and_seguranca_to_cadastros_table',11),(24,'2026_08_10_142801_add_endereco_to_fazendas_table',11),(25,'2026_08_10_152846_add_coordenadas_e_proprietario_to_fazendas_table',11),(26,'2026_08_10_161818_add_finalidade_sexo_idade_media_to_lotes_table',11),(27,'2026_08_10_181631_add_status_to_lotes_table',11),(28,'2026_08_11_120124_add_fazenda_id_to_racoes_table',11),(29,'2026_08_11_141827_create_lote_ocorrencias_table',11),(30,'2026_08_11_154706_add_doenca_fields_to_lote_ocorrencias_table',11),(31,'2026_08_11_160404_add_lote_origem_id_to_lotes_table',11),(32,'2026_08_11_163135_add_causa_to_lote_ocorrencias_table',11),(33,'2026_08_11_172500_add_valor_kg_to_racaos_table',11),(34,'2026_08_11_175642_add_sexo_idade_to_racaos_table',11),(35,'2026_08_11_180426_add_finalidade_to_racaos_table',11),(36,'2026_08_17_082800_create_codigos_verificacao_table',11),(37,'2026_08_17_102301_create_planos_table',11),(38,'2026_08_24_120000_add_racao_id_to_lotes_table',11),(39,'2026_08_24_130000_create_lote_consumo_snapshots_table',11),(40,'2026_08_25_090000_create_acessibilidade_preferencias_table',11),(41,'2026_08_25_091000_create_desempenho_lotes_table',11),(42,'2026_08_25_102713_create_personal_access_tokens_table',12),(43,'2026_09_08_090000_add_peso_e_quantidade_to_desempenho_lotes_table',12),(44,'2026_09_08_100000_add_email_verificado_em_to_cadastros_table',12),(45,'2026_09_08_110000_add_raca_to_lotes_table',13),(46,'2026_09_08_120000_add_quantidade_cochos_to_lotes_table',14),(47,'2026_09_08_120100_reestruturar_cochos_para_geracao_automatica',14),(48,'2026_08_01_000000_create_tabelas_base_agrifarm',15),(49,'2026_10_05_100000_corrigir_estrutura_agrifarm',15),(50,'2026_10_05_110000_corrigir_tipos_acessibilidade_preferencias',16);
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
  CONSTRAINT `ordenhas_responsavel_id_foreign` FOREIGN KEY (`responsavel_id`) REFERENCES `cadastros` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ordenhas`
--

LOCK TABLES `ordenhas` WRITE;
/*!40000 ALTER TABLE `ordenhas` DISABLE KEYS */;
INSERT INTO `ordenhas` VALUES (6,23,'2026-09-29','09:00:00','Manhã',30.00,1,NULL,'2026-09-29 14:04:12','2026-09-29 14:04:12'),(7,34,'2026-09-29','08:00:00','Manhã',120.00,16,'mafalda está machucada','2026-09-29 14:32:12','2026-09-29 14:32:12');
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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (4,'App\\Models\\Cadastro',1,'app-flutter','146451e98a07a838175afb1aa2eaa27768af680275daa63a6639a48f6cb173d6','[\"*\"]','2026-09-21 20:30:48',NULL,'2026-09-21 19:48:22','2026-09-21 20:30:48'),(9,'App\\Models\\Cadastro',1,'app-flutter','6e9db1c49d933942f2ecd0ebe1d9b6d660e82850d10f422a29a05a4c12a9900b','[\"*\"]','2026-09-22 15:57:13',NULL,'2026-09-22 14:04:20','2026-09-22 15:57:13'),(11,'App\\Models\\Cadastro',1,'app-flutter','7fc80f6910191c8e47bb75a23e0cdee2b4136ab9ef5dd2462c37a49d3b93ca0e','[\"*\"]','2026-09-22 17:23:16',NULL,'2026-09-22 16:22:24','2026-09-22 17:23:16'),(12,'App\\Models\\Cadastro',1,'app-flutter','70b70ce1c3a786bb62a5505db1539c1780faea81d21a70b2998e19a6e9d1a80a','[\"*\"]','2026-09-22 17:31:00',NULL,'2026-09-22 17:29:49','2026-09-22 17:31:00'),(13,'App\\Models\\Cadastro',1,'app-flutter','a90e1b5846b1b1beb4072f2e6b5c8f472948eaec9fbe7e3c5c52e3f7b8748401','[\"*\"]','2026-09-22 19:05:03',NULL,'2026-09-22 19:04:35','2026-09-22 19:05:03'),(14,'App\\Models\\Cadastro',1,'app-flutter','5b688f2ea40bf98b91aa2831fb162ece3de9667e2b7c77130c49b4f42be8840d','[\"*\"]','2026-09-28 16:30:50',NULL,'2026-09-28 16:30:09','2026-09-28 16:30:50'),(15,'App\\Models\\Cadastro',1,'app-flutter','0cdd934aed7db5d3448ad30aebfe6188bde5977f90851b129bb23b39d9799895','[\"*\"]','2026-09-28 16:32:59',NULL,'2026-09-28 16:31:40','2026-09-28 16:32:59'),(16,'App\\Models\\Cadastro',1,'app-flutter','ae6e313b0e39c516da2962dd9f4c0da32a70aad15b0551070c951856b3697088','[\"*\"]',NULL,NULL,'2026-09-29 13:26:33','2026-09-29 13:26:33'),(18,'App\\Models\\Cadastro',1,'app-flutter','200944805ebffd9349fc11dfb27262e4f5d1b4a936e690ad6486fe909eede889','[\"*\"]','2026-09-29 14:34:06',NULL,'2026-09-29 14:31:36','2026-09-29 14:34:06'),(19,'App\\Models\\Cadastro',1,'app-flutter','48749de8969dac397f874480c18103c5f1a7fae04a87dc147434196f71f4f51a','[\"*\"]','2026-09-29 14:43:26',NULL,'2026-09-29 14:42:53','2026-09-29 14:43:26'),(20,'App\\Models\\Cadastro',1,'app-flutter','181e01985e67e1b471e9473e3164f28835d7802d2adf4d87d9f16991ecb80769','[\"*\"]','2026-09-29 14:53:47',NULL,'2026-09-29 14:48:06','2026-09-29 14:53:47'),(21,'App\\Models\\Cadastro',17,'app-flutter','415c743f4653d0eec1441f2ffeeefd771fd359176f43cf877e2221870c70f271','[\"*\"]','2026-09-29 17:12:20',NULL,'2026-09-29 14:50:12','2026-09-29 17:12:20'),(29,'App\\Models\\Cadastro',1,'app-flutter','b31b401096d42d9265a128e56d5eb85a1a0dba0e64c8de637950b69a903daa97','[\"*\"]','2026-09-29 17:44:32',NULL,'2026-09-29 16:55:43','2026-09-29 17:44:32'),(31,'App\\Models\\Cadastro',1,'app-flutter','f501ac5cff6c178a4fde3a1c46c783e164d1d7ab67e2862cacca76e6f0beffe2','[\"*\"]','2026-09-29 17:45:17',NULL,'2026-09-29 17:45:00','2026-09-29 17:45:17'),(32,'App\\Models\\Cadastro',1,'app-flutter','5056a649d5ea0fa9db2fe3555bc5718742ca1be51b7f541cdaacce04fe36fe0c','[\"*\"]','2026-09-29 17:58:15',NULL,'2026-09-29 17:45:36','2026-09-29 17:58:15'),(33,'App\\Models\\Cadastro',1,'app-flutter','7d706e6f0838c3eade1600e2f4d3b6f56cc223e6047d49954f4560f09d0b3cc0','[\"*\"]','2026-09-29 20:02:49',NULL,'2026-09-29 19:06:29','2026-09-29 20:02:49'),(36,'App\\Models\\Cadastro',1,'app-flutter','6d0e4d9bd2acdb4ee14be2008e1c33f823adbe3940a4a9b35a0befa19b2c08fe','[\"*\"]','2026-09-29 20:26:30',NULL,'2026-09-29 20:06:13','2026-09-29 20:26:30'),(38,'App\\Models\\Cadastro',1,'app-flutter','725152d60ccec33f44287655182c336f711ba5f20a664e42cdb75f6977eb8b60','[\"*\"]','2026-09-29 21:06:12',NULL,'2026-09-29 21:01:49','2026-09-29 21:06:12'),(39,'App\\Models\\Cadastro',1,'app-flutter','8204705d11ff26d2c4c7165cfc3e66b8b3623907094e291cccb5012ddefa1347','[\"*\"]','2026-10-05 15:08:17',NULL,'2026-10-05 15:03:22','2026-10-05 15:08:17'),(40,'App\\Models\\Cadastro',1,'app-flutter','688fc45954281f92b6b9a0a0dd45dc84a78ec806634df0fb5239a840809a1eb0','[\"*\"]','2026-10-05 15:44:02',NULL,'2026-10-05 15:34:36','2026-10-05 15:44:02'),(41,'App\\Models\\Cadastro',1,'app-flutter','79c4462a7bc733efe6e0c0b00a0d34237a5250cfdf6b2ceb27c99b2735f80565','[\"*\"]','2026-10-05 15:57:54',NULL,'2026-10-05 15:52:38','2026-10-05 15:57:54'),(45,'App\\Models\\Cadastro',1,'app-flutter','829d86170769bd41d15e3dc472336784224098d3a0c312f2da0dade89827e6f1','[\"*\"]','2026-10-05 16:23:55',NULL,'2026-10-05 16:23:36','2026-10-05 16:23:55'),(46,'App\\Models\\Cadastro',1,'app-flutter','2eeac352c424eaacfb02efbf2695853e3761583526943b7afac4ca1faaab90a0','[\"*\"]','2026-10-05 17:50:34',NULL,'2026-10-05 17:43:50','2026-10-05 17:50:34'),(47,'App\\Models\\Cadastro',1,'app-flutter','e2fec59477039b907b36b71331a0aac888b4e23767a19529c29026c09df39932','[\"*\"]','2026-10-05 20:18:33',NULL,'2026-10-05 20:16:55','2026-10-05 20:18:33');
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
  `fornecedor` varchar(100) DEFAULT NULL,
  `reposicao` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `racaos_fazenda_id_foreign` (`fazenda_id`),
  CONSTRAINT `racaos_fazenda_id_foreign` FOREIGN KEY (`fazenda_id`) REFERENCES `fazendas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `racaos`
--

LOCK TABLES `racaos` WRITE;
/*!40000 ALTER TABLE `racaos` DISABLE KEYS */;
INSERT INTO `racaos` VALUES (1,1,'Bovino','Macho','1-2 anos','Corte',NULL,'kg','Ração Bovino Premium',500.00,700.00,15.00,1.50,'Fornecedor AgriFarm','2026-09-09','2026-08-25 14:09:39','2026-08-25 19:36:45'),(2,1,'Suíno','Macho','1-2 anos','Corte',NULL,'kg','Ração Pig Premium',500.00,600.00,12.00,1.00,'Fornecedor AgriFarm','2026-10-08','2026-08-25 14:10:21','2026-08-25 19:38:07'),(3,1,'Bovino','Fêmea','2-3 anos','Leite',NULL,'kg','Ração Leite Bovino',500.00,1000.00,11.00,1.80,'Fornecedor AgriFarm','2026-10-01','2026-08-25 14:15:52','2026-08-25 19:38:23'),(4,5,'Bovino','Fêmea','Acima de 3 anos','Leite',NULL,'kg','raçao magnus',500.00,1000.00,25.00,90.00,'magnus','2026-08-25','2026-08-25 14:16:17','2026-08-25 14:16:17'),(5,1,'Equino','Macho','2-3 anos','Esporte',NULL,'kg','Ração Magnus Cow',500.00,600.00,13.00,1.20,'Fornecedor AgriFarm','2026-08-01','2026-08-25 15:25:43','2026-08-25 19:38:36'),(6,1,'Avicultura','Fêmea','6-12 meses','Ovos',NULL,'kg','Ração Avicultura',400.00,400.00,5.00,0.90,'Fornecedor AgriFarm','2026-09-01','2026-08-25 15:28:13','2026-08-25 19:38:54'),(7,11,'Bovino','Macho','6-12 meses','Corte',NULL,'kg','Ração Premium',500.00,NULL,30.00,3.00,'LUIZIN FORNECEDÔ','2026-08-25','2026-08-25 17:09:19','2026-08-25 17:09:19'),(8,11,'Bovino','Macho','6-12 meses','Corte',NULL,'kg','Ração Premium 2',222.00,NULL,22.00,222.00,'LUIZIN FORNECEDÔ NOVO','2026-08-26','2026-08-25 17:52:34','2026-08-25 17:52:34'),(9,1,'Avicultura','Fêmea','6-12 meses','Ovos',NULL,'kg','Ração Avicultura',120.00,400.00,9.00,0.50,'Fornecedor AgriFarm','2026-09-09','2026-08-31 16:23:30','2026-08-31 16:24:01'),(12,NULL,'Bovino','Fêmea','2-3 anos','Leite',NULL,'kg','R1',100.00,500.00,10.00,2.00,'F','2026-09-21','2026-09-21 19:05:06','2026-09-21 19:05:06'),(13,NULL,'Bovino','Fêmea','2-3 anos','Leite',NULL,'kg','R1',100.00,500.00,10.00,2.00,'F','2026-09-21','2026-09-21 19:12:01','2026-09-21 19:12:01'),(14,18,'Bovino','Fêmea','1-2 anos','Reprodução',NULL,'kg','premio',500.00,1000.00,20.00,2.40,'jão','2026-09-29','2026-09-29 13:41:17','2026-09-29 13:41:17'),(15,18,'Bovino','Fêmea','1-2 anos','Reprodução',NULL,'kg','premio',500.00,1000.00,20.00,2.40,'jão','2026-09-29','2026-09-29 13:41:18','2026-09-29 13:41:18'),(16,18,'Bovino','Fêmea','1-2 anos','Reprodução',NULL,'kg','premio',500.00,1000.00,20.00,2.40,'jão','2026-09-29','2026-09-29 13:41:19','2026-09-29 13:41:19'),(17,18,'Bovino','Fêmea','1-2 anos','Reprodução',NULL,'kg','premio',500.00,1000.00,20.00,2.40,'jão','2026-09-29','2026-09-29 13:41:19','2026-09-29 13:41:19'),(18,18,'Bovino','Fêmea','1-2 anos','Reprodução',NULL,'kg','premio',500.00,1000.00,20.00,2.40,'jão','2026-09-29','2026-09-29 13:41:20','2026-09-29 13:41:20'),(19,18,'Bovino','Fêmea','1-2 anos','Reprodução',NULL,'kg','premio',500.00,1000.00,20.00,2.40,'jão','2026-09-29','2026-09-29 13:41:21','2026-09-29 13:41:21'),(20,21,'Bovino','Macho','0-6 meses','Corte',NULL,'kg','Ração Magnus',180.00,400.00,30.00,6.00,'Junior','2026-09-29','2026-09-29 14:09:20','2026-09-29 14:38:40'),(21,25,'Bovino','Misto','Acima de 3 anos','Reprodução',NULL,'kg','raçao magnus',200.00,600.00,30.00,2.30,'magnus','2026-09-29','2026-09-29 14:14:42','2026-09-29 14:14:42'),(22,23,'Suíno','Fêmea','6-12 meses','Reprodução',NULL,'kg','Ração Premiun',130.00,400.00,50.00,5.00,'Junior','2026-09-29','2026-09-29 14:18:57','2026-09-29 14:25:12');
/*!40000 ALTER TABLE `racaos` ENABLE KEYS */;
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
INSERT INTO `sessions` VALUES ('7a1tIhr8Yof2tAFaoR2MIaXFnjJYNBCldEchx7Vz',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNFVmQ2JqUjdHeTFsTHdmOFUyWndEUUd1Z2lzbHhFSnMwd3hnMUtCbiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zb2JyZSI7czo1OiJyb3V0ZSI7czoxMzoibGFuZGluZy5zb2JyZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1791217575),('9mZqOwMBS0hkSJyRsgIehQTrVJHGLUaVVu8xw96h',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoiekE3S2JYeFhaRkJRc2Fxd1pVQldVTFFrczlZaEdaWEYwRzNPUXJnVSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zb2x1Y2FvIjtzOjU6InJvdXRlIjtzOjE1OiJsYW5kaW5nLnNvbHVjYW8iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1791219381),('AVRzW7So6hl57s9ycUrTRAi5UuYvjETPDRu40tNh',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoibDFveEdoUXo5all0RnhwN3dDM0pnYndtWVJ5Y3JMaFdXU0FzSmNnRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9iZW5lZmljaW9zIjtzOjU6InJvdXRlIjtzOjE4OiJsYW5kaW5nLmJlbmVmaWNpb3MiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1791219386),('bNKWHKaSClObDhNLY6DH3aRc2KWXxR1Y0EMD36EA',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoianJlRTd6V1ZFakY0OWZDcGdoN0JlN2ljTTY0cFJaSjZsTGU0R0l6MCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zb2x1Y2FvIjtzOjU6InJvdXRlIjtzOjE1OiJsYW5kaW5nLnNvbHVjYW8iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1791217565),('cBAZvwBU2f97akt4SVcRFifqeNrUy89VZliJY7IO',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoicTRmUkhrMGhYNFllM3dEVFpMczJlc3pOOWN6RDhTV0dFVGoxa3FkRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wcm9ibGVtYSI7czo1OiJyb3V0ZSI7czoxNjoibGFuZGluZy5wcm9ibGVtYSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1791219378),('cBbv16OkGtI1hnMSstk0prOd3rg2NEFYPWehthW2',NULL,'127.0.0.1','node','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVGJLTWQwWVJYaUpjOXkwaURYSjliM2E5cHJWZmI1SFZoZXNZd24xTSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czoxMzoicHJvamV0by5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1791219165),('CGjK8wKJjOb1CXG94IMQ4ATB4KZ3L5Ocjnt7ayGE',NULL,'127.0.0.1','node','YTozOntzOjY6Il90b2tlbiI7czo0MDoiMVdDY1ZHeGM5RExuSll1TG5RUVFCeEY2UEhpenJjQ0M0bG5razI5UCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czoxMzoicHJvamV0by5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1791219881),('deZLFxx0JAEzYFo5ICSyT3xuyhQXKQalgkKTuIJz',NULL,'127.0.0.1','node','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZkEydzkycHpFSFY5U081YUlkVktlTWxqWUE2TUxCcWd3d2xPUmdRdCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czoxMzoicHJvamV0by5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1791218988),('dHbETpqxGQND3r9RolCkgO9SPst9Lz1FS6wCaeiA',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0','YTo3OntzOjY6Il90b2tlbiI7czo0MDoiWFJhT2ppcGdzeUhvZXNYN1JDOWI1T2xaUlZPVWJJQ09Ub09qVkhSSCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hbmltYWlzLXBvci1sb3RlIjtzOjU6InJvdXRlIjtzOjIyOiJwcm9qZXRvLmFuaW1haXNQb3JMb3RlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czoxMDoidXN1YXJpb19pZCI7aToxO3M6MTI6InVzdWFyaW9fbm9tZSI7czoyMDoiQnJ1bmEgRmlndWVpcmEgU2lsdmEiO3M6MTM6InVzdWFyaW9fZW1haWwiO3M6MjU6ImJydW5hZnNpbHZhLjA5QGljbG91ZC5jb20iO3M6MjI6ImZhemVuZGFfc2VsZWNpb25hZGFfaWQiO2k6MTt9',1791220617),('DuVwCTA6utYjIMqUYSUccsnyGxeAginKKYDfWBNV',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoiYlE2MjRtZGlzN1JTZGtoOHpLMzQ2N3d3NEJvakNsbm1CZXBlNkJqZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czoxMzoicHJvamV0by5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1791216727),('HRW9ecClVtQgGDNFIgVGdpPWmCQ1kQ46XzgJktrH',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoibVhraUpHQ0xRa21NTnFaVjUyWVZKNjRLUld3bk9nOUlmT3dicjJnYyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hdmFsaWFjb2VzIjtzOjU6InJvdXRlIjtzOjE3OiJjb21lbnRhcmlvLmxpc3RhciI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1791219393),('HsjMmkSELIoSxLQjtxBJKzdqqreDKYZIyT99LLTs',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoieThOc0huUjhtdm5BdElLeUpXQ2Q3aVE4TEh3cU85WDZLUDl5ekl4aiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zb2JyZSI7czo1OiJyb3V0ZSI7czoxMzoibGFuZGluZy5zb2JyZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1791219388),('J6QfraD1kGuQxkZVnjrKZgN13IoYZI6VpwLG9PwR',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoidHhIUXRYR21jUElFb2FHQUF1ajFhVjdJRlRBaURtMHFLUjN4SXpGNCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jb21vLWZ1bmNpb25hIjtzOjU6InJvdXRlIjtzOjIwOiJsYW5kaW5nLmNvbW9GdW5jaW9uYSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1791219383),('jLgunCqBvXvLHYl4gYmAxY8JWBRYbGek6syaeVf9',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNjgxMVdmaUphY2hHUXdmRkthdFE2Q2dTWURwWFdPOTlJQzAyWXJMMiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czoxMzoicHJvamV0by5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1791216882),('kNbrZyhTcnCmNseIYWYh7Rp3YLGjMmPx31Jyf4Sx',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoiMFJCc3FFb1YyaWw4QWNTUmdJODNZb0NDTEpxd0lTem1valNJNXRUaiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9iZW5lZmljaW9zIjtzOjU6InJvdXRlIjtzOjE4OiJsYW5kaW5nLmJlbmVmaWNpb3MiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1791217571),('lXp5BvEkxs6rnSD4OSB7q6cw3cld001jWq8oTCon',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoiY1ZrekdqVjJFWm5ORzlDS3lkVjRXZ2ZQRUQwQnJ2OG9KMm5RU1NSOCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jb21vLWZ1bmNpb25hIjtzOjU6InJvdXRlIjtzOjIwOiJsYW5kaW5nLmNvbW9GdW5jaW9uYSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1791217568),('MoLFdGSgbVPBHWL4HAzhS9CPUXUf9ONkobLX4gMX',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoicFBNRjFzdnpybFdta1dmOWxXcHV6SjdCMFpndTRLWEVnQ3BrOTRHWiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czoxMzoicHJvamV0by5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1791217557),('QvjxbEf0GzG7ulYfXhv09ncfhVi7F7lgWzemkgtD',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRHFTeEQwUUVzcnB4YjBqTDJQSjVtR0Y3MmJSYmhQNk9PWkg3c2swOSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jb250YXRvIjtzOjU6InJvdXRlIjtzOjE1OiJsYW5kaW5nLmNvbnRhdG8iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1791219390),('sFegFkUdQbwqd93F9idcl4lIip9kNWsHWW0H7AFr',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTHRnNk50WXpNNDJmaWNxRXBWWVZRYnBJZnh5TlVzdlZnRGdsa3NRUyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jb250YXRvIjtzOjU6InJvdXRlIjtzOjE1OiJsYW5kaW5nLmNvbnRhdG8iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1791217578),('SPfpJjL75456cyvYFdEWyewcZrOoyUeWGqINOGnI',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoiMlBNYkNsVmpSUzFITlhnc3c1N0ZiZ284ejdmMFlaTThTOWFzc1A1NCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czoxMzoicHJvamV0by5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1791219395),('TAHlYTcCA0A0M7bZTFibsEHgjDBIOYJmE1ojfuJC',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoiM2FKZmlnWm9vRXE3MlljVDBXRkJ0cFhhNkRLMVhHOHp3dWgwQXJqQyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czoxMzoicHJvamV0by5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1791218746),('V4CVulcdNnBY0q3Z6vb4zOaEaCYi57mVyjBgsXBI',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoibHFpc0tiUWlITkRyMXdKalFaWGpNcVZFdkx4M0x6Rk9UTjIxRnNSZSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hbmltYWlzLXBvci1sb3RlIjtzOjU6InJvdXRlIjtzOjIyOiJwcm9qZXRvLmFuaW1haXNQb3JMb3RlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1791218436),('wx2fzeYwKRUClZQ7WFq3QDfXdq5u2c8CWFosvbRu',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWmEwQ3pIR0J1Q1BDTHdxV25xbks5clU3ZnNsVXlMcnBYZnBHYm0wZSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wcm9ibGVtYSI7czo1OiJyb3V0ZSI7czoxNjoibGFuZGluZy5wcm9ibGVtYSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1791217561),('XVx2yneDRQtOtMjOVK57Ga97pmeZ9norvLqpABNW',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVVVtaGpSZTh1b2ZGV3RoWWxkdFREQ0VPTDI3NjM0SUcyWTFPelg4MCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hdmFsaWFjb2VzIjtzOjU6InJvdXRlIjtzOjE3OiJjb21lbnRhcmlvLmxpc3RhciI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1791217582),('xYPqvCsQhvsosesz1rOblnHxyujOcPc2joTiulvs',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNm5BM2FKTllzRlo3bWs4NU81SEpzeXBqcVFmWnlpSUljQ2tDQk9ZRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hbmltYWlzLXBvci1sb3RlIjtzOjU6InJvdXRlIjtzOjIyOiJwcm9qZXRvLmFuaW1haXNQb3JMb3RlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1791217105),('yaLD3rbdrxoP6sd6jdKz0ZZipdWWKrDmn4qjsYqU',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWFkwV1lpWWZIQXdMQndhcDBjQVRXNnBDSUFZY0VrYkFTOEJnNjhIbCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czoxMzoicHJvamV0by5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1791217357),('zZ46H7O4D64m9Ib3Nx3oVYqpPcEH3N8hV35yXOMs',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','YTozOntzOjY6Il90b2tlbiI7czo0MDoiSTlVWkR0MnpFNmVkc1FFTUhjSHZiT3lHVndLcmdTck5zVkRXaHZ3RiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czoxMzoicHJvamV0by5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1791216534);
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
-- Dumping routines for database 'agrifarm_mysqltcc'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-05 14:18:50
