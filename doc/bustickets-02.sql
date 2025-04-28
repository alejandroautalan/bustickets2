/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.1-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: 192.168.122.11    Database: bustickets2
-- ------------------------------------------------------
-- Server version	10.11.11-MariaDB-0+deb12u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `boleto`
--

DROP TABLE IF EXISTS `boleto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `boleto` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `origen_id` int(10) unsigned DEFAULT NULL,
  `destino_id` int(10) unsigned DEFAULT NULL,
  `viaje_fecha` date DEFAULT NULL,
  `viaje_hora` time DEFAULT NULL,
  `servicio_id` int(10) unsigned DEFAULT NULL,
  `precio` float DEFAULT NULL,
  `pasajero_id` int(10) unsigned DEFAULT NULL,
  `reserva_id` int(10) unsigned DEFAULT NULL,
  `asiento_id` int(10) unsigned DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `boleto_parada_FK` (`origen_id`),
  KEY `boleto_parada_FK_1` (`destino_id`),
  KEY `boleto_servicio_FK` (`servicio_id`),
  KEY `boleto_pasajero_FK` (`pasajero_id`),
  KEY `boleto_reserva_FK` (`reserva_id`),
  KEY `boleto_asiento_FK` (`asiento_id`),
  CONSTRAINT `boleto_asiento_FK` FOREIGN KEY (`asiento_id`) REFERENCES `transporte_asiento` (`id`),
  CONSTRAINT `boleto_parada_FK` FOREIGN KEY (`origen_id`) REFERENCES `parada` (`id`),
  CONSTRAINT `boleto_parada_FK_1` FOREIGN KEY (`destino_id`) REFERENCES `parada` (`id`),
  CONSTRAINT `boleto_pasajero_FK` FOREIGN KEY (`pasajero_id`) REFERENCES `pasajero` (`id`),
  CONSTRAINT `boleto_reserva_FK` FOREIGN KEY (`reserva_id`) REFERENCES `reserva` (`id`),
  CONSTRAINT `boleto_servicio_FK` FOREIGN KEY (`servicio_id`) REFERENCES `servicio` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boleto`
--

LOCK TABLES `boleto` WRITE;
/*!40000 ALTER TABLE `boleto` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `boleto` VALUES
(5,1,2,NULL,NULL,1,300,1,1,4,1),
(8,1,2,NULL,NULL,1,300,2,3,3,1);
/*!40000 ALTER TABLE `boleto` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `ciudad`
--

DROP TABLE IF EXISTS `ciudad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ciudad` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(128) NOT NULL,
  `provincia_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ciudad_provincia_FK` (`provincia_id`),
  CONSTRAINT `ciudad_provincia_FK` FOREIGN KEY (`provincia_id`) REFERENCES `provincia` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ciudad`
--

LOCK TABLES `ciudad` WRITE;
/*!40000 ALTER TABLE `ciudad` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `ciudad` VALUES
(1,'Santiago del Estero (capital)',1),
(2,'La Banda',1),
(3,'Vilmer',1),
(4,'Beltran',1),
(5,'Forres',1),
(6,'Taboada',1),
(7,'Añatuya',1),
(8,'Termas',1),
(9,'San Miguel (capital)',2);
/*!40000 ALTER TABLE `ciudad` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `fos_user_user`
--

DROP TABLE IF EXISTS `fos_user_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fos_user_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(180) NOT NULL,
  `username_canonical` varchar(180) NOT NULL,
  `email` varchar(180) NOT NULL,
  `email_canonical` varchar(180) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `confirmation_token` varchar(180) DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext NOT NULL COMMENT '(DC2Type:array)',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_C560D76192FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_C560D761A0D96FBF` (`email_canonical`),
  UNIQUE KEY `UNIQ_C560D761C05FB297` (`confirmation_token`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fos_user_user`
--

LOCK TABLES `fos_user_user` WRITE;
/*!40000 ALTER TABLE `fos_user_user` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `fos_user_user` VALUES
(23,'admin','admin','admin@admin.com','admin@admin.com',1,NULL,'$2y$13$emjYOen20Lq5K/GGO05Y0efWwIePe//K3GuN08MzlZ/OqjoQPwg.y','2025-04-28 00:41:39',NULL,NULL,'a:1:{i:0;s:16:\"ROLE_SUPER_ADMIN\";}','2019-10-01 15:43:56','2025-04-28 00:41:39'),
(24,'root','root','root@admin.com','root@admin.com',1,NULL,'$2y$13$bi.TFLHyaNiepV9Rb9dfHu4L03Pd5wLuJkE1dh1Wxz8cTUN9QaknS','2025-03-26 10:04:01',NULL,NULL,'a:1:{i:0;s:16:\"ROLE_SUPER_ADMIN\";}','2024-12-26 12:59:20','2025-03-26 10:04:01');
/*!40000 ALTER TABLE `fos_user_user` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `marca`
--

DROP TABLE IF EXISTS `marca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `marca` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marca`
--

LOCK TABLES `marca` WRITE;
/*!40000 ALTER TABLE `marca` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `marca` VALUES
(2,'Ford',NULL),
(3,'Fiat',NULL);
/*!40000 ALTER TABLE `marca` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `modelo`
--

DROP TABLE IF EXISTS `modelo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `modelo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `marca_id` int(10) unsigned DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `version` varchar(255) NOT NULL,
  `activo` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F0D76C4681EF0041` (`marca_id`),
  CONSTRAINT `modelo_marca_FK` FOREIGN KEY (`marca_id`) REFERENCES `marca` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modelo`
--

LOCK TABLES `modelo` WRITE;
/*!40000 ALTER TABLE `modelo` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `modelo` VALUES
(2,2,'Ranger 4x2','1',1),
(3,2,'Ranger 4x4','1',1),
(4,3,'Generico','1',1);
/*!40000 ALTER TABLE `modelo` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `pago`
--

DROP TABLE IF EXISTS `pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `monto` decimal(10,0) NOT NULL,
  `fecha` date NOT NULL,
  `tipo` int(11) NOT NULL,
  `observacion` longtext DEFAULT NULL,
  `usuario` int(11) DEFAULT NULL,
  `numero_comprobante` varchar(255) DEFAULT NULL,
  `importe_recibido` decimal(10,0) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pago`
--

LOCK TABLES `pago` WRITE;
/*!40000 ALTER TABLE `pago` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `pago` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `parada`
--

DROP TABLE IF EXISTS `parada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `parada` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(128) NOT NULL,
  `provincia_id` int(10) unsigned DEFAULT NULL,
  `ciudad_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parada_provincia_FK` (`provincia_id`),
  KEY `parada_ciudad_FK` (`ciudad_id`),
  CONSTRAINT `parada_ciudad_FK` FOREIGN KEY (`ciudad_id`) REFERENCES `ciudad` (`id`),
  CONSTRAINT `parada_provincia_FK` FOREIGN KEY (`provincia_id`) REFERENCES `provincia` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parada`
--

LOCK TABLES `parada` WRITE;
/*!40000 ALTER TABLE `parada` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `parada` VALUES
(1,'Terminal Omnibus Banda',1,2),
(2,'Terminal Santiago',1,1),
(3,'Terminal Beltran',1,6),
(4,'Terminal Forres',1,5),
(5,'Terminal Vilmer',1,3),
(6,'Terminal Temas',1,8),
(7,'Terminal Tucuman',2,9);
/*!40000 ALTER TABLE `parada` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `pasajero`
--

DROP TABLE IF EXISTS `pasajero`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pasajero` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `apellido` varchar(255) NOT NULL,
  `dni` int(11) NOT NULL,
  `edad` int(11) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `telefono` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pasajero`
--

LOCK TABLES `pasajero` WRITE;
/*!40000 ALTER TABLE `pasajero` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `pasajero` VALUES
(1,'Pepe','Sanchez',22333444,NULL,NULL,NULL,NULL),
(2,'Tomas','Anderson',77666555,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `pasajero` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `provincia`
--

DROP TABLE IF EXISTS `provincia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `provincia` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provincia`
--

LOCK TABLES `provincia` WRITE;
/*!40000 ALTER TABLE `provincia` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `provincia` VALUES
(1,'Santiago del Estero'),
(2,'Tucuman');
/*!40000 ALTER TABLE `provincia` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `reserva`
--

DROP TABLE IF EXISTS `reserva`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reserva` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `estado` int(11) DEFAULT 0,
  `origen_id` int(10) unsigned DEFAULT NULL,
  `destino_id` int(10) unsigned DEFAULT NULL,
  `servicio_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reserva`
--

LOCK TABLES `reserva` WRITE;
/*!40000 ALTER TABLE `reserva` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `reserva` VALUES
(1,2,1,2,1),
(3,2,1,2,1);
/*!40000 ALTER TABLE `reserva` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `servicio`
--

DROP TABLE IF EXISTS `servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `servicio` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `partida` datetime NOT NULL,
  `llegada` datetime NOT NULL,
  `trayecto_id` int(10) unsigned DEFAULT NULL,
  `transporte_id` int(10) unsigned DEFAULT NULL,
  `vehiculo_id` int(10) unsigned DEFAULT NULL,
  `estado` smallint(6) DEFAULT NULL COMMENT 'Draft, Preventa, Transporte, Finalizado',
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `servicio_trayecto_FK` (`trayecto_id`),
  KEY `servicio_transporte_FK` (`transporte_id`),
  KEY `servicio_vehiculo_FK` (`vehiculo_id`),
  CONSTRAINT `servicio_transporte_FK` FOREIGN KEY (`transporte_id`) REFERENCES `transporte` (`id`),
  CONSTRAINT `servicio_trayecto_FK` FOREIGN KEY (`trayecto_id`) REFERENCES `trayecto` (`id`),
  CONSTRAINT `servicio_vehiculo_FK` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculo` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servicio`
--

LOCK TABLES `servicio` WRITE;
/*!40000 ALTER TABLE `servicio` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `servicio` VALUES
(1,'2025-04-17 07:00:00','2025-04-17 08:00:00',1,1,1,1,'Banda Santiago - Ida - Comun'),
(2,'2025-04-22 07:00:00','2025-04-22 13:00:00',2,1,2,1,'Forres - Termas - Comun'),
(3,'2025-04-22 07:00:00','2025-04-22 12:00:00',2,3,NULL,1,'Forres-Tremas Bus Directo');
/*!40000 ALTER TABLE `servicio` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `transporte`
--

DROP TABLE IF EXISTS `transporte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transporte` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `grilla_rows` smallint(5) unsigned DEFAULT 1,
  `grilla_cols` int(10) unsigned DEFAULT 1,
  `plantas` smallint(5) unsigned DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transporte`
--

LOCK TABLES `transporte` WRITE;
/*!40000 ALTER TABLE `transporte` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `transporte` VALUES
(1,'Combi 6 Asientos',2,3,1),
(2,'Combi 8 asientos',4,2,1),
(3,'BUS - 41 Asientos',10,3,2);
/*!40000 ALTER TABLE `transporte` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `transporte_asiento`
--

DROP TABLE IF EXISTS `transporte_asiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transporte_asiento` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transporte_id` int(10) unsigned DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `categoria` smallint(6) DEFAULT NULL COMMENT 'Premium\nSemicama\nComun',
  `planta` smallint(5) unsigned DEFAULT 0,
  `row` smallint(6) DEFAULT 0,
  `col` smallint(5) unsigned DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `transporte_asiento_transporte_FK` (`transporte_id`),
  CONSTRAINT `transporte_asiento_transporte_FK` FOREIGN KEY (`transporte_id`) REFERENCES `transporte` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transporte_asiento`
--

LOCK TABLES `transporte_asiento` WRITE;
/*!40000 ALTER TABLE `transporte_asiento` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `transporte_asiento` VALUES
(1,1,1,1,0,0,0),
(2,1,2,1,0,0,1),
(3,1,3,1,0,0,2),
(4,1,4,1,0,1,0),
(5,1,5,1,0,1,1),
(6,1,6,1,0,1,2),
(9,2,1,1,0,0,0),
(10,2,2,1,0,0,1),
(11,2,3,1,0,1,0),
(12,2,4,1,0,1,1),
(13,2,5,1,0,2,0),
(14,2,6,1,0,2,1),
(15,2,7,1,0,3,0),
(16,2,8,1,0,3,1),
(17,3,1,1,0,5,0),
(18,3,2,1,0,5,1),
(19,3,3,1,0,6,0),
(20,3,4,1,0,6,1),
(21,3,5,1,0,7,0),
(22,3,6,1,0,7,1),
(23,3,7,1,0,7,2),
(24,3,8,1,0,8,0),
(25,3,9,1,0,8,1),
(26,3,10,1,0,8,2),
(27,3,11,1,0,9,0),
(28,3,12,1,0,9,1),
(29,3,13,1,0,9,2),
(30,3,14,1,1,0,0),
(31,3,15,1,1,0,1),
(32,3,16,1,1,0,2),
(33,3,17,1,1,1,0),
(34,3,18,1,1,1,1),
(35,3,19,1,1,1,2),
(36,3,20,1,1,2,0),
(37,3,21,1,1,2,1),
(38,3,22,1,1,3,0),
(39,3,23,1,1,3,1),
(40,3,24,1,1,4,0),
(41,3,25,1,1,4,1),
(42,3,26,1,1,4,2),
(43,3,27,1,1,5,0),
(44,3,28,1,1,5,1),
(45,3,29,1,1,5,2),
(46,3,30,1,1,6,0),
(47,3,31,1,1,6,1),
(48,3,32,1,1,6,2),
(49,3,33,1,1,7,0),
(50,3,34,1,1,7,1),
(51,3,35,1,1,7,2),
(52,3,36,1,1,8,0),
(53,3,37,1,1,8,1),
(54,3,38,1,1,8,2),
(55,3,39,1,1,9,0),
(56,3,40,1,1,9,1),
(57,3,41,1,1,9,2);
/*!40000 ALTER TABLE `transporte_asiento` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `trayecto`
--

DROP TABLE IF EXISTS `trayecto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `trayecto` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(128) NOT NULL,
  `origen_id` int(10) unsigned DEFAULT NULL,
  `destino_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `trayecto_origen_FK` (`origen_id`),
  KEY `trayecto_destino_FK` (`destino_id`),
  CONSTRAINT `trayecto_destino_FK` FOREIGN KEY (`destino_id`) REFERENCES `parada` (`id`),
  CONSTRAINT `trayecto_origen_FK` FOREIGN KEY (`origen_id`) REFERENCES `parada` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trayecto`
--

LOCK TABLES `trayecto` WRITE;
/*!40000 ALTER TABLE `trayecto` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `trayecto` VALUES
(1,'Banda-Santiago-01',1,2),
(2,'Forres-Termas',4,6),
(3,'Santiago - Tucuman',2,7);
/*!40000 ALTER TABLE `trayecto` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `trayecto_parada`
--

DROP TABLE IF EXISTS `trayecto_parada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `trayecto_parada` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parada_id` int(10) unsigned DEFAULT NULL,
  `trayecto_id` int(10) unsigned DEFAULT NULL,
  `nro_orden` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `trayecto_parada_trayecto_FK` (`trayecto_id`),
  KEY `trayecto_parada_parada_FK` (`parada_id`),
  CONSTRAINT `trayecto_parada_parada_FK` FOREIGN KEY (`parada_id`) REFERENCES `parada` (`id`),
  CONSTRAINT `trayecto_parada_trayecto_FK` FOREIGN KEY (`trayecto_id`) REFERENCES `trayecto` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trayecto_parada`
--

LOCK TABLES `trayecto_parada` WRITE;
/*!40000 ALTER TABLE `trayecto_parada` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `trayecto_parada` VALUES
(3,3,2,1),
(5,5,2,2),
(7,6,3,1);
/*!40000 ALTER TABLE `trayecto_parada` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `vehiculo`
--

DROP TABLE IF EXISTS `vehiculo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehiculo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL COMMENT 'Nombre/modelo',
  `placa` varchar(32) DEFAULT NULL COMMENT 'placa/patente',
  `marca_id` int(10) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `transporte_id` int(10) unsigned DEFAULT NULL,
  `modelo_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehiculo_transporte_FK` (`transporte_id`),
  KEY `vehiculo_modelo_FK` (`modelo_id`),
  KEY `vehiculo_marca_FK` (`marca_id`),
  CONSTRAINT `vehiculo_marca_FK` FOREIGN KEY (`marca_id`) REFERENCES `marca` (`id`),
  CONSTRAINT `vehiculo_modelo_FK` FOREIGN KEY (`modelo_id`) REFERENCES `modelo` (`id`),
  CONSTRAINT `vehiculo_transporte_FK` FOREIGN KEY (`transporte_id`) REFERENCES `transporte` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehiculo`
--

LOCK TABLES `vehiculo` WRITE;
/*!40000 ALTER TABLE `vehiculo` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `vehiculo` VALUES
(1,'Combi Roja','ARK00001',3,1,1,4),
(2,'Combi blanca','ARK000002',2,1,1,3);
/*!40000 ALTER TABLE `vehiculo` ENABLE KEYS */;
UNLOCK TABLES;
commit;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-04-28  2:53:09
