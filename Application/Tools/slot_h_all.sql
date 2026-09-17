-- MySQL dump 10.13  Distrib 8.0.46, for Linux (x86_64)
--
-- Host: localhost    Database: start
-- ------------------------------------------------------
-- Server version	8.0.46-0ubuntu0.24.04.4

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `start`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `start` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `start`;

--
-- Table structure for table `aliases`
--

DROP TABLE IF EXISTS `aliases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aliases` (
  `alias_id` int NOT NULL AUTO_INCREMENT,
  `alias_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alias_value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alias_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alias_type` tinyint(1) DEFAULT '0',
  `project_id` int DEFAULT NULL,
  PRIMARY KEY (`alias_id`),
  FULLTEXT KEY `NewIndex1` (`alias_link`),
  FULLTEXT KEY `NewIndex2` (`alias_value`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aliases`
--

LOCK TABLES `aliases` WRITE;
/*!40000 ALTER TABLE `aliases` DISABLE KEYS */;
INSERT INTO `aliases` VALUES (1,'Free/About/index','about','About page',0,1),(2,'Free/Docs/index','doc','Documentation',0,1),(3,'Free/Contact/index','contacts','Contacts',0,1);
/*!40000 ALTER TABLE `aliases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_checks`
--

DROP TABLE IF EXISTS `auth_checks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_checks` (
  `auth_check_id` int NOT NULL AUTO_INCREMENT,
  `auth_check_time` int NOT NULL,
  `auth_check_hash` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `auth_check_attempt` int NOT NULL,
  PRIMARY KEY (`auth_check_id`),
  KEY `auth_check_time_key` (`auth_check_time`),
  KEY `auth_check_hash_key` (`auth_check_hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_checks`
--

LOCK TABLES `auth_checks` WRITE;
/*!40000 ALTER TABLE `auth_checks` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_checks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brutus`
--

DROP TABLE IF EXISTS `brutus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brutus` (
  `brutus_id` int NOT NULL AUTO_INCREMENT,
  `brutus_url` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `brutus_counter` int DEFAULT NULL,
  PRIMARY KEY (`brutus_id`)
) ENGINE=InnoDB AUTO_INCREMENT=974 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brutus`
--

LOCK TABLES `brutus` WRITE;
/*!40000 ALTER TABLE `brutus` DISABLE KEYS */;
/*!40000 ALTER TABLE `brutus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `constants`
--

DROP TABLE IF EXISTS `constants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `constants` (
  `constant_id` int NOT NULL AUTO_INCREMENT,
  `constant_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `constant_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `constant_type` tinyint(1) DEFAULT NULL,
  `constant_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `constant_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `constant_group` int DEFAULT NULL,
  `project_id` int DEFAULT NULL,
  PRIMARY KEY (`constant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=191 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `constants`
--

LOCK TABLES `constants` WRITE;
/*!40000 ALTER TABLE `constants` DISABLE KEYS */;
INSERT INTO `constants` VALUES (1,'60','online_timeout',1,'User Online Time (sec)','{\"min\":60,\"max\":300}',2,51),(2,'31536000','session_lifetime',1,'Session Lifetime (sec)','{\"min\":3600,\"max\":31536000}',2,51),(3,'1','role_superadmin',0,'Superradmin Role ID',NULL,2,51),(4,'3','max_attempt_allow',1,'Number of authorized authorization attempts','{\"min\":3,\"max\":10}',2,51),(6,'6','min_password_length',1,'Minimum password or login length','{\"min\":6,\"max\":32}',2,51),(7,'1','password_has_decimal',1,'Password must contain numbers','{\"boolean\":true}',2,51),(8,'1','password_has_uppercase',1,'Password must be uppercase','{\"boolean\":true}',2,51),(9,'1','password_has_lowercase',1,'Password must be lowercase','{\"boolean\":true}',2,51),(10,NULL,'password_has_symbol',1,'Password must contain characters',NULL,2,51),(116,'120','auth_timeout',1,'Authorization freeze time','{\"min\":30,\"max\":300}',2,51),(148,'180','short_session_ttl',1,'Session duration in minutes with the \"Remember me\" flag turned off','{\"min\":0,\"max\":43200}',2,51);
/*!40000 ALTER TABLE `constants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entities`
--

DROP TABLE IF EXISTS `entities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `entities` (
  `entity_id` int NOT NULL AUTO_INCREMENT,
  `entity_entity` int DEFAULT NULL,
  `entity_entity_type` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `entity_value` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entities`
--

LOCK TABLES `entities` WRITE;
/*!40000 ALTER TABLE `entities` DISABLE KEYS */;
/*!40000 ALTER TABLE `entities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `icons`
--

DROP TABLE IF EXISTS `icons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `icons` (
  `icon_id` int NOT NULL AUTO_INCREMENT,
  `icon_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `icon_constant` int DEFAULT NULL,
  `icon_frontend` int DEFAULT NULL,
  PRIMARY KEY (`icon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `icons`
--

LOCK TABLES `icons` WRITE;
/*!40000 ALTER TABLE `icons` DISABLE KEYS */;
/*!40000 ALTER TABLE `icons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `images` (
  `image_id` int NOT NULL AUTO_INCREMENT,
  `image_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `image_entity` int DEFAULT NULL,
  `image_entity_type` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `image_status` int DEFAULT '2',
  `image_rotate` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `image_width` int DEFAULT NULL,
  `image_height` int DEFAULT NULL,
  `image_real_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `image_size` int DEFAULT NULL,
  `person_id` int DEFAULT NULL,
  PRIMARY KEY (`image_id`),
  KEY `NewIndex1` (`deleted_at`),
  KEY `img_entity_key` (`image_entity`,`image_entity_type`),
  FULLTEXT KEY `types` (`image_entity_type`),
  FULLTEXT KEY `names` (`image_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `images`
--

LOCK TABLES `images` WRITE;
/*!40000 ALTER TABLE `images` DISABLE KEYS */;
/*!40000 ALTER TABLE `images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_actives`
--

DROP TABLE IF EXISTS `person_actives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `person_actives` (
  `person_active_id` int NOT NULL AUTO_INCREMENT,
  `person_id` int DEFAULT NULL,
  `person_active_date` int DEFAULT NULL,
  `person_active_session` varchar(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  PRIMARY KEY (`person_active_id`),
  KEY `NewIndex1` (`person_id`),
  KEY `NewIndex2` (`person_active_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_actives`
--

LOCK TABLES `person_actives` WRITE;
/*!40000 ALTER TABLE `person_actives` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_actives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_alerts`
--

DROP TABLE IF EXISTS `person_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `person_alerts` (
  `person_alert_id` int NOT NULL AUTO_INCREMENT,
  `person_id` int NOT NULL,
  `person_alert_type` int NOT NULL,
  `person_alert_time` int NOT NULL,
  `person_alert_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`person_alert_id`),
  KEY `NewIndex1` (`person_alert_time`),
  KEY `NewIndex2` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_alerts`
--

LOCK TABLES `person_alerts` WRITE;
/*!40000 ALTER TABLE `person_alerts` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_alerts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_logs`
--

DROP TABLE IF EXISTS `person_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `person_logs` (
  `person_log_id` int NOT NULL AUTO_INCREMENT,
  `person_id` int NOT NULL,
  `person_log_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `person_log_ip` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `person_log_time` int NOT NULL,
  `person_log_module_code` int NOT NULL,
  `person_log_controller_code` int NOT NULL,
  `person_log_module` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `person_log_controller` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `person_log_action` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `person_log_entity` int DEFAULT NULL,
  `person_log_change` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `person_log_archive` int DEFAULT NULL,
  PRIMARY KEY (`person_log_id`),
  KEY `persons` (`person_id`),
  KEY `archive` (`person_log_archive`)
) ENGINE=InnoDB AUTO_INCREMENT=8258 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_logs`
--

LOCK TABLES `person_logs` WRITE;
/*!40000 ALTER TABLE `person_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_privates`
--

DROP TABLE IF EXISTS `person_privates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `person_privates` (
  `person_private_id` int NOT NULL AUTO_INCREMENT,
  `person_id` int NOT NULL,
  `person_private_banned` tinyint(1) DEFAULT '1',
  `person_private_hash` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `person_private_uid` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `person_private_login` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `project_id` int DEFAULT NULL,
  PRIMARY KEY (`person_private_id`),
  KEY `pp_person` (`person_id`),
  KEY `person_private_banned_key` (`person_private_banned`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_privates`
--

LOCK TABLES `person_privates` WRITE;
/*!40000 ALTER TABLE `person_privates` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_privates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_protecteds`
--

DROP TABLE IF EXISTS `person_protecteds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `person_protecteds` (
  `person_protected_id` int NOT NULL AUTO_INCREMENT,
  `person_id` int DEFAULT NULL,
  `person_protected_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`person_protected_id`),
  KEY `persons` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_protecteds`
--

LOCK TABLES `person_protecteds` WRITE;
/*!40000 ALTER TABLE `person_protecteds` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_protecteds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_roles`
--

DROP TABLE IF EXISTS `person_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `person_roles` (
  `person_role_id` int NOT NULL AUTO_INCREMENT,
  `person_id` int DEFAULT NULL,
  `role_id` int DEFAULT NULL,
  PRIMARY KEY (`person_role_id`),
  KEY `persons` (`person_id`),
  KEY `roles` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_roles`
--

LOCK TABLES `person_roles` WRITE;
/*!40000 ALTER TABLE `person_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_rules`
--

DROP TABLE IF EXISTS `person_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `person_rules` (
  `person_rule_id` int NOT NULL AUTO_INCREMENT,
  `person_id` int DEFAULT NULL,
  `rule_id` int DEFAULT NULL,
  `person_rule_action` int DEFAULT NULL,
  PRIMARY KEY (`person_rule_id`),
  KEY `persons` (`person_id`),
  KEY `rules` (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_rules`
--

LOCK TABLES `person_rules` WRITE;
/*!40000 ALTER TABLE `person_rules` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `persons`
--

DROP TABLE IF EXISTS `persons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `persons` (
  `person_id` int NOT NULL AUTO_INCREMENT,
  `person_first_name` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `person_second_name` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `person_patronymic` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `country_id` int DEFAULT NULL,
  `city_name` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `person_age` date DEFAULT NULL,
  `person_male` int DEFAULT NULL,
  `person_status` int DEFAULT '0',
  `project_id` int DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `parent` int DEFAULT NULL,
  `language_id` int DEFAULT NULL,
  `voice_id` int DEFAULT NULL,
  `person_notification` int DEFAULT NULL,
  `timezone_id` int DEFAULT NULL,
  PRIMARY KEY (`person_id`),
  KEY `NewIndex1` (`deleted_at`),
  KEY `person_id_key` (`person_id`),
  KEY `person_second_name_key` (`person_second_name`),
  KEY `city_id_key` (`city_name`),
  KEY `person_status_key` (`person_status`),
  KEY `person_male_key` (`person_male`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `persons`
--

LOCK TABLES `persons` WRITE;
/*!40000 ALTER TABLE `persons` DISABLE KEYS */;
/*!40000 ALTER TABLE `persons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_languages`
--

DROP TABLE IF EXISTS `project_languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_languages` (
  `project_language_id` int NOT NULL AUTO_INCREMENT,
  `project_id` int DEFAULT NULL,
  `language_id` int DEFAULT NULL,
  `project_language_order` int DEFAULT NULL,
  PRIMARY KEY (`project_language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_languages`
--

LOCK TABLES `project_languages` WRITE;
/*!40000 ALTER TABLE `project_languages` DISABLE KEYS */;
INSERT INTO `project_languages` VALUES (1,1,1,1),(2,1,2,2);
/*!40000 ALTER TABLE `project_languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `project_name` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `project_landing` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `timezone_id` int NOT NULL,
  `project_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id_key` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (1,1,'Start','slot-h.ru',347,'[\"googleMetric\": \"<script>window.dataLayer = window.dataLayer || [];function gtag() {dataLayer.push(arguments);}gtag(\'js\', new Date());gtag(\'config\', \'G-ST5HEMEWEB\');</script>\",\"yandexMetric\":\"<script type=\\\"text/javascript\\\">(function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};m[i].l=1*new Date();for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})(window, document,\\\'script\\\',\\\'https://mc.yandex.ru/metrika/tag.js?id=110332902\\\', \\\'ym\\\');ym(110332902, \\\'init\\\', {ssr:true, webvisor:true, clickmap:true, ecommerce:\"dataLayer\", referrer: document.referrer, url: location.href, accurateTrackBounce:true, trackLinks:true});</script><script type=\\\"application/ld+json\\\">\'. json_encode($this->schemaOrg, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) .\'</script><noscript><div><img src=\\\"https://mc.yandex.ru/watch/110332902\\\" style=\\\"position:absolute; left:-9999px;\\\" alt=\\\"\\\" /></div></noscript>\"]',NULL,NULL),(2,1,'Start (aliias)','slot-h.online',347,'[]',NULL,NULL);
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `queues`
--

DROP TABLE IF EXISTS `queues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `queues` (
  `queue_id` int NOT NULL AUTO_INCREMENT,
  `queue_status` int DEFAULT NULL,
  `queue_params` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `queue_action` int DEFAULT NULL,
  `queue_time` int DEFAULT NULL,
  `queue_first_start` int DEFAULT NULL,
  `queue_process_id` int DEFAULT NULL,
  PRIMARY KEY (`queue_id`),
  KEY `statuses` (`queue_status`),
  KEY `actions` (`queue_action`),
  KEY `times` (`queue_time`),
  KEY `start` (`queue_first_start`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `queues`
--

LOCK TABLES `queues` WRITE;
/*!40000 ALTER TABLE `queues` DISABLE KEYS */;
/*!40000 ALTER TABLE `queues` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_roles`
--

DROP TABLE IF EXISTS `role_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_roles` (
  `role_role_id` int NOT NULL AUTO_INCREMENT,
  `parent` int DEFAULT NULL,
  `role_id` int DEFAULT NULL,
  PRIMARY KEY (`role_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_roles`
--

LOCK TABLES `role_roles` WRITE;
/*!40000 ALTER TABLE `role_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_rules`
--

DROP TABLE IF EXISTS `role_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_rules` (
  `role_rule_id` int NOT NULL AUTO_INCREMENT,
  `role_id` int DEFAULT NULL,
  `rule_id` int DEFAULT NULL,
  PRIMARY KEY (`role_rule_id`),
  KEY `roles` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_rules`
--

LOCK TABLES `role_rules` WRITE;
/*!40000 ALTER TABLE `role_rules` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `parent` int DEFAULT NULL,
  `role_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `project_id` int DEFAULT NULL,
  `role_internal` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  KEY `parents` (`parent`),
  KEY `projects` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rules`
--

DROP TABLE IF EXISTS `rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rules` (
  `rule_id` int NOT NULL AUTO_INCREMENT,
  `rule_name` varchar(256) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `rule_group` int DEFAULT NULL,
  `rule_subgroup` int DEFAULT NULL,
  `project_id` int DEFAULT NULL,
  PRIMARY KEY (`rule_id`),
  KEY `groups` (`rule_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rules`
--

LOCK TABLES `rules` WRITE;
/*!40000 ALTER TABLE `rules` DISABLE KEYS */;
/*!40000 ALTER TABLE `rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `static_files`
--

DROP TABLE IF EXISTS `static_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `static_files` (
  `static_file_id` int NOT NULL AUTO_INCREMENT,
  `static_file_path` varchar(256) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `static_file_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `static_file_time` int DEFAULT NULL,
  PRIMARY KEY (`static_file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `static_files`
--

LOCK TABLES `static_files` WRITE;
/*!40000 ALTER TABLE `static_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `static_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_texts`
--

DROP TABLE IF EXISTS `t_texts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_texts` (
  `t_text_id` int NOT NULL AUTO_INCREMENT,
  `language_id` int NOT NULL,
  `parent` int NOT NULL,
  `text_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `text_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`t_text_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_texts`
--

LOCK TABLES `t_texts` WRITE;
/*!40000 ALTER TABLE `t_texts` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_texts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `texts`
--

DROP TABLE IF EXISTS `texts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `texts` (
  `text_id` int NOT NULL AUTO_INCREMENT,
  `text_tag` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `text_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `text_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `text_group` int DEFAULT NULL,
  `project_id` int DEFAULT NULL,
  `form_id` int DEFAULT NULL,
  `text_menu` int DEFAULT NULL,
  `text_menu_enabled` int DEFAULT NULL,
  `text_alternative` int DEFAULT NULL,
  `text_alternative_enabled` int DEFAULT NULL,
  PRIMARY KEY (`text_id`),
  KEY `text_tag_key` (`text_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `texts`
--

LOCK TABLES `texts` WRITE;
/*!40000 ALTER TABLE `texts` DISABLE KEYS */;
/*!40000 ALTER TABLE `texts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tkeys`
--

DROP TABLE IF EXISTS `tkeys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tkeys` (
  `tkey_id` int NOT NULL AUTO_INCREMENT,
  `tkey_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tkey_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`tkey_id`),
  KEY `tkey_hash_key` (`tkey_hash`)
) ENGINE=InnoDB AUTO_INCREMENT=288 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tkeys`
--

LOCK TABLES `tkeys` WRITE;
/*!40000 ALTER TABLE `tkeys` DISABLE KEYS */;
INSERT INTO `tkeys` VALUES (1,'Please wait...','25e8f2fd2871c8423bbe4e254066cd98'),(2,'О SLOT-H — история, философия, автор','76f759d37ac1c4e584f2e10d6be1c7b3'),(3,'SLOT-H — экосистема для прагматичной разработки мультипроектных систем. Создана на основе 25-летнего опыта, 15+ лет в продакшене. История, философия и принципы.','82bb25585d5bba35dfc6af9382c8b878'),(4,'о SLOT-H, история SLOT-H, автор SLOT-H, философия SLOT-H, принципы SLOT-H, 25 лет опыта','3d00816f6eace6a87859f0e2e7fa19aa'),(5,'Контакты SLOT-H','316bbf05856fb330df98dc521740bfdc'),(6,'Свяжитесь с автором и сообществом SLOT-H через доступные каналы. В социальных сетях и мессенджерах.','29358a9ff2855df5d236b4340755531b'),(7,'контакты SLOT-H','7c2b7ab26592393b701c0eb8af891b77'),(8,'Документация SLOT-H — архитектура, принципы, руководства','df0f1ced42de5555678132e8a811920d'),(9,'Полная документация SLOT-H: архитектура, принципы работы, ORM, модули, безопасность, локализация, очереди. Всё для быстрого старта и глубокого понимания системы.','6f9843098dbb4199d5f2663503146843'),(10,'документация SLOT-H, архитектура SLOT-H, принципы SLOT-H, ORM, мультипроектность, PHP экосистема, руководство SLOT-H','11c70e2a892a15af415ec1f3a9b66bd8'),(11,'Документация SLOT-H. Всё, что нужно для установки, настройки и создания собственных модулей.','e10d4e877c317f1d343788e4f4c62040'),(12,'Incorrect login or password','372269a0bab6fee444b0fad49b5c30e7'),(13,'The user is blocked','a7cbcbe29e233ce7d6ecfae91d77aaff'),(14,'The limit of authorization attempts has been exceeded. You can continue after %s seconds. I suggest you reset your password.','db6adfe2ff57489a481e9b05914a17a1'),(15,'Invalid credentials. You have %s left','11cffe8ddfa990bae23bcad3f5ef10a3'),(16,'Скоро запуск — SLOT-H','c79370040096570ec40a95a5483599ea'),(17,'SLOT-H готовится к запуску. Следите за обновлениями в социальных сетях и мессенджерах. Запуск запланирован на 14 августа 2026 года.','79ca974f5d0e3900e40f23d034b28ffa'),(18,'SLOT-H запуск, скоро запуск, таймер SLOT-H, 14 августа 2026','9ead87beffcc2772c87c18ccbb486d59'),(19,'SLOT-H скоро запустится. Следите за обновлениями — осталось совсем немного времени.','6552379250f0cb4634b22c41c4297033'),(20,'О проекте SLOT-H - история и философия','3bfbed24789ddf7572a03996a74f968b'),(21,'История создания SLOT-H, автор, философия прагматизма и почему фреймворки - это чёрный ящик.','cef720cc3ec7bef447d0fcbd379ca5d6'),(22,'История','63fb7e1ff002d54e99ebf2f1a3df5172'),(23,'О проекте SLOT-H','da541248c7f029ed77d90ed49301a3f1'),(24,'Один код. Множество проектов. Ноль границ.','df25edf9ed365b63775034bcab910514'),(25,'И 15 лет непрерывной эволюции.','f000d6ca19b607b18e06e469184823d1'),(26,'Как всё начиналось','c85808720dff0f01554d837b39388c24'),(27,'История создания','88196b275fa5c68430226bc067f54cc7'),(28,'Всё началось в %s2011 году%s. Автор, разработчик с 10-летним стажем на тот момент, столкнулся с проблемой, которая мучает многих: %s10 проектов - 10 разных кодовых баз%s.','f0a570da8945fec05ad2dd841c8becfe'),(29,'Каждый проект требовал отдельного обслуживания, обновлений, исправлений багов. Одно и то же действие нужно было делать 10 раз. 10 раз править один и тот же баг. 10 раз объяснять клиентам, почему обновление займёт время.','80913e4d0c2c96390bd8547d5d494ef3'),(30,'Проблема:','89c2a604f628417d8d6b9c74f2e19b8a'),(31,'Один баг → 10 проектов → 10 исправлений → бесконечность','7633cd1a6985516e50022e71630c8450'),(32,'Решение пришло не сразу. Первая версия SLOT-H (тогда он вообще никак не назывался) была простым набором скриптов для унификации. Но с каждым новым проектом система эволюционировала, обрастала новыми возможностями и становилась всё более целостной.','13fe7cb4eed1cf4c28ed53b74085bbd7'),(33,'К %s2015 году%s стало ясно: это не просто набор скриптов, это целая экосистема. К %s2020 году%s - боевой продакшен на десятках проектов. К %s2026 году%s - %s15 лет непрерывной эволюции%s.','b225f7a4940208dd3d8107bcc131a6ad'),(34,'Я не создавал SLOT-H для портфолио. Я создавал его, чтобы выжить. Чтобы не сойти с ума от поддержки 20 проектов. И чтобы спать спокойно по ночам.','89824a09c2b4340fe21077260b3f9e0e'),(35,'автор проекта, 25 лет в разработке','c9a3459d76b892b4420d020492dcde24'),(36,'Личная история','8f17d8e32a80bed4b930a18631ee4f88'),(37,'Как Zend Framework 1 изменил всё','4c66094a88e38b7c7419a3b36672c444'),(38,'И почему SLOT-H - это не фреймворк, а нечто совсем другое','9bd2090ed9d111b8e490d26ec0ff5f1d'),(39,'В то время я работал с %sZend Framework 1%s. Это было время, когда PHP-фреймворки только набирали обороты, и ZF1 казался вершиной инженерной мысли. Я впитывал его архитектуру, его паттерны, его подходы.','04c844b9cdfe84cf5f16e7b368a5ebf4'),(40,'Zend Framework 1 научил меня %sструктуре%s. Он показал, как можно организовать код, как разделять ответственность, как строить большие системы. Но со временем пришло и другое понимание.','d5ac58ae927ab4d145d0ff94d3dadcf0'),(41,'Мысль, которая изменила всё:','3bc517b1b5ce36c0374675938f4dc9e9'),(42,'Если Zend Framework 1 - это швейцарский нож, то мне нужна была %sпростая отвертка%s, которая всегда под рукой.','b570bebc31ab7672abfd674d9fb4ce63'),(43,'Идеология SLOT-H рождалась под влиянием ZF1, но шла %sсвоим путём%s. Я брал лучшее - чёткую структуру, разделение на слои, - но отбрасывал всё, что казалось %sизбыточным%s для моих задач.','e03252ab7f0ea8f8421f92c0a30ae6b2'),(44,'Постепенно система начала напоминать %sCodeIgniter%s в своей %sпрагматичности%s и простоте, но с %sсобственной архитектурой%s, которая выросла из реальных проектов, а не из теоретических построений.','a6120793d48ff8fd487088846ecbee71'),(45,'Вдохновение, структура, паттерны','d37fa8f94f624065c38c6839d25b8e6e'),(46,'Прагматичность, простота, \"работает из коробки\"','3b9229caf1c97d4c1a1728c8d62c41d8'),(47,'Собственная экосистема с уникальной архитектурой','a66cb7b6994712507f5bccc76179a823'),(48,'Суть:','e08f53da59a71f2dcc3c133442cbbc81'),(49,'SLOT-H не пытается быть лучше ZF или CodeIgniter, Laravel или Yii. Он %sдругой%s. Он впитал идеи этих инструментов, переосмыслил их и превратил в %sсобственную философию%s, которая работает уже %s15 лет%s.','716f3db3d5eb94e04b5c643d0bdf7cda'),(50,'Эволюция','a757b7ce7a01089bb99c0d7a33cfc0f4'),(51,'Таймлайн развития','6949a1e0589869cb0609cab6da288c41'),(52,'15 лет непрерывной эволюции - от скриптов до боевой экосистемы','df76e9fcc2813b2a6a5d217cb235fad7'),(53,'Первая версия','98ac17da079392ee4b89303eed5a683c'),(54,'Простой набор скриптов для унификации проектов. Ещё не SLOT-H, но идея уже родилась.','48c4cee57680024943fd92495f46780f'),(55,'Единая точка входа','0f4d5358d45fa0630476317818756227'),(56,'Появляется index.php как единый маршрутизатор. Рождение архитектуры.','8c5d3d310f0f7e5eb3c8eae1acf3baef'),(57,'Рождение модулей','46acc9faa69ecc91f4bb89fe2b97fc53'),(58,'Архитектура Modules/Base, /Main, /Free. Первое разделение на публичную часть, ядро и админку.','063dacc7d554f96b572305902fbdb3e7'),(59,'Первый продакшен','8fa0490ae489c3599119f4ee0f8b741b'),(60,'SLOT-H (тогда без имени) идёт в бой. 5 проектов на одной кодовой базе.','5b4eab317ff38cdb48d7c4c8a4e1eec6'),(61,'Осознание экосистемы','485ab095c2e1d093c651a57fea28c7aa'),(62,'Становится ясно: это не просто набор скриптов. Это целая экосистема для мультипроектной разработки.','424714c0a6312bffb6f17bc49db300dd'),(63,'Рефакторинг ORM','d3cfff797bf676d381b7f86002cd9d78'),(64,'Полный отказ от запросов в коде. Прозрачные JOIN, понятные WHERE. Без скрытой логики. И всё это - в едином объекте запроса.','6755839512990e4583f876acc2f6621f'),(65,'Расширение БД','fffb6e729f8af2e871abe16139023a4e'),(66,'Добавлена поддержка PostgreSQL и MongoDB. ORM становится гибче.','daf61d8549d6095138ae06e4714e572d'),(67,'Переход на PHP 8.x. Использование современных возможностей без потери совместимости.','d4489f365add11ba66ab2122686b2c54'),(68,'15 лет эволюции','9ba931708797d21eb8445b31b6477617'),(69,'Полный код - в личном репозитории автора. Публичная демо-версия - для знакомства с вырезанными реализациями под клиентов и с единственным даижком на MySQL.','2021527e63b73123e6207485007d7b52'),(70,'Сейчас мы здесь','91075928a4623e112f9f13abd182c428'),(71,'Автор','7c7d054bc5ae9c0d93b69431dfdf2264'),(72,'Кто за этим стоит','cd6ff7dde8ab56a1a7bd8495db3597fe'),(73,'лет в разработке','669ba04d4c6d249adb691710473084de'),(74,'лет эволюции SLOT-H','60b838f930366cc15035c5fcc1d66eb5'),(75,'проектов','c9b76e38026d62e6a11ff941e48ec0d4'),(76,'За проектом стоит %sодин человек%s. Начинал с Basic, Algol и C, потом перешёл на PHP (в то время 4 версии), когда это ещё не было мейнстримом.','d9468d4802b935ce3b5993933db7a3f9'),(77,'Создатель SLOT-H - %sпрагматик%s до мозга костей. Он не гонится за хайпом, не переписывает код каждые полгода под новый фреймворк. Он пишет код, который работает. Код, который кормит семью.','14c7643ed9478bfc46b01e4a58769c05'),(78,'За плечами - %sпроекты%s, от небольших сайтов до крупных корпоративных систем. И каждый проект - это урок, который был применён в SLOT-H.','f047b35e6fdf26f0dfe71e43aba4f5a7'),(79,'Философия автора:','4d8aa6d7c526d95be42f10a80ef8737b'),(80,'Я не пишу код для того, чтобы он был красивым. Я пишу код для того, чтобы он %sработал%s и чтобы его %sможно было поддерживать%s через 5 лет.','a6b8f2ea46a29e8a1193667dfca53841'),(81,'Ценности','bb40214a3844fa14bfc87a3ec65a0510'),(82,'На чём стоим','c91d8e2f4a1500492f4a544237f2699a'),(83,'Принципы, которые определяют развитие SLOT-H','16d226403dc9800512929321e25e6b52'),(84,'Прагматизм','625eea9e790df7b7870e1418a50ea880'),(85,'Работает - значит хорошо. Идеально - враг достаточно хорошего.','db8705cb096a9169d6b6cc900d30d721'),(86,'Идеализма','b2cede73d19dd557517e409d2d411018'),(87,'Реальный код','bc8a8564d21316139e7920525db31223'),(88,'Никаких учебных примеров. Только код, который прошёл через продакшен.','5a066f5c4f1cb0ce7932e0b532a5f9b2'),(89,'Учебных примеров','e1a1cfe793937501b6f2f1c22e3411b0'),(90,'Простота','8afc71c86e3d37d22bd56240c88db85e'),(91,'Сложность - враг понимания. Простой код легче поддерживать.','51828e2c457b6473db7b45ea9cb40cf2'),(92,'Сложности','2d494e060f9b94ae0f86ee69cc00cd3a'),(93,'Надёжность','b979c336c5740f491b2d7a35c24a3e46'),(94,'Баги недопустимы. Каждое изменение проверяется временем.','9f4b975ae08d9a24c3835d2fa8ff241d'),(95,'Скорости','72a6258a5b00d872b5d62a5e32f82863'),(96,'Долгосрочность','53e6fbf611effc01cc3c27c86dd7aadd'),(97,'Код пишется на годы вперёд. Никаких революций, только эволюция.','6c293b3244e5ca71ab6f382c7e2c8707'),(98,'Сиюминутных трендов','e194e9d87cadca2ae5168f3c64911fad'),(99,'Открытость','8b0163d57e3be920e4065fac2894784a'),(100,'Демо-код открыт. Диалог открыт. Конструктивная критика приветствуется.','7bf7a103153e06540b771dbedc7ab0a3'),(101,'Закрытости','ba18c9f2ea6703820020cf7c9e206fda'),(102,'Честно о коде','855fe9e5184b55b6a13e21de54aa921f'),(103,'Публичная демо-версия','3742040a4d90d154dde00b0edabfab5c'),(104,'Что вы найдёте в открытом репозитории, а что останется за кадром','f94b9f3a52f9bc5c287629500bc59bd8'),(105,'Публичный репозиторий','dbe86d7d5cebf71a4912b53d57407a79'),(106,'Демо-версия','5f0da6f179dbd41ccde44218206b9bfd'),(107,'Базовая архитектура и структура','5cb6a5add5c4cf05dca89457d3a0eca0'),(108,'Примеры контроллеров и моделей','8df9cf9bf4da3728c750d6e4175e37e9'),(109,'Демонстрация мультипроектности','f38417a1646b0acf8bb6e8d076e1dd1e'),(110,'Документация и примеры использования','fd63b44cb85d676e5fe4c4bbe073406a'),(111,'Базовый ORM с поддержкой MySQL','f18d1d8346722fd4d8879fc0cd51a58b'),(112,'Публичная часть','d30725621e653ee0f25ae52141e90f0e'),(113,'Этого достаточно, чтобы понять философию и архитектуру SLOT-H','3045ce867258225757c2bcc750c556c6'),(114,'Личный репозиторий автора','7467c250b2d702915b0449907600d5a6'),(115,'Полная версия','3ba03526fc7b60853dd17291fdb7e001'),(116,'Полный код с миграциями','a7097e3917718da3e4813e2c04923005'),(117,'Движки работы с кэшем (Memcached, Redis)','44a79af52efe3ddd0bfb30f372596162'),(118,'Полноценная работа с SQL и NoSQL','e0c73f84c9e63a0e2c8b7dc5bcda7fda'),(119,'Контроль пользователя по fingerprint устройства','18d13118e897d4e8b5849d14e29df3ea'),(120,'Расширенная система безопасности','4b466e0d35f83194d55fb60c184f8e2c'),(121,'Интеграция с внешними API и сервисами','f0cec70e89c6885a19907eb9b8584be8'),(122,'Продвинутая маршрутизация и валидация','146e45f4266179984dc5f25427b0a545'),(123,'CLI-инструменты для автоматизации','c08333feac5e11d061600b06a1cd604d'),(124,'%sВажно:%s Полный код остаётся в личном репозитории автора и не будет выложен в открытый доступ. Публичная демо-версия - это приглашение к диалогу, а не весь инструмент.','237a5fe47d996e8fa5a335257fced9a4'),(125,'Почему так:','5436cf86fc82a841bad43bfaa55003f8'),(126,'SLOT-H - это %sживой инструмент%s, который автор использует в работе каждый день. Полный код содержит решения, которые были выработаны за 15 лет и продолжают эволюционировать. Публичная демо-версия показывает %sархитектуру и философию%s, а полная версия - это %sбоевой инструмент%s.','b3a78383e78be38129430e67a5664e2c'),(127,'Сообщество','a8b894086dcccc3d9c56c48e2c1617bc'),(128,'Открытый диалог','ba3ef024b6a7c7ec1c4dc5da2e88b0f6'),(129,'SLOT-H - это не коммерческий продукт. Это %sоткрытый проект%s с открытой демо-версией и открытым диалогом.','8c058cc5237be69d94170d4096371eb0'),(130,'Автор открыт к %sдиалогу%s, конструктивной критике и улучшениям. Смотрите демо-версию, задавайте вопросы, предлагайте идеи.','acbee94ae9831490bf5801d179ca5a2d'),(131,'Полный код остаётся в личном репозитории, но %sидеи и философия%s - открыты для всех.','0563ff3aef52919bd041612f8eed69d6'),(132,'Я не идеален. Код не идеален. Но он работает. И мы можем сделать его лучше вместе. Начните с демо-версии - этого достаточно, чтобы понять суть.\"','07ea2991f55f13731bd09e3160e11a74'),(133,'автор проекта','4d3a2f30130d598bfeed11ebcd7fbb78'),(134,'Будущее','3a5a311c78ab63748bb9246355fbbdfe'),(135,'SLOT-H продолжает развиваться. Без революций - только эволюция. Каждое изменение проходит проверку временем и реальными проектами.','8ef6be31df47257e54ec4286c0bee34b'),(136,'Планы на развитие:','c641a0528ce486fa2cbe19a0d9ff52e6'),(137,'Расширение документации к демо-версии','a22416cf0636548bd0dccf0e2abeac4d'),(138,'Больше примеров использования','02a1e64eb0ad8269876fc6a47ed7ddc6'),(139,'Присоединяйтесь','0e14f1920e117d03af3824ac750a1133'),(140,'Демо-код открыт. Диалог открыт. Добро пожаловать.','e88591d021d77b0d81f8ec1c096224e5'),(141,'демо','fcac54111a34984caaed48b8f283785a'),(142,'Проект SLOT-H - контакты','27587a544b1379c46215c2451ade7547'),(143,'Контакты автора проекта и ссылки на документацию, блоги и ленты.','8342a97a112a000f58096f461fba7d03'),(144,'Контакты','75768c49c24662cc4465237b0731e1ce'),(145,'Свяжитесь со мной','b658a658b0e2461cf96d439da3603db2'),(146,'Я в мессенджерах и социальных сетях','08a381392287453107464d03bf579256'),(147,'Чат сообщества','4f2bf69807647b4c36e8f62d5a372676'),(148,'Канал и чат','817493318eae59212fb6d15d712f96db'),(149,'Канал','2710d4797143ef5a3368334ce709b20e'),(150,'Лента','eb71513eeda2eb0413e586f723c957a3'),(151,'Блог и профиль','1e4b9ba625aa0875e7fb036731e85d9c'),(152,'Введение + философия','c55c8d157306722416d5d6e9f7372a95'),(153,'Требования к серверу','e90193a486853367691623043893432c'),(154,'Быстрый старт','ef4a34c97005b9aa547cf83c04e5d257'),(155,'Философия экосистемы (Соглашение важнее правил)','304679f36365026f3a4cfa1b6654fc82'),(156,'Принципы работы (ядро vs модули)','29d413f973f54365136994d580b7a313'),(157,'Архитектура','86e525ce7da50f44038033ed0de23d6f'),(158,'Автолоадер (граф зависимостей)','4a6752063b1c6c8434f0231cd1fcbf4d'),(159,'Соглашение о хранилище данных','f05175ba1fa3016048da2bb9e578b9e6'),(160,'Параметризация','3e261f6d98ac46d639a043f47cf09371'),(161,'Мультипроектность','64c3ed96882767bc3ddfea4783d7f776'),(162,'Модуль Base','fb442382ee722881e541f7c3228bc880'),(163,'Модуль Free','fb45b81a5cf9025b17b2e6a858e1a7e2'),(164,'Модуль Geo (демо/полная)','cc2a35a58a0c000e6e4073b7bb6ff437'),(165,'Создание модуля (описание)','f9e852615b446ffc1c51b21686ff2426'),(166,'Диаграмма связей','4697001d376811fc15d41667ccaf25c9'),(167,'ORM - принципы','31c8ed7a93db3173b4a31c25cf9a8ace'),(168,'Методы работы','61567010fe7b33ca55acc6a5589022a5'),(169,'Сложные запросы','57719fa41f48b1519bd7b50f12f325aa'),(170,'Модели','41aaafa2cedfc3aa3dda7d2ba4371359'),(171,'JSON-данные','60d6d92eadea0a6aab71c7ce49e672ec'),(172,'Типы контроллеров','fa0ee99ed59e43b55b0bade0d6b0b911'),(173,'Структура фронтенд части (phtml файлы, css и js)','32210af793ac651103206d4299215366'),(174,'Выбор js-оболочки','5811611a51b837dc98c11efade7b8840'),(175,'Динамическая подпись','828d2fd05ae8b62654eb4a3f3993ee17'),(176,'Защита доступа','86818b7f927b8c67452310cdfb4dbe07'),(177,'Принцип локализации','ae52d6f9c1c9aee0f97e606fdd667b6e'),(178,'Запуск сервера (Демо)','ad89f020e59c534accc86f0cc25b6873'),(179,'Задачи сервера (dump - демо)','91d2fab44dbd0f42ff9ecbf24fa2ca8d'),(180,'Хелперы (Curl, Date, File, Geo, Person)','6140a6d8d5defdb7b69a484c62978802'),(181,'Демо vs Полная версия','a16b66ae48a55b61a2c4e35652fbfdd7'),(182,'Часто задаваемые вопросы','aac2f67819937093836897e7f274a9f8'),(183,'Вклад в проект - только баги и предложения','52dce6b34958f55b73474f382cdf53f2'),(184,'Лицензия','ef82960cf7f7b2f4d5ef775ef7a8cd77'),(185,'Ничего не найдено','8767f9ec282489d3e8e29021d0967187'),(186,'Предыдущий','786af9be98956c1bb0c8d51087ce0145'),(187,'Следующий','bf682ffe4d3b731e973161d60a680a0e'),(188,'Один код.','1c2c39714d77029f693a020189838a28'),(189,'Множество проектов.','ca003af843dd2c57dc2ed374579c1c16'),(190,'Ноль границ.','75021f7c5704c9cedbfaa1d362d44eb8'),(191,'SLOT-H - экосистема для прагматичных разработчиков','ce2ba37ff40ae19b16cc7329d696f333'),(192,'Пишите код один раз. SLOT-H разворачивает его на десятках сайтов с единой базой данных, единой логикой и единой точкой входа. Без микросервисов. Без контейнеризации. Без головной боли.','cbf6f6cab27c1203bd9f2e179b194dbc'),(193,'Смотреть на GitHub','4b5c5f74910492770af466f200bb3288'),(194,'Документация','3d300329be2a963d8f95b23c95c406c9'),(195,'лет в продакшене','88460359d74c8cbeec296ea478820f4c'),(196,'лет опыта автора','73586890939fd56a5081fd6adf93c7b7'),(197,'О проекте','47dc068e89b16d23b808d1afb495de78'),(198,'Что такое SLOT-H?','a63ebfe6a800daf133c1842db3283aa1'),(199,'SLOT-H (Single Logical Operating Tool for Hosting) - это не фреймворк. Это боевая экосистема, которая родилась из 25-летнего опыта разработки и 15+ лет продакшена.','e29690a6b8bbfb63171fc68569bc1f85'),(200,'Одна цель','df8eeaf8d4ca8d112a7c45cfb755b930'),(201,'Позволить одному коду работать на множестве проектов одновременно. Вы пишете код один раз - SLOT-H разворачивает его на десятках сайтов.','05b6132a26fe29b21edded63e678886a'),(202,'Единая база данных, единая логика, единая точка входа. Без микросервисов. Без контейнеризации. Без головной боли.','0afb41c54a413135233910e45759e194'),(203,'Прагматичность','c9b393fbc49292ac785aa822a39d0c55'),(204,'Код, который работает. Разработчик, который не думает о ядре - только о коде в контроллере. 15+ лет движения в этом направлении.','eea71aa65b588dc3ec90dc389e5d2ad8'),(205,'Возможности','6eabbd712a55ddf60ce30b63c737fd66'),(206,'Ключевые возможности','37f36aa6bc42edaa3c9d1638acf69838'),(207,'Всё, что нужно для прагматичной разработки мультипроектных систем','362c40c21d830404887b76b4a05a2e56'),(208,'Модульность','c2d22e19e39df793fb87c01420b4ae11'),(209,'Контроллеры, модели, представления. Preloader, Crud, наследование. Чёткая структура без лишней магии.','26492286e8387d2028f4eb1a0a9d5961'),(210,'Один код - десятки проектов. project_id - ваш главный инструмент. Автоматическая изоляция данных на уровне запросов.','442cdea367d488f7eb1cbaf571833cf4'),(211,'Локализация','66423e3f7454dd1eb10060df6e552426'),(212,'Перевод любого сайта \"из коробки\". Ядро, знающее какой язык выдать пользователю. Готово для многоязычных проектов.','c65f2ff578811f0e06403d93b696d3ed'),(213,'ORM без магии','d3639aa7555bb3999b031b035d986019'),(214,'Mysqli (& Maria), SQLite, PDO, Postgres, MsSQL, MongoDB. Сложные JOIN, умные WHERE. Ни единого прямого запроса в коде. Автоматическая оптимизация запросов.','f1ffd082317ecf3fb1b17d5749d202e6'),(215,'Безопасность','3677ee79e51454e8da26eb578c6c4e5c'),(216,'Динамическая подпись форм (меняется максимум раз 30 секунд). Защита от CSRF и повторных отправок.','14248746a34908da0e23140f3948ef30'),(217,'Готов к работе','8d2291b8c6af8c2a6f81bbd9e4a6022e'),(218,'Apache/Nginx, PHP 7.x-8.x, MySQL 8. Одна точка входа. Минимальные требования к окружению и серверу.','09c1e50695ee568392061ff9db989468'),(219,'Как это работает','164ea246fd0c4f35fd24ba107d66f112'),(220,'Простая и понятная архитектура без лишних слоёв','337201d0914a551ae754345e14958164'),(221,'единая точка входа','95ff1d225ae8b1fb595d15d06ac3961a'),(222,'маршрутизация','d8c0420b30f1e2df8446352143ce5755'),(223,'публичная часть','5668999a9d20e6ba7651dd35047402f0'),(224,'ядро системы','2fcaee2eda189a24eccbb9d9d2e68e65'),(225,'админка','3fdda9bf3d7d5a4fd9f09f1b75fcbda0'),(226,'гео-базы','040c891bbf75ec5354cea551fa8b6be1'),(227,'ORM + хранилище','f07f5c6323453195407a45d071ad782f'),(228,'базы данных','5be6892da79aee25adab8dfe5b39e6fe'),(229,'Аудитория','b14e2a6b261bbe46ef0cef0d4516872c'),(230,'Для кого SLOT-H?','ba75fa20d034443fa2625e9168b2e3e2'),(231,'Каждый найдёт в нём что-то полезное','a4c880075400b0bab67b54bc097bee57'),(232,'Архитекторы','073a989a2a5a2a2a164016c90b9cfde5'),(233,'Увидите, как можно строить мультипроектные системы без микросервисов. Просто и эффективно.','206750e92d6d8727d897ab0591be5dff'),(234,'Разработчики с опытом','f739e621d0f1e0951bea444821ea33ca'),(235,'Найдёте прагматичные решения, которые работают годами. Без хайпа, с реальным кодом.','1af2be391c6c7bddebff7c9423337326'),(236,'Владельцы проектов','19c3b0791509ea7d24b2208eb9c23fd3'),(237,'Поймёте, как сократить затраты на поддержку множества сайтов. Экономия ресурсов и времени.','785aaec889c6668a4e6e7eb4394a912c'),(238,'Студенты и начинающие','3e74c8f2265db0e72cb33c89811ab897'),(239,'Увидите реальный код, а не учебные примеры. Настоящий продакшен с 15-летней историей.','f793273367d5bce49fd3398874a9dcb1'),(240,'Сравнение','20857d9b936304d91f83e039504279fb'),(241,'Почему SLOT-H, а не Laravel / Symfony / тому подобное?','5ea98b42efeba1f958ff3b1de1a40ad0'),(242,'<strong>Laravel</strong> - это фреймворк. <strong>SLOT-H</strong> - это экосистема.','6c5b367c2758c44a92170b811ffd3737'),(243,'<strong>Laravel</strong> учит \"правильно\". <strong>SLOT-H</strong> учит \"прагматично\".','74a985acbbb79ccbcf0160bf5a99a5ba'),(244,'<strong>Laravel</strong> требует подстройки. <strong>SLOT-H</strong> работает как есть.','c68eac2e659129d2e5eaa7ffa748a2f7'),(245,'Я не говорю, что SLOT-H лучше. Я говорю: он <strong>ДРУГОЙ</strong>. Он создан для моих задач. Возможно, и для ваших тоже.','25817ba23f02ec9d298f59034edf5444'),(246,'автор проекта, 25 лет опыта','2c1dbecb385fd7663f5f1a796790d446'),(247,'Структура','2411c441c300cf0fd5c25f856acf73c8'),(248,'Что внутри','c294be76cb3cfece2955b7a778ac0e55'),(249,'Организация кода - чистая и понятная','5bad3c1df762f0b42fc81a329f854054'),(250,'Ядро системы','bf5ff58bda0f2d759b4f8fa127ab4a35'),(251,'Базовые хелперы','cf36cd68732d0ce3200c2523c256c2ff'),(252,'Шаблоны проектов','fdddff50e110bc155d86a9c51b32c3c5'),(253,'Базовая статика (css, js, fonts)','4e4dc62011caff3ae974d246f0be3538'),(254,'Утилиты (автолоадер, CLI)','3464e07d4677040a31baa3533d1578f3'),(255,'Конфигурация','aa5d5de024044914cd74f4b0c9c3e067'),(256,'Глобальные настройки','a03bb3904275c3d913641d7ac878b242'),(257,'Конфиги проектов (*.ini)','ef5c63b41fd9c70881e638310cd95b63'),(258,'Логи по проектам','f97c86e827f1b3c89b231c6cb0af6029'),(259,'Модули (Base, Main, Free, Geo)','a20492c25dd00904ab3555fdf02997cf'),(260,'Файловое хранилище','bb46aef56d767829f5492ebd4757a314'),(261,'Точка входа','5405a413ef240fe8b0c368b7db43e7aa'),(262,'URL-роутинг','36fa0a3672a9d482da7e6afc7b253e0c'),(263,'MIT - свободно, для любых целей','c5cd5fc705ca4931487d48e86547eaf1'),(264,'Статус','7203f7a4ff564cb876e8db54c903dbfc'),(265,'Боевой, работает в продакшене','83f7df0c9212c7404f52168cc59a4ce7'),(266,'Возраст','00870b4106645ad3ade4f09812f60516'),(267,'15+ лет непрерывной эволюции','0faf7b260fc7888f8d389191527e5c6d'),(268,'25 лет опыта в одном репозитории','157009682966d6d334f1cafda72a0969'),(269,'<strong>Важно:</strong> SLOT-H - это НЕ учебный проект. Это код, который кормит семью автора. Он не идеален. Он прагматичен.','694642e4638d203d27a7ddd1c2c37ec1'),(270,'Готовы увидеть, как работает SLOT-H?','92aff544b1ebc04555158ecb9d64540e'),(271,'Если SLOT-H помог вам - поставьте звезду','9e1df7cc9953ad75c614ed1fb934f41b'),(272,'Если нашли баг - создайте Issue','4298274fcaea2ebae4fe715b8e506c15'),(273,'Если хотите улучшить - сделайте Pull Request','58bc94126a71b8b71fdb7e58ebcaefbe'),(274,'Конструктивная критика приветствуется. Тролли - игнорируются.','415f7deb258982629273931920d6f223'),(275,'Идёт подготовка к запуску','f31706e8c28df288692cfe0485421d8a'),(276,'Скоро здесь будет SLOT-H','404b850b95efdbdc802ba9032280226d'),(277,'Собираю последние кирпичики для GitHub.','c7dd771f1c5f23a569040c8884b7ba73'),(278,'До запуска осталось:','e4343c424adc9cb5157918d4440db219'),(279,'Дней','ad30713c64da47b80a765ccd2192016e'),(280,'Часов','8e98137ff5d73e087bc2c3528edcf6c6'),(281,'Минут','c765568c24020c28f5ae3ee21f0fe838'),(282,'Секунд','cf9bd70f8f3579e26e41eba5cb9cb354'),(283,'Запланированный запуск:','ffc0f0c0174c2ce5e6f6547b54c64a12'),(284,'августа','e3aa9825ef059fd1059614890b71ed81'),(285,'Поддержка','662448730e42616d253de473ba48fc61'),(286,'Разработано с прагматизмом в течение 15+ лет.','11d4ade4a4d73f09efc4f99964af77e6'),(287,'Главная','047f5653b183292396e67f14c8750b73');
/*!40000 ALTER TABLE `tkeys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tvalues`
--

DROP TABLE IF EXISTS `tvalues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tvalues` (
  `tvalue_id` int NOT NULL AUTO_INCREMENT,
  `tkey_id` int NOT NULL,
  `language_id` int NOT NULL,
  `tvalue_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `project_id` int DEFAULT NULL,
  PRIMARY KEY (`tvalue_id`),
  KEY `tkey_id_key` (`tkey_id`),
  KEY `language_id_key` (`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=288 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tvalues`
--

LOCK TABLES `tvalues` WRITE;
/*!40000 ALTER TABLE `tvalues` DISABLE KEYS */;
INSERT INTO `tvalues` VALUES (1,1,2,'Please wait...',1),(2,2,2,'About SLOT-H — history, philosophy, author',1),(3,3,2,'SLOT-H is an ecosystem for pragmatic development of multi-project systems. Built on 25 years of experience, 15+ years in production. History, philosophy and principles.',1),(4,4,2,'about SLOT-H, SLOT-H history, SLOT-H author, SLOT-H philosophy, SLOT-H principles, 25 years of experience',1),(5,5,2,'SLOT-H Contacts',1),(6,6,2,'Contact the SLOT-H author and community through available channels. On social networks and messengers.',1),(7,7,2,'SLOT-H contacts',1),(8,8,2,'SLOT-H Documentation — architecture, principles, guides',1),(9,9,2,'Complete SLOT-H documentation: architecture, operating principles, ORM, modules, security, localization, queues. Everything for a quick start and deep understanding of the system.',1),(10,10,2,'SLOT-H documentation, SLOT-H architecture, SLOT-H principles, ORM, multi-project, PHP ecosystem, SLOT-H guide',1),(11,11,2,'SLOT-H Documentation. Everything you need for installation, configuration and creating your own modules.',1),(12,12,2,'Incorrect login or password',1),(13,13,2,'The user is blocked',1),(14,14,2,'The limit of authorization attempts has been exceeded. You can continue after %s seconds. I suggest you reset your password.',1),(15,15,2,'Invalid credentials. You have %s left',1),(16,16,2,'Launching soon — SLOT-H',1),(17,17,2,'SLOT-H is preparing for launch. Follow updates on social networks and messengers. Launch is scheduled for August 14, 2026.',1),(18,18,2,'SLOT-H launch, launching soon, SLOT-H timer, August 14 2026',1),(19,19,2,'SLOT-H will launch soon. Follow updates — very little time left.',1),(20,20,2,'About SLOT-H - history and philosophy',1),(21,21,2,'History of SLOT-H creation, author, philosophy of pragmatism and why frameworks are a black box.',1),(22,22,2,'History',1),(23,23,2,'About SLOT-H',1),(24,24,2,'One code. Many projects. Zero boundaries.',1),(25,25,2,'And 15 years of continuous evolution.',1),(26,26,2,'How it all began',1),(27,27,2,'Creation history',1),(28,28,2,'It all started in %s2011%s. The author, a developer with 10 years of experience at that time, faced a problem that torments many: %s10 projects - 10 different codebases%s.',1),(29,29,2,'Each project required separate maintenance, updates, bug fixes. The same action had to be done 10 times. Fix the same bug 10 times. Explain to clients 10 times why an update takes time.',1),(30,30,2,'The problem:',1),(31,31,2,'One bug → 10 projects → 10 fixes → infinity',1),(32,32,2,'The solution did not come immediately. The first version of SLOT-H (then it had no name at all) was a simple set of scripts for unification. But with each new project the system evolved, grew new capabilities and became more and more holistic.',1),(33,33,2,'By %s2015%s it became clear: this is not just a set of scripts, it is a whole ecosystem. By %s2020%s - battle-tested production on dozens of projects. By %s2026%s - %s15 years of continuous evolution%s.',1),(34,34,2,'I did not create SLOT-H for a portfolio. I created it to survive. To not go crazy from supporting 20 projects. And to sleep peacefully at night.',1),(35,35,2,'project author, 25 years in development',1),(36,36,2,'Personal story',1),(37,37,2,'How Zend Framework 1 changed everything',1),(38,38,2,'And why SLOT-H is not a framework, but something completely different',1),(39,39,2,'At that time I was working with %sZend Framework 1%s. It was a time when PHP frameworks were just gaining momentum, and ZF1 seemed the pinnacle of engineering thought. I absorbed its architecture, its patterns, its approaches.',1),(40,40,2,'Zend Framework 1 taught me %sstructure%s. It showed how to organize code, how to separate responsibilities, how to build large systems. But over time another understanding came.',1),(41,41,2,'The thought that changed everything:',1),(42,42,2,'If Zend Framework 1 is a Swiss army knife, then I needed a %ssimple screwdriver%s that is always at hand.',1),(43,43,2,'The ideology of SLOT-H was born under the influence of ZF1, but went %sits own way%s. I took the best - clear structure, separation into layers - but discarded everything that seemed %sexcessive%s for my tasks.',1),(44,44,2,'Gradually the system began to resemble %sCodeIgniter%s in its %spragmatism%s and simplicity, but with %sits own architecture%s, which grew from real projects, not from theoretical constructs.',1),(45,45,2,'Inspiration, structure, patterns',1),(46,46,2,'Pragmatism, simplicity, \"works out of the box\"',1),(47,47,2,'Own ecosystem with unique architecture',1),(48,48,2,'The essence:',1),(49,49,2,'SLOT-H does not try to be better than ZF or CodeIgniter, Laravel or Yii. It is %sdifferent%s. It absorbed the ideas of these tools, rethought them and turned them into %sits own philosophy%s that has been working for %s15 years%s.',1),(50,50,2,'Evolution',1),(51,51,2,'Development timeline',1),(52,52,2,'15 years of continuous evolution - from scripts to battle-tested ecosystem',1),(53,53,2,'First version',1),(54,54,2,'A simple set of scripts for unifying projects. Not yet SLOT-H, but the idea was already born.',1),(55,55,2,'Single entry point',1),(56,56,2,'index.php appears as a single router. Birth of the architecture.',1),(57,57,2,'Birth of modules',1),(58,58,2,'Architecture Modules/Base, /Main, /Free. First separation into public part, core and admin panel.',1),(59,59,2,'First production',1),(60,60,2,'SLOT-H (then without a name) goes into battle. 5 projects on one codebase.',1),(61,61,2,'Awareness of the ecosystem',1),(62,62,2,'It becomes clear: this is not just a set of scripts. It is a whole ecosystem for multi-project development.',1),(63,63,2,'ORM refactoring',1),(64,64,2,'Complete rejection of queries in code. Transparent JOIN, clear WHERE. No hidden logic. And all this - in a single query object.',1),(65,65,2,'Database expansion',1),(66,66,2,'Added support for PostgreSQL and MongoDB. ORM becomes more flexible.',1),(67,67,2,'Transition to PHP 8.x. Using modern capabilities without losing compatibility.',1),(68,68,2,'15 years of evolution',1),(69,69,2,'Full code is in the author\'s personal repository. Public demo version is for getting acquainted with implementations cut out for clients and with a single engine on MySQL.',1),(70,70,2,'Now we are here',1),(71,71,2,'Author',1),(72,72,2,'Who is behind this',1),(73,73,2,'years in development',1),(74,74,2,'years of SLOT-H evolution',1),(75,75,2,'projects',1),(76,76,2,'Behind the project is %sone person%s. Started with Basic, Algol and C, then moved to PHP (version 4 at that time), when it was not yet mainstream.',1),(77,77,2,'The creator of SLOT-H is a %spragmatist%s to the bone. He does not chase hype, does not rewrite code every six months for a new framework. He writes code that works. Code that feeds a family.',1),(78,78,2,'Behind him are %sprojects%s, from small websites to large corporate systems. And each project is a lesson that was applied in SLOT-H.',1),(79,79,2,'Author\'s philosophy:',1),(80,80,2,'I do not write code to make it beautiful. I write code to make it %swork%s and to make it %smaintainable%s in 5 years.',1),(81,81,2,'Values',1),(82,82,2,'What we stand on',1),(83,83,2,'Principles that define the development of SLOT-H',1),(84,84,2,'Pragmatism',1),(85,85,2,'Works - means good. Perfect is the enemy of good enough.',1),(86,86,2,'Idealism',1),(87,87,2,'Real code',1),(88,88,2,'No educational examples. Only code that has gone through production.',1),(89,89,2,'Educational examples',1),(90,90,2,'Simplicity',1),(91,91,2,'Complexity is the enemy of understanding. Simple code is easier to maintain.',1),(92,92,2,'Complexity',1),(93,93,2,'Reliability',1),(94,94,2,'Bugs are unacceptable. Every change is tested by time.',1),(95,95,2,'Speed',1),(96,96,2,'Long-term',1),(97,97,2,'Code is written for years ahead. No revolutions, only evolution.',1),(98,98,2,'Momentary trends',1),(99,99,2,'Openness',1),(100,100,2,'Demo code is open. Dialogue is open. Constructive criticism is welcome.',1),(101,101,2,'Closedness',1),(102,102,2,'Honestly about the code',1),(103,103,2,'Public demo version',1),(104,104,2,'What you will find in the open repository and what will remain behind the scenes',1),(105,105,2,'Public repository',1),(106,106,2,'Demo version',1),(107,107,2,'Basic architecture and structure',1),(108,108,2,'Examples of controllers and models',1),(109,109,2,'Demonstration of multi-project capability',1),(110,110,2,'Documentation and usage examples',1),(111,111,2,'Basic ORM with MySQL support',1),(112,112,2,'Public part',1),(113,113,2,'This is enough to understand the philosophy and architecture of SLOT-H',1),(114,114,2,'Author\'s personal repository',1),(115,115,2,'Full version',1),(116,116,2,'Full code with migrations',1),(117,117,2,'Cache engines (Memcached, Redis)',1),(118,118,2,'Full-featured work with SQL and NoSQL',1),(119,119,2,'User control by device fingerprint',1),(120,120,2,'Extended security system',1),(121,121,2,'Integration with external APIs and services',1),(122,122,2,'Advanced routing and validation',1),(123,123,2,'CLI tools for automation',1),(124,124,2,'%sImportant:%s The full code remains in the author\'s personal repository and will not be published in open access. The public demo version is an invitation to dialogue, not the whole tool.',1),(125,125,2,'Why this way:',1),(126,126,2,'SLOT-H is a %sliving tool%s that the author uses in work every day. The full code contains solutions that were developed over 15 years and continue to evolve. The public demo version shows %sarchitecture and philosophy%s, while the full version is a %sbattle tool%s.',1),(127,127,2,'Community',1),(128,128,2,'Open dialogue',1),(129,129,2,'SLOT-H is not a commercial product. It is an %sopen project%s with an open demo version and open dialogue.',1),(130,130,2,'The author is open to %sdialogue%s, constructive criticism and improvements. Look at the demo version, ask questions, suggest ideas.',1),(131,131,2,'The full code remains in the personal repository, but %sideas and philosophy%s are open to everyone.',1),(132,132,2,'I am not perfect. The code is not perfect. But it works. And we can make it better together. Start with the demo version - it is enough to understand the essence.',1),(133,133,2,'project author',1),(134,134,2,'Future',1),(135,135,2,'SLOT-H continues to develop. No revolutions - only evolution. Every change is tested by time and real projects.',1),(136,136,2,'Development plans:',1),(137,137,2,'Expanding documentation for the demo version',1),(138,138,2,'More usage examples',1),(139,139,2,'Join',1),(140,140,2,'Demo code is open. Dialogue is open. Welcome.',1),(141,141,2,'demo',1),(142,142,2,'Project SLOT-H - contacts',1),(143,143,2,'Contacts of the project author and links to documentation, blogs and feeds.',1),(144,144,2,'Contacts',1),(145,145,2,'Contact me',1),(146,146,2,'I am in messengers and social networks',1),(147,147,2,'Community chat',1),(148,148,2,'Channel and chat',1),(149,149,2,'Channel',1),(150,150,2,'Feed',1),(151,151,2,'Blog and profile',1),(152,152,2,'Introduction + philosophy',1),(153,153,2,'Server requirements',1),(154,154,2,'Quick start',1),(155,155,2,'Ecosystem philosophy (Agreement is more important than rules)',1),(156,156,2,'Operating principles (core vs modules)',1),(157,157,2,'Architecture',1),(158,158,2,'Autoloader (dependency graph)',1),(159,159,2,'Data storage agreement',1),(160,160,2,'Parameterization',1),(161,161,2,'Multi-project',1),(162,162,2,'Module Base',1),(163,163,2,'Module Free',1),(164,164,2,'Module Geo (demo/full)',1),(165,165,2,'Creating a module (description)',1),(166,166,2,'Relationship diagram',1),(167,167,2,'ORM - principles',1),(168,168,2,'Working methods',1),(169,169,2,'Complex queries',1),(170,170,2,'Models',1),(171,171,2,'JSON data',1),(172,172,2,'Controller types',1),(173,173,2,'Frontend structure (phtml files, css and js)',1),(174,174,2,'Choosing js shell',1),(175,175,2,'Dynamic signature',1),(176,176,2,'Access protection',1),(177,177,2,'Localization principle',1),(178,178,2,'Starting the server (Demo)',1),(179,179,2,'Server tasks (dump - demo)',1),(180,180,2,'Helpers (Curl, Date, File, Geo, Person)',1),(181,181,2,'Demo vs Full version',1),(182,182,2,'Frequently asked questions',1),(183,183,2,'Contributing to the project - only bugs and suggestions',1),(184,184,2,'License',1),(185,185,2,'Nothing found',1),(186,186,2,'Previous',1),(187,187,2,'Next',1),(188,188,2,'One code.',1),(189,189,2,'Many projects.',1),(190,190,2,'Zero boundaries.',1),(191,191,2,'SLOT-H - ecosystem for pragmatic developers',1),(192,192,2,'Write code once. SLOT-H deploys it on dozens of sites with a single database, single logic and single entry point. No microservices. No containerization. No headache.',1),(193,193,2,'View on GitHub',1),(194,194,2,'Documentation',1),(195,195,2,'years in production',1),(196,196,2,'years of author\'s experience',1),(197,197,2,'About the project',1),(198,198,2,'What is SLOT-H?',1),(199,199,2,'SLOT-H (Single Logical Operating Tool for Hosting) is not a framework. It is a battle-tested ecosystem born from 25 years of development experience and 15+ years of production.',1),(200,200,2,'One goal',1),(201,201,2,'To allow one code to work on many projects simultaneously. You write code once - SLOT-H deploys it on dozens of sites.',1),(202,202,2,'Single database, single logic, single entry point. No microservices. No containerization. No headache.',1),(203,203,2,'Pragmatism',1),(204,204,2,'Code that works. A developer who does not think about the core - only about the code in the controller. 15+ years of movement in this direction.',1),(205,205,2,'Capabilities',1),(206,206,2,'Key capabilities',1),(207,207,2,'Everything needed for pragmatic development of multi-project systems',1),(208,208,2,'Modularity',1),(209,209,2,'Controllers, models, views. Preloader, Crud, inheritance. Clear structure without unnecessary magic.',1),(210,210,2,'One code - dozens of projects. project_id is your main tool. Automatic data isolation at the query level.',1),(211,211,2,'Localization',1),(212,212,2,'Translation of any site \"out of the box\". A core that knows which language to give the user. Ready for multilingual projects.',1),(213,213,2,'ORM without magic',1),(214,214,2,'Mysqli (& Maria), SQLite, PDO, Postgres, MsSQL, MongoDB. Complex JOIN, smart WHERE. Not a single direct query in code. Automatic query optimization.',1),(215,215,2,'Security',1),(216,216,2,'Dynamic form signature (changes at most once every 30 seconds). Protection against CSRF and repeated submissions.',1),(217,217,2,'Ready to work',1),(218,218,2,'Apache/Nginx, PHP 7.x-8.x, MySQL 8. Single entry point. Minimal requirements for environment and server.',1),(219,219,2,'How it works',1),(220,220,2,'Simple and clear architecture without unnecessary layers',1),(221,221,2,'single entry point',1),(222,222,2,'routing',1),(223,223,2,'public part',1),(224,224,2,'system core',1),(225,225,2,'admin panel',1),(226,226,2,'geo databases',1),(227,227,2,'ORM + storage',1),(228,228,2,'databases',1),(229,229,2,'Audience',1),(230,230,2,'Who is SLOT-H for?',1),(231,231,2,'Everyone will find something useful in it',1),(232,232,2,'Architects',1),(233,233,2,'You will see how to build multi-project systems without microservices. Simply and efficiently.',1),(234,234,2,'Experienced developers',1),(235,235,2,'You will find pragmatic solutions that work for years. No hype, with real code.',1),(236,236,2,'Project owners',1),(237,237,2,'You will understand how to reduce maintenance costs for multiple sites. Saving resources and time.',1),(238,238,2,'Students and beginners',1),(239,239,2,'You will see real code, not educational examples. Real production with 15 years of history.',1),(240,240,2,'Comparison',1),(241,241,2,'Why SLOT-H and not Laravel / Symfony / the like?',1),(242,242,2,'<strong>Laravel</strong> is a framework. <strong>SLOT-H</strong> is an ecosystem.',1),(243,243,2,'<strong>Laravel</strong> teaches \"correctly\". <strong>SLOT-H</strong> teaches \"pragmatically\".',1),(244,244,2,'<strong>Laravel</strong> requires adjustment. <strong>SLOT-H</strong> works as is.',1),(245,245,2,'I am not saying SLOT-H is better. I am saying: it is <strong>DIFFERENT</strong>. It was created for my tasks. Perhaps for yours too.',1),(246,246,2,'project author, 25 years of experience',1),(247,247,2,'Structure',1),(248,248,2,'What is inside',1),(249,249,2,'Code organization - clean and clear',1),(250,250,2,'System core',1),(251,251,2,'Basic helpers',1),(252,252,2,'Project templates',1),(253,253,2,'Basic static (css, js, fonts)',1),(254,254,2,'Utilities (autoloader, CLI)',1),(255,255,2,'Configuration',1),(256,256,2,'Global settings',1),(257,257,2,'Project configs (*.ini)',1),(258,258,2,'Logs by project',1),(259,259,2,'Modules (Base, Main, Free, Geo)',1),(260,260,2,'File storage',1),(261,261,2,'Entry point',1),(262,262,2,'URL routing',1),(263,263,2,'MIT - free, for any purpose',1),(264,264,2,'Status',1),(265,265,2,'Battle-tested, works in production',1),(266,266,2,'Age',1),(267,267,2,'15+ years of continuous evolution',1),(268,268,2,'25 years of experience in one repository',1),(269,269,2,'<strong>Important:</strong> SLOT-H is NOT an educational project. This is code that feeds the author\'s family. It is not perfect. It is pragmatic.',1),(270,270,2,'Ready to see how SLOT-H works?',1),(271,271,2,'If SLOT-H helped you - give it a star',1),(272,272,2,'If you found a bug - create an Issue',1),(273,273,2,'If you want to improve - make a Pull Request',1),(274,274,2,'Constructive criticism is welcome. Trolls are ignored.',1),(275,275,2,'Preparation for launch is underway',1),(276,276,2,'SLOT-H will be here soon',1),(277,277,2,'I am putting together the last bricks for GitHub.',1),(278,278,2,'Time left until launch:',1),(279,279,2,'Days',1),(280,280,2,'Hours',1),(281,281,2,'Minutes',1),(282,282,2,'Seconds',1),(283,283,2,'Scheduled launch:',1),(284,284,2,'August',1),(285,285,2,'Support',1),(286,286,2,'Developed with pragmatism over 15+ years.',1),(287,287,2,'Home',1);
/*!40000 ALTER TABLE `tvalues` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'start'
--

--
-- Dumping routines for database 'start'
--

--
-- Current Database: `geo_start`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `geo_start` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `geo_start`;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `languages` (
  `language_id` int NOT NULL AUTO_INCREMENT,
  `language_name` varchar(128) NOT NULL,
  `language_iso_1` varchar(2) DEFAULT NULL,
  `language_iso_3` varchar(7) DEFAULT NULL,
  `language_status` tinyint(1) DEFAULT NULL,
  `language_privacy_url` varchar(255) DEFAULT NULL,
  `language_license_url` varchar(255) DEFAULT NULL,
  `language_flag` varchar(3) DEFAULT NULL,
  `language_direction` tinyint(1) DEFAULT NULL,
  `language_voice_male` int DEFAULT NULL,
  `language_voice_female` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `language_s` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (1,'Russian','ru','rus',1,NULL,NULL,'ru',NULL,53,52,'2023-03-06 16:03:50',NULL,NULL),(2,'English','en','eng',1,NULL,NULL,'gb',NULL,1,0,'2019-09-17 11:36:36',NULL,NULL),(3,'French','fr','fra/fre',1,NULL,NULL,'fr',NULL,28,27,'2023-04-04 11:55:27',NULL,NULL),(4,'Spanish','es','esl/spa',1,NULL,NULL,'es',NULL,57,56,'2023-04-04 10:54:00',NULL,NULL),(5,'Italian','it','ita',1,NULL,NULL,'it',NULL,38,37,'2020-09-24 12:54:31',NULL,NULL),(6,'Germany','de','deu/ger',1,NULL,NULL,'de',NULL,22,21,'2023-04-04 10:59:16',NULL,NULL),(7,'Azerbaijani','az','aze',2,NULL,NULL,'az',NULL,54,53,'2023-04-04 10:58:49',NULL,NULL),(8,'Albanian','sq','sqi',0,NULL,NULL,'al',NULL,71,71,NULL,NULL,NULL),(9,'Amharic','am','amh',0,NULL,NULL,'et',NULL,2,1,NULL,NULL,NULL),(10,'Afrikaans','af','afr',0,NULL,NULL,'af',NULL,70,70,NULL,NULL,NULL),(11,'Irish','ga','gle',2,NULL,NULL,'ga',NULL,2,1,'2023-04-04 10:52:58',NULL,NULL),(12,'Creole','sc','syc',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(13,'Samoan','sm','smo',2,NULL,NULL,'sm',NULL,2,1,'2023-04-04 10:56:32',NULL,NULL),(14,'Swazi','ss','ssw',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(15,'Sesotho','st','sot',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(16,'Swahili','sw','swa',0,NULL,NULL,'tz',NULL,84,84,NULL,NULL,NULL),(17,'Nyanja','ny','nya',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(18,'Arab','ar','ara',1,NULL,NULL,'ae',1,4,5,'2023-04-04 10:54:06',NULL,'٪s'),(19,'Kurdish','ku','kur',0,NULL,NULL,'tr',NULL,2,1,NULL,NULL,NULL),(20,'Somalia','so','som',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(21,'Armenian','hy','hye/axm',0,NULL,NULL,'am',NULL,7,7,NULL,NULL,NULL),(22,'Bengal','bn','ben',0,NULL,NULL,'bn',NULL,2,1,NULL,NULL,NULL),(23,'Burmese','my','mya',0,NULL,NULL,'my',NULL,2,1,NULL,NULL,NULL),(24,'Bislama','bi','bis',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(25,'Bulgarian','bg','bul',0,NULL,NULL,'bg',NULL,2,1,NULL,NULL,NULL),(26,'Bosnian','bs','bos',0,NULL,NULL,'bs',NULL,72,72,NULL,NULL,NULL),(27,'Serbian','sr','srp',0,NULL,NULL,'sr',NULL,82,82,NULL,NULL,NULL),(28,'Croatian','hr','hrv',0,NULL,NULL,'hr',NULL,74,74,NULL,NULL,NULL),(29,'Hungarian','hu','hun',0,NULL,NULL,'hu',NULL,35,34,NULL,NULL,NULL),(30,'Vietnamese','vi','vie',0,NULL,NULL,'vn',NULL,69,68,NULL,NULL,NULL),(31,'Greek','el','ell',0,NULL,NULL,'gr',NULL,31,30,NULL,NULL,NULL),(32,'Turkish','tr','tur',0,NULL,NULL,'tr',NULL,67,66,NULL,NULL,NULL),(33,'Georgian','ka','kat',0,NULL,NULL,NULL,NULL,54,53,NULL,NULL,NULL),(34,'Guarani','gn','grn',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(35,'Danish','da','dan',0,NULL,NULL,'dk',NULL,21,20,NULL,NULL,NULL),(36,'Dzong-ke','dz','dzo',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(37,'Hebrew','he','heb',0,NULL,NULL,'il',1,2,1,NULL,NULL,NULL),(38,'Indonesian','id','ind',0,NULL,NULL,'id',NULL,37,36,NULL,NULL,NULL),(39,'Icelandic','is','isl',0,NULL,NULL,'is',NULL,76,76,NULL,NULL,NULL),(40,'Aymara','ay','aym',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(41,'Quechua','qu','que',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(42,'Latin','la','lat',0,NULL,NULL,NULL,NULL,45,44,NULL,NULL,NULL),(43,'Kazakh','kk','kaz',0,NULL,NULL,'kz',NULL,54,53,NULL,NULL,NULL),(44,'Catalan','ca','cat',0,NULL,NULL,'ca',NULL,73,73,NULL,NULL,NULL),(45,'Rwanda','rw','kin',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(46,'Rundi','rn','run',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(47,'Chinese','zh','zho',1,NULL,NULL,'cn',NULL,12,11,'2023-03-06 18:59:37',NULL,NULL),(48,'Korean','ko','kor',0,NULL,NULL,'ko',NULL,43,42,NULL,NULL,NULL),(49,'Khmer','km','khm',0,NULL,NULL,'km',NULL,2,1,NULL,NULL,NULL),(50,'Laotian','lo','lao',0,NULL,NULL,'la',NULL,2,1,NULL,NULL,NULL),(51,'Latvian','lv','lav',0,NULL,NULL,'lv',NULL,77,77,NULL,NULL,NULL),(52,'Lithuanian','lt','lit',0,NULL,NULL,'lt',NULL,2,1,NULL,NULL,NULL),(53,'Macedonian','mk','mkd',0,NULL,NULL,NULL,NULL,78,78,NULL,NULL,NULL),(54,'Malagasy','mg','mlg',0,NULL,NULL,'mg',NULL,2,1,NULL,NULL,NULL),(55,'Malay','ms','msa',0,NULL,NULL,'id',NULL,2,1,NULL,NULL,NULL),(56,'Tamil','ta','tam',0,NULL,NULL,'in',NULL,63,63,NULL,NULL,NULL),(57,'Divehi','dv','div',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(58,'Maltese','mt','mlt',0,NULL,NULL,'mt',NULL,2,1,NULL,NULL,NULL),(59,'Moldavian','mo','mol',0,NULL,NULL,NULL,NULL,80,79,NULL,NULL,NULL),(60,'Mongolian','mn','mon',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(61,'Nauru','na','nau',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(62,'Rhetoromanic','rm','roh',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(63,'Luxembourgish','lb','ltz',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(64,'Nepali','ne','nep',0,NULL,NULL,'ne',NULL,2,1,NULL,NULL,NULL),(65,'Dutch','nl','nld',0,NULL,NULL,'nl',NULL,25,24,NULL,NULL,NULL),(66,'Norwegian','no','nor',0,NULL,NULL,'no',NULL,47,46,NULL,NULL,NULL),(67,'Persian','fa','fas',0,NULL,NULL,'ir',1,2,1,NULL,NULL,NULL),(68,'Polish','pl','pol',0,NULL,NULL,'pl',NULL,49,48,NULL,NULL,NULL),(69,'Portuguese','pt','por',0,NULL,NULL,'pt',NULL,51,50,NULL,NULL,NULL),(70,'Tetum',NULL,'tet',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(71,'Pashto','ps','pus',0,NULL,NULL,'pk',1,2,1,NULL,NULL,NULL),(72,'Dari',NULL,'prs',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(73,'Romanian','ro','ron',0,NULL,NULL,'ro',NULL,52,52,NULL,NULL,NULL),(74,'Belorussian','be','bel',0,NULL,NULL,NULL,NULL,54,53,NULL,NULL,NULL),(75,'Kyrgyz','ky','kir',0,NULL,NULL,NULL,NULL,54,53,NULL,NULL,NULL),(76,'Tswana','tn','tsn',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(77,'Slovak','sk','slk',0,NULL,NULL,'sk',NULL,56,55,NULL,NULL,NULL),(78,'Slovenian','sl','slv',0,NULL,NULL,'sl',NULL,2,1,NULL,NULL,NULL),(79,'Tagalog','tl','tgl',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(80,'Tajik','tg','tgk',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(81,'Thai','th','tha',0,NULL,NULL,'th',NULL,65,64,NULL,NULL,NULL),(82,'Sinhalese','si','sin',0,NULL,NULL,NULL,NULL,2,1,NULL,NULL,NULL),(83,'Tigrinya','ti','tir',0,NULL,NULL,'er',NULL,2,1,NULL,NULL,NULL),(84,'Tongan','to','ton',0,NULL,NULL,'to',NULL,2,1,NULL,NULL,NULL),(85,'Turkmen','tk','tuk',0,NULL,NULL,NULL,NULL,54,53,NULL,NULL,NULL),(86,'Uzbek','uz','uzb',0,NULL,NULL,NULL,NULL,54,53,NULL,NULL,NULL),(87,'Ukrainian','uk','ukr',0,NULL,NULL,'ua',NULL,54,53,NULL,NULL,NULL),(88,'Finnish','fi','fin',0,NULL,NULL,'fi',NULL,27,26,NULL,NULL,NULL),(89,'Swedish','sv','swe',0,NULL,NULL,'sv',NULL,62,61,NULL,NULL,NULL),(90,'Czech','cs','ces',0,NULL,NULL,'cz',NULL,19,18,NULL,NULL,NULL),(91,'Estonian','et','est',0,NULL,NULL,'et',NULL,2,1,NULL,NULL,NULL),(92,'Japanese','ja','jpn',1,NULL,NULL,'jp',NULL,40,39,'2023-03-06 18:59:29',NULL,NULL),(93,'Hindi','hi','hin',0,NULL,NULL,'in',NULL,33,32,NULL,NULL,NULL),(94,'Urdu','ur','urd',0,NULL,NULL,'pk',1,2,1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `t_languages`
--

DROP TABLE IF EXISTS `t_languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_languages` (
  `t_language_id` int NOT NULL AUTO_INCREMENT,
  `language_id` int NOT NULL,
  `parent` int NOT NULL,
  `language_name` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`t_language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=854 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `t_languages`
--

LOCK TABLES `t_languages` WRITE;
/*!40000 ALTER TABLE `t_languages` DISABLE KEYS */;
INSERT INTO `t_languages` VALUES (7,1,1,'Русский'),(8,2,1,'Russian'),(9,1,2,NULL),(10,1,140,'Арабский'),(11,3,1,NULL),(12,4,1,NULL),(13,5,1,NULL),(14,6,1,NULL),(15,18,1,'الروسية'),(16,47,1,'俄语'),(17,92,1,'ロシア語'),(18,2,2,'English'),(19,3,2,'Anglais'),(20,4,2,'Inglés'),(21,5,2,'Inglese'),(22,6,2,'Englisch'),(23,18,2,'الإنكليزية'),(24,47,2,'英语'),(25,92,2,'英語'),(26,1,3,'Французский'),(27,2,3,'French'),(28,3,3,'Français'),(29,4,3,'Francés'),(30,5,3,'Francese'),(31,6,3,'Französisch'),(32,18,3,'الفرنسية'),(33,47,3,'法语'),(34,92,3,'フランス語'),(35,1,4,'Испанский'),(36,2,4,'Spanish'),(37,3,4,'Espagnol'),(38,4,4,'Español'),(39,5,4,'Spagnolo'),(40,6,4,'Spanisch'),(41,18,4,'الإسبانية'),(42,47,4,'西班牙语'),(43,92,4,'スペイン語'),(44,1,5,'Итальянский'),(45,2,5,'Italian'),(46,3,5,'Italien'),(47,4,5,'Italiano'),(48,5,5,'Italiano'),(49,6,5,'Italienisch'),(50,18,5,'الإيطالية'),(51,47,5,'意大利语'),(52,92,5,'イタリア語'),(53,1,6,'Германия'),(54,2,6,'Germany'),(55,3,6,'Allemagne'),(56,4,6,'Alemania'),(57,5,6,'Germania'),(58,6,6,'Deutschland'),(59,18,6,'ألمانيا'),(60,47,6,'德国'),(61,92,6,'ドイツ'),(62,1,7,'Азербайджанский'),(63,2,7,'Azerbaijani'),(64,3,7,'Azéri'),(65,4,7,'Azerbaiyano'),(66,5,7,'Azerbaigiano'),(67,6,7,'Aserbaidschanisch'),(68,18,7,'الاذربيجانيه'),(69,47,7,'阿塞拜疆语'),(70,92,7,'アゼルバイジャン語'),(71,1,8,'Албанский'),(72,2,8,'Albanian'),(73,3,8,'Albanais'),(74,4,8,'Albanés'),(75,5,8,'Albanese'),(76,6,8,'Albanisch'),(77,18,8,'الألبانية'),(78,47,8,'阿尔巴尼亚语'),(79,92,8,'アルバニア語'),(80,1,9,'Амхарский'),(81,2,9,'Amharic'),(82,3,9,'Amharique'),(83,4,9,'Amárico'),(84,5,9,'Amarico'),(85,6,9,'Amharisch'),(86,18,9,'الأمهرية'),(87,47,9,'阿姆哈拉语'),(88,92,9,'アムハラ語'),(89,1,10,'Африкаанс'),(90,2,10,'Afrikaans'),(91,3,10,'Afrikaans'),(92,4,10,'Afrikaans'),(93,5,10,'Afrikaans'),(94,6,10,'Afrikaans'),(95,18,10,'الأفريقانية'),(96,47,10,'南非荷兰语'),(97,92,10,'アフリカーンス語'),(98,1,11,'Ирландский'),(99,2,11,'Irish'),(100,3,11,'Irlandais'),(101,4,11,'Irlandés'),(102,5,11,'Irlandese'),(103,6,11,'Irisch'),(104,18,11,'الأيرلندية'),(105,47,11,'爱尔兰语'),(106,92,11,'アイルランド語'),(107,1,12,'Креол'),(108,2,12,'Creole'),(109,3,12,'Créole'),(110,4,12,'Criollo'),(111,5,12,'Creolo'),(112,6,12,'Kreolisch'),(113,18,12,'الكريول'),(114,47,12,'克里奥尔 语'),(115,92,12,'クリオール'),(116,1,13,'Самоанский'),(117,2,13,'Samoan'),(118,3,13,'Samoan'),(119,4,13,'Samoano'),(120,5,13,'Samoano'),(121,6,13,'Samoanisch'),(122,18,13,'ساموا'),(123,47,13,'萨摩亚语'),(124,92,13,'サモア語'),(125,1,14,'Свазиленд'),(126,2,14,'Swazi'),(127,3,14,'Swazi'),(128,4,14,'Suazi'),(129,5,14,'Swazi'),(130,6,14,'Swasiländisch'),(131,18,14,'سوازيلند'),(132,47,14,'斯威士兰'),(133,92,14,'スワジ'),(134,1,15,'Сесото'),(135,2,15,'Sesotho'),(136,3,15,'Sesotho'),(137,4,15,'Sesotho'),(138,5,15,'Sesotho'),(139,6,15,'Sesotho'),(140,18,15,'سيسوتو'),(141,47,15,'塞索托语'),(142,92,15,'セソト'),(143,1,16,'Суахили'),(144,2,16,'Swahili'),(145,3,16,'Swahili'),(146,4,16,'Swahili'),(147,5,16,'Swahili'),(148,6,16,'Swahili'),(149,18,16,'السواحلية'),(150,47,16,'斯瓦希里语'),(151,92,16,'スワヒリ語'),(152,1,17,'Аспект'),(153,2,17,'Aspect'),(154,3,17,'Aspect'),(155,4,17,'Aspecto'),(156,5,17,'Aspetto'),(157,6,17,'Aspekt'),(158,18,17,'الجانب'),(159,47,17,'方面'),(160,92,17,'アスペクト'),(161,1,18,'Араб'),(162,2,18,'Arab'),(163,3,18,'Arabe'),(164,4,18,'Árabe'),(165,5,18,'Arabo'),(166,6,18,'Araber'),(167,18,18,'عربي'),(168,47,18,'阿拉伯人'),(169,92,18,'アラブ'),(170,1,19,'Курдский'),(171,2,19,'Kurdish'),(172,3,19,'Kurde'),(173,4,19,'Kurdo'),(174,5,19,'Curdo'),(175,6,19,'Kurdisch'),(176,18,19,'كردي'),(177,47,19,'库尔德语'),(178,92,19,'クルド語'),(179,1,20,'Сомали'),(180,2,20,'Somalia'),(181,3,20,'Somalie'),(182,4,20,'Somalia'),(183,5,20,'Somalia'),(184,6,20,'Somalia'),(185,18,20,'الصومال'),(186,47,20,'索马里'),(187,92,20,'ソマリア'),(188,1,21,'Армянский'),(189,2,21,'Armenian'),(190,3,21,'Arménien'),(191,4,21,'Armenio'),(192,5,21,'Armeno'),(193,6,21,'Armenisch'),(194,18,21,'الأرمينية'),(195,47,21,'亚美尼亚语'),(196,92,21,'アルメニア語'),(197,1,22,'Бенгальский'),(198,2,22,'Bengal'),(199,3,22,'Bengale'),(200,4,22,'Bengala'),(201,5,22,'Bengala'),(202,6,22,'Bengal'),(203,18,22,'بنغال'),(204,47,22,'孟加拉'),(205,92,22,'ベンガル'),(206,1,23,'Бирманский'),(207,2,23,'Burmese'),(208,3,23,'Birman'),(209,4,23,'Birmano'),(210,5,23,'Birmano'),(211,6,23,'Burmesisch'),(212,18,23,'البورميه'),(213,47,23,'缅甸语'),(214,92,23,'ビルマ語'),(215,1,24,'Бислама'),(216,2,24,'Bislama'),(217,3,24,'Bichlamar'),(218,4,24,'Bislama'),(219,5,24,'Bislama'),(220,6,24,'Bislama'),(221,18,24,'بيسلاما'),(222,47,24,'比斯拉马'),(223,92,24,'ビスラマ'),(224,1,25,'Болгарский'),(225,2,25,'Bulgarian'),(226,3,25,'Bulgare'),(227,4,25,'Búlgaro'),(228,5,25,'Bulgaro'),(229,6,25,'Bulgarisch'),(230,18,25,'البلغارية'),(231,47,25,'保加利亚语'),(232,92,25,'ブルガリア語'),(233,1,26,'Боснийский'),(234,2,26,'Bosnian'),(235,3,26,'Bosniaque'),(236,4,26,'Bosnio'),(237,5,26,'Bosniaco'),(238,6,26,'Bosnisch'),(239,18,26,'البوسنية'),(240,47,26,'波斯尼亚语'),(241,92,26,'ボスニア語'),(242,1,27,'Сербский'),(243,2,27,'Serbian'),(244,3,27,'Serbe'),(245,4,27,'Serbio'),(246,5,27,'Serbo'),(247,6,27,'Serbisch'),(248,18,27,'الصربية'),(249,47,27,'塞尔维亚语'),(250,92,27,'セルビア語'),(251,1,28,'Хорватский'),(252,2,28,'Croatian'),(253,3,28,'Croate'),(254,4,28,'Croata'),(255,5,28,'Croato'),(256,6,28,'Kroatisch'),(257,18,28,'الكرواتية'),(258,47,28,'克罗地亚语'),(259,92,28,'クロアチア語'),(260,1,29,'Венгерский'),(261,2,29,'Hungarian'),(262,3,29,'Hongrois'),(263,4,29,'Húngaro'),(264,5,29,'Ungherese'),(265,6,29,'Ungarisch'),(266,18,29,'المجرية'),(267,47,29,'匈牙利语'),(268,92,29,'ハンガリー語'),(269,1,30,'Вьетнамский'),(270,2,30,'Vietnamese'),(271,3,30,'Vietnamien'),(272,4,30,'Vietnamita'),(273,5,30,'Vietnamita'),(274,6,30,'Vietnamesisch'),(275,18,30,'الفيتنامية'),(276,47,30,'越南语'),(277,92,30,'ベトナム語'),(278,1,31,'Греческий'),(279,2,31,'Greek'),(280,3,31,'Grec'),(281,4,31,'Griego'),(282,5,31,'Greco'),(283,6,31,'Griechisch'),(284,18,31,'اليونانية'),(285,47,31,'希腊语'),(286,92,31,'ギリシャ語'),(287,1,32,'Турецкий'),(288,2,32,'Turkish'),(289,3,32,'Turc'),(290,4,32,'Turco'),(291,5,32,'Turco'),(292,6,32,'Türkisch'),(293,18,32,'التركية'),(294,47,32,'土耳其语'),(295,92,32,'トルコ語'),(296,1,33,'Грузинский'),(297,2,33,'Georgian'),(298,3,33,'Géorgien'),(299,4,33,'Georgiano'),(300,5,33,'Georgiano'),(301,6,33,'Georgisch'),(302,18,33,'الجورجية'),(303,47,33,'乔治亚语'),(304,92,33,'グルジア語'),(305,1,34,'Гуарани'),(306,2,34,'Guaraní'),(307,3,34,'Guarani'),(308,4,34,'Guaraní'),(309,5,34,'Guaraní'),(310,6,34,'Guaraní'),(311,18,34,'غواراني'),(312,47,34,'瓜拉尼'),(313,92,34,'グアラニー語'),(314,1,35,'Датский'),(315,2,35,'Danish'),(316,3,35,'Danois'),(317,4,35,'Danés'),(318,5,35,'Danese'),(319,6,35,'Dänisch'),(320,18,35,'الدانماركية'),(321,47,35,'丹麦语'),(322,92,35,'デンマーク語'),(323,1,36,'Дзонг-кэ'),(324,2,36,'Dzong-ke'),(325,3,36,'Dzong-ke'),(326,4,36,'Dzong-ke'),(327,5,36,'Dzong-ke'),(328,6,36,'Dzong-ke'),(329,18,36,'دزونغ كه'),(330,47,36,'宗科'),(331,92,36,'ゾンケ'),(332,1,37,'Иврит'),(333,2,37,'Hebrew'),(334,3,37,'Hébreu'),(335,4,37,'Hebreo'),(336,5,37,'Ebraico'),(337,6,37,'Hebräisch'),(338,18,37,'العبرية'),(339,47,37,'希伯来语'),(340,92,37,'ヘブライ語'),(341,1,38,'Индонезийский'),(342,2,38,'Indonesian'),(343,3,38,'Indonésien'),(344,4,38,'Indonesio'),(345,5,38,'Indonesiano'),(346,6,38,'Indonesisch'),(347,18,38,'الإندونيسية'),(348,47,38,'印度尼西亚语'),(349,92,38,'インドネシア語'),(350,1,39,'Исландский'),(351,2,39,'Icelandic'),(352,3,39,'Islandais'),(353,4,39,'Islandés'),(354,5,39,'Islandese'),(355,6,39,'Isländisch'),(356,18,39,'الأيسلندية'),(357,47,39,'冰岛语'),(358,92,39,'アイスランド語'),(359,1,40,'Аймара'),(360,2,40,'Aymara'),(361,3,40,'Aymara'),(362,4,40,'Aymara'),(363,5,40,'Aymara'),(364,6,40,'Aymara'),(365,18,40,'ايمارا'),(366,47,40,'艾马拉语'),(367,92,40,'アイマラ'),(368,1,41,'Кечуа'),(369,2,41,'Quechua'),(370,3,41,'Quechua'),(371,4,41,'Quechua'),(372,5,41,'Quechua'),(373,6,41,'Quechua'),(374,18,41,'الكيشوا'),(375,47,41,'克丘亚语'),(376,92,41,'ケチュア語'),(377,1,42,'Латинский'),(378,2,42,'Latin'),(379,3,42,'Latin'),(380,4,42,'Latín'),(381,5,42,'Latino'),(382,6,42,'Latein'),(383,18,42,'لاتينيه'),(384,47,42,'拉丁语'),(385,92,42,'ラテン語'),(386,1,43,'Казахский'),(387,2,43,'Kazakh'),(388,3,43,'Kazakh'),(389,4,43,'Kazajo'),(390,5,43,'Kazaco'),(391,6,43,'Kasachisch'),(392,18,43,'الكازاخية'),(393,47,43,'哈萨克语'),(394,92,43,'カザフ語'),(395,1,44,'Каталанский'),(396,2,44,'Catalan'),(397,3,44,'Catalan'),(398,4,44,'Catalán'),(399,5,44,'Catalano'),(400,6,44,'Katalanisch'),(401,18,44,'الكتالانية'),(402,47,44,'加泰隆语'),(403,92,44,'カタロニア語'),(404,1,45,'Руанда'),(405,2,45,'Rwanda'),(406,3,45,'Rwanda'),(407,4,45,'Ruanda'),(408,5,45,'Ruanda'),(409,6,45,'Ruanda'),(410,18,45,'رواندا'),(411,47,45,'卢旺达'),(412,92,45,'ルワンダ'),(413,1,46,'Круглое железо'),(414,2,46,'Rounds'),(415,3,46,'Tours'),(416,4,46,'Rondas'),(417,5,46,'Giri'),(418,6,46,'Runden'),(419,18,46,'جولات'),(420,47,46,'轮'),(421,92,46,'ラウンド'),(422,1,47,'Китайский'),(423,2,47,'Chinese'),(424,3,47,'Chinois'),(425,4,47,'Chino'),(426,5,47,'Cinese'),(427,6,47,'Chinesisch'),(428,18,47,'الصينية'),(429,47,47,'中文'),(430,92,47,'中国語'),(431,1,48,'Корейский'),(432,2,48,'Korean'),(433,3,48,'Coréen'),(434,4,48,'Coreano'),(435,5,48,'Coreano'),(436,6,48,'Koreanisch'),(437,18,48,'الكورية'),(438,47,48,'朝鲜语'),(439,92,48,'韓国語'),(440,1,49,'Кхмерский'),(441,2,49,'Khmer'),(442,3,49,'Khmer'),(443,4,49,'Jemer'),(444,5,49,'Khmer'),(445,6,49,'Khmer'),(446,18,49,'خميرية'),(447,47,49,'高棉语'),(448,92,49,'クメール語'),(449,1,50,'Лаосский'),(450,2,50,'Laotian'),(451,3,50,'Laotien'),(452,4,50,'Laosiano'),(453,5,50,'Laotiano'),(454,6,50,'Laotisch'),(455,18,50,'لاوس'),(456,47,50,'老挝'),(457,92,50,'ラオスの'),(458,1,51,'Латышский'),(459,2,51,'Latvian'),(460,3,51,'Letton'),(461,4,51,'Letón'),(462,5,51,'Lettone'),(463,6,51,'Lettisch'),(464,18,51,'اللاتفية'),(465,47,51,'拉脱维亚语'),(466,92,51,'ラトビア語'),(467,1,52,'Литовский'),(468,2,52,'Lithuanian'),(469,3,52,'Lituanien'),(470,4,52,'Lituano'),(471,5,52,'Lituano'),(472,6,52,'Litauisch'),(473,18,52,'الليتوانية'),(474,47,52,'立陶宛语'),(475,92,52,'リトアニア語'),(476,1,53,'Македонец'),(477,2,53,'Macedonian'),(478,3,53,'Macédonien'),(479,4,53,'Macedonio'),(480,5,53,'Macedone'),(481,6,53,'Mazedonisch'),(482,18,53,'مقدوني'),(483,47,53,'马其顿语'),(484,92,53,'マケドニアの'),(485,1,54,'Малагасийский'),(486,2,54,'Malagasy'),(487,3,54,'Malgache'),(488,4,54,'Malgache'),(489,5,54,'Malgascio'),(490,6,54,'Madagassisch'),(491,18,54,'الملغاشيه'),(492,47,54,'马达加斯加语'),(493,92,54,'マダガスカル語'),(494,1,55,'Малайский'),(495,2,55,'Malay'),(496,3,55,'Malaisien'),(497,4,55,'Malayo'),(498,5,55,'Malese'),(499,6,55,'Malaiisch'),(500,18,55,'الماليزية'),(501,47,55,'马来语'),(502,92,55,'マレー語'),(503,1,56,'Тамильский'),(504,2,56,'Tamil'),(505,3,56,'Tamil'),(506,4,56,'Tamil'),(507,5,56,'Tamil'),(508,6,56,'Tamil'),(509,18,56,'التاميلية'),(510,47,56,'泰米尔语'),(511,92,56,'タミール語'),(512,1,57,'Дивехи'),(513,2,57,'Divehi'),(514,3,57,'Maldivien'),(515,4,57,'Divehi'),(516,5,57,'Divehi'),(517,6,57,'Divehi'),(518,18,57,'ديفيهي'),(519,47,57,'迪维希'),(520,92,57,'ディベヒ語'),(521,1,58,'Мальтийский'),(522,2,58,'Maltese'),(523,3,58,'Maltais'),(524,4,58,'Maltés'),(525,5,58,'Maltese'),(526,6,58,'Maltesisch'),(527,18,58,'المالطية'),(528,47,58,'马耳他语'),(529,92,58,'マルタ語'),(530,1,59,'Молдаванин'),(531,2,59,'Moldavian'),(532,3,59,'Moldave'),(533,4,59,'Moldavo'),(534,5,59,'Moldavo'),(535,6,59,'Moldauisch'),(536,18,59,'المولدافية'),(537,47,59,'摩尔达维亚语'),(538,92,59,'モルダビア語'),(539,1,60,'Монгольский'),(540,2,60,'Mongolian'),(541,3,60,'Mongol'),(542,4,60,'Mongol'),(543,5,60,'Mongolo'),(544,6,60,'Mongolisch'),(545,18,60,'المنغولية'),(546,47,60,'蒙古语'),(547,92,60,'モンゴル語'),(548,1,61,'Науру'),(549,2,61,'Nauru'),(550,3,61,'Nauru'),(551,4,61,'Nauru'),(552,5,61,'Nauru'),(553,6,61,'Nauru'),(554,18,61,'ناورو'),(555,47,61,'瑙鲁'),(556,92,61,'ナウル'),(557,1,62,'Ретороманик'),(558,2,62,'Rhetoromanic'),(559,3,62,'Rhétoromanique'),(560,4,62,'Retorománico'),(561,5,62,'Retoromanico'),(562,6,62,'Rhetoromanisch'),(563,18,62,'ريتورومانيك'),(564,47,62,'修辞狂'),(565,92,62,'レトロマンティック'),(566,1,63,'Люксембургский'),(567,2,63,'Luxembourgish'),(568,3,63,'Luxembourgeois'),(569,4,63,'Luxemburgués'),(570,5,63,'Lussemburghese'),(571,6,63,'Luxemburgisch'),(572,18,63,'اللوكسمبرجية'),(573,47,63,'卢森堡语'),(574,92,63,'ルクセンブルク語'),(575,1,64,'Непальский'),(576,2,64,'Nepali'),(577,3,64,'Népalais'),(578,4,64,'Nepalí'),(579,5,64,'Nepalese'),(580,6,64,'Nepalesisch'),(581,18,64,'النيبالية'),(582,47,64,'尼泊尔语'),(583,92,64,'ネパール語'),(584,1,65,'Нидерландский'),(585,2,65,'Dutch'),(586,3,65,'Néerlandais'),(587,4,65,'Holandés'),(588,5,65,'Olandese'),(589,6,65,'Holländisch'),(590,18,65,'الهولندية'),(591,47,65,'荷兰语'),(592,92,65,'オランダ語'),(593,1,66,'Норвежский'),(594,2,66,'Norwegian'),(595,3,66,'Norvégien'),(596,4,66,'Noruego'),(597,5,66,'Norvegese'),(598,6,66,'Norwegisch'),(599,18,66,'النرويجية ‏'),(600,47,66,'挪威语'),(601,92,66,'ノルウェー語'),(602,1,67,'Персидский'),(603,2,67,'Persian'),(604,3,67,'Perse'),(605,4,67,'Persa'),(606,5,67,'Farsi'),(607,6,67,'Persisch'),(608,18,67,'فارسي'),(609,47,67,'波斯语'),(610,92,67,'ペルシャ語'),(611,1,68,'Польский'),(612,2,68,'Polish'),(613,3,68,'Polonais'),(614,4,68,'Polaco'),(615,5,68,'Polacco'),(616,6,68,'Polnisch'),(617,18,68,'البولندية'),(618,47,68,'波兰语'),(619,92,68,'ポーランド語'),(620,1,69,'Португальский'),(621,2,69,'Portuguese'),(622,3,69,'Portugais'),(623,4,69,'Portugués'),(624,5,69,'Portoghese'),(625,6,69,'Portugiesisch'),(626,18,69,'البرتغالية'),(627,47,69,'葡萄牙语'),(628,92,69,'ポルトガル語'),(629,1,70,'Тетум'),(630,2,70,'Tetum'),(631,3,70,'Tetum'),(632,4,70,'Tetun'),(633,5,70,'Tetum'),(634,6,70,'Tetum'),(635,18,70,'تيتوم'),(636,47,70,'德顿语'),(637,92,70,'テトゥン'),(638,1,71,'Пушту'),(639,2,71,'Pashto'),(640,3,71,'Pachtou'),(641,4,71,'Pashto'),(642,5,71,'Pashto'),(643,6,71,'Puschtu'),(644,18,71,'الباشتو'),(645,47,71,'普什图语'),(646,92,71,'パシュトー語'),(647,1,72,'От'),(648,2,72,'From'),(649,3,72,'De'),(650,4,72,'De'),(651,5,72,'Da'),(652,6,72,'Von'),(653,18,72,'من'),(654,47,72,'从'),(655,92,72,'差出人'),(656,1,73,'Румынский'),(657,2,73,'Romanian'),(658,3,73,'Roumain'),(659,4,73,'Rumano'),(660,5,73,'Rumeno'),(661,6,73,'Rumänisch'),(662,18,73,'الرومانية'),(663,47,73,'罗马尼亚'),(664,92,73,'ルーマニア語'),(665,1,74,'Белорусский'),(666,2,74,'Belorussian'),(667,3,74,'Biélorusse'),(668,4,74,'Bielorruso'),(669,5,74,'Bielorusso'),(670,6,74,'Weißrussisch'),(671,18,74,'بيلوروسية'),(672,47,74,'白俄罗斯语'),(673,92,74,'ベラルーシ語'),(674,1,75,'Киргизский'),(675,2,75,'Kyrgyz'),(676,3,75,'Kirghiz'),(677,4,75,'Kirguizo'),(678,5,75,'Kirghiso'),(679,6,75,'Kirgisisch'),(680,18,75,'القرقيزية'),(681,47,75,'吉尔吉斯语'),(682,92,75,'キルギス語'),(683,1,76,'Тсвана'),(684,2,76,'Tswana'),(685,3,76,'Tswana'),(686,4,76,'Setswana'),(687,5,76,'Tswana'),(688,6,76,'Tswana'),(689,18,76,'التسوانية'),(690,47,76,'茨瓦纳语'),(691,92,76,'ツワナ語'),(692,1,77,'Словацкий'),(693,2,77,'Slovak'),(694,3,77,'Slovaque'),(695,4,77,'Eslovaco'),(696,5,77,'Slovacco'),(697,6,77,'Slowakisch'),(698,18,77,'السلوفاكية'),(699,47,77,'斯洛伐克语'),(700,92,77,'スロバキア語'),(701,1,78,'Словенский'),(702,2,78,'Slovenian'),(703,3,78,'Slovène'),(704,4,78,'Esloveno'),(705,5,78,'Sloveno'),(706,6,78,'Slowenisch'),(707,18,78,'السلوفينية'),(708,47,78,'斯洛文尼亚语'),(709,92,78,'スロベニア語'),(710,1,79,'Тагальский'),(711,2,79,'Tagalog'),(712,3,79,'Tagalog'),(713,4,79,'Tagalo'),(714,5,79,'Tagalog'),(715,6,79,'Tagalog'),(716,18,79,'التغالوغيه'),(717,47,79,'塔加洛语'),(718,92,79,'タガログ語'),(719,1,80,'Таджикский'),(720,2,80,'Tajik'),(721,3,80,'Tadjik'),(722,4,80,'Tayiko'),(723,5,80,'Tagico'),(724,6,80,'Tadschikisch'),(725,18,80,'الطاجيكي'),(726,47,80,'塔吉克斯坦'),(727,92,80,'タジク'),(728,1,81,'Тайский'),(729,2,81,'Thai'),(730,3,81,'Thaï'),(731,4,81,'Tailandés'),(732,5,81,'Tailandese'),(733,6,81,'Thailändisch'),(734,18,81,'التايلندية'),(735,47,81,'泰语'),(736,92,81,'タイ語'),(737,1,82,'Сингальский'),(738,2,82,'Sinhalese'),(739,3,82,'Cingalais'),(740,4,82,'Cingalés'),(741,5,82,'Singalese'),(742,6,82,'Singhalesisch'),(743,18,82,'السنهاليه'),(744,47,82,'僧伽罗语'),(745,92,82,'シンハラ語'),(746,1,83,'Тигринья'),(747,2,83,'Tigrinya'),(748,3,83,'Tigrigna'),(749,4,83,'Tigriña'),(750,5,83,'Tigrigna'),(751,6,83,'Tigrinya'),(752,18,83,'التغرينية'),(753,47,83,'提格里尼亚语'),(754,92,83,'ティグリニャ語'),(755,1,84,'Тонганский'),(756,2,84,'Tongan'),(757,3,84,'Tonguien'),(758,4,84,'Tongano'),(759,5,84,'Tongano'),(760,6,84,'Tongaisch'),(761,18,84,'تونغا'),(762,47,84,'汤加语'),(763,92,84,'トンガ語'),(764,1,85,'Туркменский'),(765,2,85,'Turkmen'),(766,3,85,'Turkmène'),(767,4,85,'Turcomano'),(768,5,85,'Turkmeno'),(769,6,85,'Turkmenisch'),(770,18,85,'التركمانية'),(771,47,85,'土库曼语'),(772,92,85,'トルクメン語'),(773,1,86,'Узбекский'),(774,2,86,'Uzbek'),(775,3,86,'Ouzbek'),(776,4,86,'Uzbek'),(777,5,86,'Uzbeco'),(778,6,86,'Usbekisch'),(779,18,86,'الأوزبكية'),(780,47,86,'乌兹别克语'),(781,92,86,'ウズベク語'),(782,1,87,'Украинский'),(783,2,87,'Ukrainian'),(784,3,87,'Ukrainien'),(785,4,87,'Ucraniano'),(786,5,87,'Ucraino'),(787,6,87,'Ukrainisch'),(788,18,87,'الأوكرانية'),(789,47,87,'乌克兰语'),(790,92,87,'ウクライナ語'),(791,1,88,'Финский'),(792,2,88,'Finnish'),(793,3,88,'Finnois'),(794,4,88,'Finlandés'),(795,5,88,'Finlandese'),(796,6,88,'Finnisch'),(797,18,88,'الفنلندية'),(798,47,88,'芬兰语'),(799,92,88,'フィンランド語'),(800,1,89,'Шведский'),(801,2,89,'Swedish'),(802,3,89,'Suédois'),(803,4,89,'Sueco'),(804,5,89,'Svedese'),(805,6,89,'Schwedisch'),(806,18,89,'السويدية'),(807,47,89,'瑞典语'),(808,92,89,'スウェーデン語'),(809,1,90,'Чешский'),(810,2,90,'Czech'),(811,3,90,'Tchèque'),(812,4,90,'Checo'),(813,5,90,'Ceco'),(814,6,90,'Tschechisch'),(815,18,90,'التشيكية'),(816,47,90,'捷克语'),(817,92,90,'チェコ語'),(818,1,91,'Эстонский'),(819,2,91,'Estonian'),(820,3,91,'Estonien'),(821,4,91,'Estonio'),(822,5,91,'Estone'),(823,6,91,'Estnisch'),(824,18,91,'الإستونية'),(825,47,91,'爱沙尼亚语'),(826,92,91,'エストニア語'),(827,1,92,'Японский'),(828,2,92,'Japanese'),(829,3,92,'Japonais'),(830,4,92,'Japonés'),(831,5,92,'Giapponese'),(832,6,92,'Japanisch'),(833,18,92,'اليابانية'),(834,47,92,'日语'),(835,92,92,'日本語'),(836,1,93,'Нет'),(837,2,93,'No'),(838,3,93,'Non'),(839,4,93,'No'),(840,5,93,'No'),(841,6,93,'Nein'),(842,18,93,'لا'),(843,47,93,'不'),(844,92,93,'いいえ'),(845,1,94,'Урду'),(846,2,94,'Urdu'),(847,3,94,'Urdu'),(848,4,94,'Urdu'),(849,5,94,'Urdu'),(850,6,94,'Urdu'),(851,18,94,'الأوردية'),(852,47,94,'乌都语'),(853,92,94,'ウルドゥ語');
/*!40000 ALTER TABLE `t_languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timezones`
--

DROP TABLE IF EXISTS `timezones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `timezones` (
  `timezone_id` int NOT NULL AUTO_INCREMENT,
  `timezone_standart` varchar(64) NOT NULL,
  `timezone_value` varchar(64) NOT NULL,
  `timezone_offset` decimal(10,2) DEFAULT NULL,
  `timezone_code` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`timezone_id`)
) ENGINE=InnoDB AUTO_INCREMENT=425 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timezones`
--

LOCK TABLES `timezones` WRITE;
/*!40000 ALTER TABLE `timezones` DISABLE KEYS */;
INSERT INTO `timezones` VALUES (1,'Africa/Abidjan','Africa/Abidjan',0.00,'z'),(2,'Africa/Accra','Africa/Accra',0.00,NULL),(3,'Africa/Addis_Ababa','Africa/Addis_Ababa',3.00,'c'),(4,'Africa/Algiers','Africa/Algiers',1.00,'a'),(5,'Africa/Asmara','Africa/Asmara',3.00,NULL),(6,'Africa/Bamako','Africa/Bamako',0.00,NULL),(7,'Africa/Bangui','Africa/Bangui',1.00,NULL),(8,'Africa/Banjul','Africa/Banjul',0.00,NULL),(9,'Africa/Bissau','Africa/Bissau',0.00,NULL),(10,'Africa/Blantyre','Africa/Blantyre',2.00,'b'),(11,'Africa/Brazzaville','Africa/Brazzaville',1.00,NULL),(12,'Africa/Bujumbura','Africa/Bujumbura',2.00,NULL),(13,'Africa/Cairo','Africa/Cairo',2.00,NULL),(14,'Africa/Casablanca','Africa/Casablanca',1.00,NULL),(15,'Africa/Ceuta','Africa/Ceuta',2.00,NULL),(16,'Africa/Conakry','Africa/Conakry',0.00,NULL),(17,'Africa/Dakar','Africa/Dakar',0.00,NULL),(18,'Africa/Dar_es_Salaam','Africa/Dar_es_Salaam',3.00,NULL),(19,'Africa/Djibouti','Africa/Djibouti',3.00,NULL),(20,'Africa/Douala','Africa/Douala',1.00,NULL),(21,'Africa/El_Aaiun','Africa/El_Aaiun',1.00,NULL),(22,'Africa/Freetown','Africa/Freetown',0.00,NULL),(23,'Africa/Gaborone','Africa/Gaborone',2.00,NULL),(24,'Africa/Harare','Africa/Harare',2.00,NULL),(25,'Africa/Johannesburg','Africa/Johannesburg',2.00,NULL),(26,'Africa/Juba','Africa/Juba',3.00,NULL),(27,'Africa/Kampala','Africa/Kampala',3.00,NULL),(28,'Africa/Khartoum','Africa/Khartoum',2.00,NULL),(29,'Africa/Kigali','Africa/Kigali',2.00,NULL),(30,'Africa/Kinshasa','Africa/Kinshasa',1.00,NULL),(31,'Africa/Lagos','Africa/Lagos',1.00,NULL),(32,'Africa/Libreville','Africa/Libreville',1.00,NULL),(33,'Africa/Lome','Africa/Lome',0.00,NULL),(34,'Africa/Luanda','Africa/Luanda',1.00,NULL),(35,'Africa/Lubumbashi','Africa/Lubumbashi',2.00,NULL),(36,'Africa/Lusaka','Africa/Lusaka',2.00,NULL),(37,'Africa/Malabo','Africa/Malabo',1.00,NULL),(38,'Africa/Maputo','Africa/Maputo',2.00,NULL),(39,'Africa/Maseru','Africa/Maseru',2.00,NULL),(40,'Africa/Mbabane','Africa/Mbabane',2.00,NULL),(41,'Africa/Mogadishu','Africa/Mogadishu',3.00,NULL),(42,'Africa/Monrovia','Africa/Monrovia',0.00,NULL),(43,'Africa/Nairobi','Africa/Nairobi',3.00,NULL),(44,'Africa/Ndjamena','Africa/Ndjamena',1.00,NULL),(45,'Africa/Niamey','Africa/Niamey',1.00,NULL),(46,'Africa/Nouakchott','Africa/Nouakchott',0.00,NULL),(47,'Africa/Ouagadougou','Africa/Ouagadougou',0.00,NULL),(48,'Africa/Porto-Novo','Africa/Porto-Novo',1.00,NULL),(49,'Africa/Sao_Tome','Africa/Sao_Tome',0.00,NULL),(50,'Africa/Tripoli','Africa/Tripoli',2.00,NULL),(51,'Africa/Tunis','Africa/Tunis',1.00,NULL),(52,'Africa/Windhoek','Africa/Windhoek',2.00,NULL),(53,'America/Adak','America/Adak',-9.00,'v'),(54,'America/Anchorage','America/Anchorage',-8.00,'u'),(55,'America/Anguilla','America/Anguilla',-4.00,'q'),(56,'America/Antigua','America/Antigua',-4.00,NULL),(57,'America/Araguaina','America/Araguaina',-3.00,'p'),(58,'America/Argentina/Buenos_Aires','America/Argentina/Buenos_Aires',-3.00,NULL),(59,'America/Argentina/Catamarca','America/Argentina/Catamarca',-3.00,NULL),(60,'America/Argentina/Cordoba','America/Argentina/Cordoba',-3.00,NULL),(61,'America/Argentina/Jujuy','America/Argentina/Jujuy',-3.00,NULL),(62,'America/Argentina/La_Rioja','America/Argentina/La_Rioja',-3.00,NULL),(63,'America/Argentina/Mendoza','America/Argentina/Mendoza',-3.00,NULL),(64,'America/Argentina/Rio_Gallegos','America/Argentina/Rio_Gallegos',-3.00,NULL),(65,'America/Argentina/Salta','America/Argentina/Salta',-3.00,NULL),(66,'America/Argentina/San_Juan','America/Argentina/San_Juan',-3.00,NULL),(67,'America/Argentina/San_Luis','America/Argentina/San_Luis',-3.00,NULL),(68,'America/Argentina/Tucuman','America/Argentina/Tucuman',-3.00,NULL),(69,'America/Argentina/Ushuaia','America/Argentina/Ushuaia',-3.00,NULL),(70,'America/Aruba','America/Aruba',-4.00,NULL),(71,'America/Asuncion','America/Asuncion',-4.00,NULL),(72,'America/Atikokan','America/Atikokan',-5.00,'r'),(73,'America/Bahia','America/Bahia',-3.00,NULL),(74,'America/Bahia_Banderas','America/Bahia_Banderas',-5.00,NULL),(75,'America/Barbados','America/Barbados',-4.00,NULL),(76,'America/Belem','America/Belem',-3.00,NULL),(77,'America/Belize','America/Belize',-6.00,'s'),(78,'America/Blanc-Sablon','America/Blanc-Sablon',-4.00,NULL),(79,'America/Boa_Vista','America/Boa_Vista',-4.00,NULL),(80,'America/Bogota','America/Bogota',-5.00,NULL),(81,'America/Boise','America/Boise',-6.00,NULL),(82,'America/Cambridge_Bay','America/Cambridge_Bay',-6.00,NULL),(83,'America/Campo_Grande','America/Campo_Grande',-4.00,NULL),(84,'America/Cancun','America/Cancun',-5.00,NULL),(85,'America/Caracas','America/Caracas',-4.00,NULL),(86,'America/Cayenne','America/Cayenne',-3.00,NULL),(87,'America/Cayman','America/Cayman',-5.00,NULL),(88,'America/Chicago','America/Chicago',-5.00,NULL),(89,'America/Chihuahua','America/Chihuahua',-6.00,NULL),(90,'America/Costa_Rica','America/Costa_Rica',-6.00,NULL),(91,'America/Creston','America/Creston',-7.00,'t'),(92,'America/Cuiaba','America/Cuiaba',-4.00,NULL),(93,'America/Curacao','America/Curacao',-4.00,NULL),(94,'America/Danmarkshavn','America/Danmarkshavn',0.00,NULL),(95,'America/Dawson','America/Dawson',-7.00,NULL),(96,'America/Dawson_Creek','America/Dawson_Creek',-7.00,NULL),(97,'America/Denver','America/Denver',-6.00,NULL),(98,'America/Detroit','America/Detroit',-4.00,NULL),(99,'America/Dominica','America/Dominica',-4.00,NULL),(100,'America/Edmonton','America/Edmonton',-6.00,NULL),(101,'America/Eirunepe','America/Eirunepe',-5.00,NULL),(102,'America/El_Salvador','America/El_Salvador',-6.00,NULL),(103,'America/Fortaleza','America/Fortaleza',-3.00,NULL),(104,'America/Fort_Nelson','America/Fort_Nelson',-7.00,NULL),(105,'America/Glace_Bay','America/Glace_Bay',-3.00,NULL),(106,'America/Godthab','America/Godthab',-2.00,'o'),(107,'America/Goose_Bay','America/Goose_Bay',-3.00,NULL),(108,'America/Grand_Turk','America/Grand_Turk',-4.00,NULL),(109,'America/Grenada','America/Grenada',-4.00,NULL),(110,'America/Guadeloupe','America/Guadeloupe',-4.00,NULL),(111,'America/Guatemala','America/Guatemala',-6.00,NULL),(112,'America/Guayaquil','America/Guayaquil',-5.00,NULL),(113,'America/Guyana','America/Guyana',-4.00,NULL),(114,'America/Halifax','America/Halifax',-3.00,NULL),(115,'America/Havana','America/Havana',-4.00,NULL),(116,'America/Hermosillo','America/Hermosillo',-7.00,NULL),(117,'America/Indiana/Indianapolis','America/Indiana/Indianapolis',-4.00,NULL),(118,'America/Indiana/Knox','America/Indiana/Knox',-5.00,NULL),(119,'America/Indiana/Marengo','America/Indiana/Marengo',-4.00,NULL),(120,'America/Indiana/Petersburg','America/Indiana/Petersburg',-4.00,NULL),(121,'America/Indiana/Tell_City','America/Indiana/Tell_City',-5.00,NULL),(122,'America/Indiana/Vevay','America/Indiana/Vevay',-4.00,NULL),(123,'America/Indiana/Vincennes','America/Indiana/Vincennes',-4.00,NULL),(124,'America/Indiana/Winamac','America/Indiana/Winamac',-4.00,NULL),(125,'America/Inuvik','America/Inuvik',-6.00,NULL),(126,'America/Iqaluit','America/Iqaluit',-4.00,NULL),(127,'America/Jamaica','America/Jamaica',-5.00,NULL),(128,'America/Juneau','America/Juneau',-8.00,NULL),(129,'America/Kentucky/Louisville','America/Kentucky/Louisville',-4.00,NULL),(130,'America/Kentucky/Monticello','America/Kentucky/Monticello',-4.00,NULL),(131,'America/Kralendijk','America/Kralendijk',-4.00,NULL),(132,'America/La_Paz','America/La_Paz',-4.00,NULL),(133,'America/Lima','America/Lima',-5.00,NULL),(134,'America/Los_Angeles','America/Los_Angeles',-11.00,NULL),(135,'America/Lower_Princes','America/Lower_Princes',-4.00,NULL),(136,'America/Maceio','America/Maceio',-3.00,NULL),(137,'America/Managua','America/Managua',-6.00,NULL),(138,'America/Manaus','America/Manaus',-4.00,NULL),(139,'America/Marigot','America/Marigot',-4.00,NULL),(140,'America/Martinique','America/Martinique',-4.00,NULL),(141,'America/Matamoros','America/Matamoros',-5.00,NULL),(142,'America/Mazatlan','America/Mazatlan',-6.00,NULL),(143,'America/Menominee','America/Menominee',-5.00,NULL),(144,'America/Merida','America/Merida',-5.00,NULL),(145,'America/Metlakatla','America/Metlakatla',-8.00,NULL),(146,'America/Mexico_City','America/Mexico_City',-5.00,NULL),(147,'America/Miquelon','America/Miquelon',-2.00,NULL),(148,'America/Moncton','America/Moncton',-3.00,NULL),(149,'America/Monterrey','America/Monterrey',-5.00,NULL),(150,'America/Montevideo','America/Montevideo',-3.00,NULL),(151,'America/Montserrat','America/Montserrat',-4.00,NULL),(152,'America/Nassau','America/Nassau',-4.00,NULL),(153,'America/New_York','America/New_York',-4.00,NULL),(154,'America/Nipigon','America/Nipigon',-4.00,NULL),(155,'America/Nome','America/Nome',-8.00,NULL),(156,'America/Noronha','America/Noronha',-2.00,NULL),(157,'America/North_Dakota/Beulah','America/North_Dakota/Beulah',-5.00,NULL),(158,'America/North_Dakota/Center','America/North_Dakota/Center',-5.00,NULL),(159,'America/North_Dakota/New_Salem','America/North_Dakota/New_Salem',-5.00,NULL),(160,'America/Ojinaga','America/Ojinaga',-6.00,NULL),(161,'America/Panama','America/Panama',-5.00,NULL),(162,'America/Pangnirtung','America/Pangnirtung',-4.00,NULL),(163,'America/Paramaribo','America/Paramaribo',-3.00,NULL),(164,'America/Phoenix','America/Phoenix',-7.00,NULL),(165,'America/Port-au-Prince','America/Port-au-Prince',-4.00,NULL),(166,'America/Porto_Velho','America/Porto_Velho',-4.00,NULL),(167,'America/Port_of_Spain','America/Port_of_Spain',-4.00,NULL),(168,'America/Puerto_Rico','America/Puerto_Rico',-4.00,NULL),(169,'America/Rainy_River','America/Rainy_River',-5.00,NULL),(170,'America/Rankin_Inlet','America/Rankin_Inlet',-5.00,NULL),(171,'America/Recife','America/Recife',-3.00,NULL),(172,'America/Regina','America/Regina',-6.00,NULL),(173,'America/Resolute','America/Resolute',-5.00,NULL),(174,'America/Rio_Branco','America/Rio_Branco',-5.00,NULL),(175,'America/Santarem','America/Santarem',-3.00,NULL),(176,'America/Santiago','America/Santiago',-3.00,NULL),(177,'America/Santo_Domingo','America/Santo_Domingo',-4.00,NULL),(178,'America/Sao_Paulo','America/Sao_Paulo',-3.00,NULL),(179,'America/Scoresbysund','America/Scoresbysund',0.00,NULL),(180,'America/Sitka','America/Sitka',-8.00,NULL),(181,'America/St_Barthelemy','America/St_Barthelemy',-4.00,NULL),(182,'America/St_Johns','America/St_Johns',-2.50,NULL),(183,'America/St_Kitts','America/St_Kitts',-4.00,NULL),(184,'America/St_Lucia','America/St_Lucia',-4.00,NULL),(185,'America/St_Thomas','America/St_Thomas',-4.00,NULL),(186,'America/St_Vincent','America/St_Vincent',-4.00,NULL),(187,'America/Swift_Current','America/Swift_Current',-6.00,NULL),(188,'America/Tegucigalpa','America/Tegucigalpa',-6.00,NULL),(189,'America/Thule','America/Thule',-3.00,NULL),(190,'America/Thunder_Bay','America/Thunder_Bay',-4.00,NULL),(191,'America/Tijuana','America/Tijuana',-7.00,NULL),(192,'America/Toronto','America/Toronto',-4.00,NULL),(193,'America/Tortola','America/Tortola',-4.00,NULL),(194,'America/Vancouver','America/Vancouver',-7.00,NULL),(195,'America/Whitehorse','America/Whitehorse',-7.00,NULL),(196,'America/Winnipeg','America/Winnipeg',-5.00,NULL),(197,'America/Yakutat','America/Yakutat',-8.00,NULL),(198,'America/Yellowknife','America/Yellowknife',-6.00,NULL),(199,'Antarctica/Casey','Antarctica/Casey',8.00,'h'),(200,'Antarctica/Davis','Antarctica/Davis',7.00,'g'),(201,'Antarctica/DumontDUrville','Antarctica/DumontDUrville',10.00,'k'),(202,'Antarctica/Macquarie','Antarctica/Macquarie',11.00,'l'),(203,'Antarctica/Mawson','Antarctica/Mawson',5.00,'e'),(204,'Antarctica/McMurdo','Antarctica/McMurdo',12.00,'m'),(205,'Antarctica/Palmer','Antarctica/Palmer',-3.00,NULL),(206,'Antarctica/Rothera','Antarctica/Rothera',-3.00,NULL),(207,'Antarctica/Syowa','Antarctica/Syowa',3.00,NULL),(208,'Antarctica/Troll','Antarctica/Troll',2.00,NULL),(209,'Antarctica/Vostok','Antarctica/Vostok',6.00,'f'),(210,'Arctic/Longyearbyen','Arctic/Longyearbyen',2.00,NULL),(211,'Asia/Aden','Asia/Aden',3.00,NULL),(212,'Asia/Almaty','Asia/Almaty',6.00,NULL),(213,'Asia/Amman','Asia/Amman',3.00,NULL),(214,'Asia/Anadyr','Asia/Anadyr',12.00,NULL),(215,'Asia/Aqtau','Asia/Aqtau',5.00,NULL),(216,'Asia/Aqtobe','Asia/Aqtobe',5.00,NULL),(217,'Asia/Ashgabat','Asia/Ashgabat',5.00,NULL),(218,'Asia/Atyrau','Asia/Atyrau',5.00,NULL),(219,'Asia/Baghdad','Asia/Baghdad',3.00,NULL),(220,'Asia/Bahrain','Asia/Bahrain',3.00,NULL),(221,'Asia/Baku','Asia/Baku',4.00,'d'),(222,'Asia/Bangkok','Asia/Bangkok',7.00,NULL),(223,'Asia/Barnaul','Asia/Barnaul',7.00,NULL),(224,'Asia/Beirut','Asia/Beirut',3.00,NULL),(225,'Asia/Bishkek','Asia/Bishkek',6.00,NULL),(226,'Asia/Brunei','Asia/Brunei',8.00,NULL),(227,'Asia/Chita','Asia/Chita',9.00,'i'),(228,'Asia/Choibalsan','Asia/Choibalsan',8.00,NULL),(229,'Asia/Colombo','Asia/Colombo',5.50,NULL),(230,'Asia/Damascus','Asia/Damascus',3.00,NULL),(231,'Asia/Dhaka','Asia/Dhaka',6.00,NULL),(232,'Asia/Dili','Asia/Dili',9.00,NULL),(233,'Asia/Dubai','Asia/Dubai',4.00,NULL),(234,'Asia/Dushanbe','Asia/Dushanbe',5.00,NULL),(235,'Asia/Famagusta','Asia/Famagusta',3.00,NULL),(236,'Asia/Gaza','Asia/Gaza',3.00,NULL),(237,'Asia/Hebron','Asia/Hebron',3.00,NULL),(238,'Asia/Hong_Kong','Asia/Hong_Kong',8.00,NULL),(239,'Asia/Hovd','Asia/Hovd',7.00,NULL),(240,'Asia/Ho_Chi_Minh','Asia/Ho_Chi_Minh',7.00,NULL),(241,'Asia/Irkutsk','Asia/Irkutsk',8.00,NULL),(242,'Asia/Jakarta','Asia/Jakarta',7.00,NULL),(243,'Asia/Jayapura','Asia/Jayapura',9.00,NULL),(244,'Asia/Jerusalem','Asia/Jerusalem',3.00,NULL),(245,'Asia/Kabul','Asia/Kabul',4.50,NULL),(246,'Asia/Kamchatka','Asia/Kamchatka',12.00,NULL),(247,'Asia/Karachi','Asia/Karachi',5.00,NULL),(248,'Asia/Kathmandu','Asia/Kathmandu',5.75,NULL),(249,'Asia/Khandyga','Asia/Khandyga',9.00,NULL),(250,'Asia/Kolkata','Asia/Kolkata',5.50,NULL),(251,'Asia/Krasnoyarsk','Asia/Krasnoyarsk',7.00,NULL),(252,'Asia/Kuala_Lumpur','Asia/Kuala_Lumpur',8.00,NULL),(253,'Asia/Kuching','Asia/Kuching',8.00,NULL),(254,'Asia/Kuwait','Asia/Kuwait',3.00,NULL),(255,'Asia/Macau','Asia/Macau',8.00,NULL),(256,'Asia/Magadan','Asia/Magadan',11.00,NULL),(257,'Asia/Makassar','Asia/Makassar',8.00,NULL),(258,'Asia/Manila','Asia/Manila',8.00,NULL),(259,'Asia/Muscat','Asia/Muscat',4.00,NULL),(260,'Asia/Nicosia','Asia/Nicosia',3.00,NULL),(261,'Asia/Novokuznetsk','Asia/Novokuznetsk',7.00,NULL),(262,'Asia/Novosibirsk','Asia/Novosibirsk',7.00,NULL),(263,'Asia/Omsk','Asia/Omsk',6.00,NULL),(264,'Asia/Oral','Asia/Oral',5.00,NULL),(265,'Asia/Phnom_Penh','Asia/Phnom_Penh',7.00,NULL),(266,'Asia/Pontianak','Asia/Pontianak',7.00,NULL),(267,'Asia/Pyongyang','Asia/Pyongyang',9.00,NULL),(268,'Asia/Qatar','Asia/Qatar',3.00,NULL),(269,'Asia/Qyzylorda','Asia/Qyzylorda',5.00,NULL),(270,'Asia/Riyadh','Asia/Riyadh',3.00,NULL),(271,'Asia/Sakhalin','Asia/Sakhalin',11.00,NULL),(272,'Asia/Samarkand','Asia/Samarkand',5.00,NULL),(273,'Asia/Seoul','Asia/Seoul',9.00,NULL),(274,'Asia/Shanghai','Asia/Shanghai',8.00,NULL),(275,'Asia/Singapore','Asia/Singapore',8.00,NULL),(276,'Asia/Srednekolymsk','Asia/Srednekolymsk',11.00,NULL),(277,'Asia/Taipei','Asia/Taipei',8.00,NULL),(278,'Asia/Tashkent','Asia/Tashkent',5.00,NULL),(279,'Asia/Tbilisi','Asia/Tbilisi',4.00,NULL),(280,'Asia/Tehran','Asia/Tehran',3.50,NULL),(281,'Asia/Thimphu','Asia/Thimphu',6.00,NULL),(282,'Asia/Tokyo','Asia/Tokyo',9.00,NULL),(283,'Asia/Tomsk','Asia/Tomsk',7.00,NULL),(284,'Asia/Ulaanbaatar','Asia/Ulaanbaatar',8.00,NULL),(285,'Asia/Urumqi','Asia/Urumqi',6.00,NULL),(286,'Asia/Ust-Nera','Asia/Ust-Nera',10.00,NULL),(287,'Asia/Vientiane','Asia/Vientiane',7.00,NULL),(288,'Asia/Vladivostok','Asia/Vladivostok',10.00,NULL),(289,'Asia/Yakutsk','Asia/Yakutsk',9.00,NULL),(290,'Asia/Yangon','Asia/Yangon',6.50,NULL),(291,'Asia/Yekaterinburg','Asia/Yekaterinburg',5.00,NULL),(292,'Asia/Yerevan','Asia/Yerevan',4.00,NULL),(293,'Atlantic/Azores','Atlantic/Azores',0.00,NULL),(294,'Atlantic/Bermuda','Atlantic/Bermuda',-3.00,NULL),(295,'Atlantic/Canary','Atlantic/Canary',1.00,NULL),(296,'Atlantic/Cape_Verde','Atlantic/Cape_Verde',-1.00,'n'),(297,'Atlantic/Faroe','Atlantic/Faroe',1.00,NULL),(298,'Atlantic/Madeira','Atlantic/Madeira',1.00,NULL),(299,'Atlantic/Reykjavik','Atlantic/Reykjavik',0.00,NULL),(300,'Atlantic/South_Georgia','Atlantic/South_Georgia',-2.00,NULL),(301,'Atlantic/Stanley','Atlantic/Stanley',-3.00,NULL),(302,'Atlantic/St_Helena','Atlantic/St_Helena',0.00,NULL),(303,'Australia/Adelaide','Australia/Adelaide',9.50,NULL),(304,'Australia/Brisbane','Australia/Brisbane',10.00,NULL),(305,'Australia/Broken_Hill','Australia/Broken_Hill',9.50,NULL),(306,'Australia/Currie','Australia/Currie',10.00,NULL),(307,'Australia/Darwin','Australia/Darwin',9.50,NULL),(308,'Australia/Eucla','Australia/Eucla',8.75,NULL),(309,'Australia/Hobart','Australia/Hobart',10.00,NULL),(310,'Australia/Lindeman','Australia/Lindeman',10.00,NULL),(311,'Australia/Lord_Howe','Australia/Lord_Howe',10.50,NULL),(312,'Australia/Melbourne','Australia/Melbourne',10.00,NULL),(313,'Australia/Perth','Australia/Perth',8.00,NULL),(314,'Australia/Sydney','Australia/Sydney',10.00,NULL),(315,'Europe/Amsterdam','Europe/Amsterdam',2.00,NULL),(316,'Europe/Andorra','Europe/Andorra',2.00,NULL),(317,'Europe/Astrakhan','Europe/Astrakhan',4.00,NULL),(318,'Europe/Athens','Europe/Athens',3.00,NULL),(319,'Europe/Belgrade','Europe/Belgrade',2.00,NULL),(320,'Europe/Berlin','Europe/Berlin',2.00,NULL),(321,'Europe/Bratislava','Europe/Bratislava',2.00,NULL),(322,'Europe/Brussels','Europe/Brussels',2.00,NULL),(323,'Europe/Bucharest','Europe/Bucharest',3.00,NULL),(324,'Europe/Budapest','Europe/Budapest',2.00,NULL),(325,'Europe/Busingen','Europe/Busingen',2.00,NULL),(326,'Europe/Chisinau','Europe/Chisinau',3.00,NULL),(327,'Europe/Copenhagen','Europe/Copenhagen',2.00,NULL),(328,'Europe/Dublin','Europe/Dublin',1.00,NULL),(329,'Europe/Gibraltar','Europe/Gibraltar',2.00,NULL),(330,'Europe/Guernsey','Europe/Guernsey',1.00,NULL),(331,'Europe/Helsinki','Europe/Helsinki',3.00,NULL),(332,'Europe/Isle_of_Man','Europe/Isle_of_Man',1.00,NULL),(333,'Europe/Istanbul','Europe/Istanbul',3.00,NULL),(334,'Europe/Jersey','Europe/Jersey',1.00,NULL),(335,'Europe/Kaliningrad','Europe/Kaliningrad',2.00,NULL),(336,'Europe/Kiev','Europe/Kiev',3.00,NULL),(337,'Europe/Kirov','Europe/Kirov',3.00,NULL),(338,'Europe/Lisbon','Europe/Lisbon',1.00,NULL),(339,'Europe/Ljubljana','Europe/Ljubljana',2.00,NULL),(340,'Europe/London','Europe/London',1.00,NULL),(341,'Europe/Luxembourg','Europe/Luxembourg',2.00,NULL),(342,'Europe/Madrid','Europe/Madrid',2.00,NULL),(343,'Europe/Malta','Europe/Malta',2.00,NULL),(344,'Europe/Mariehamn','Europe/Mariehamn',3.00,NULL),(345,'Europe/Minsk','Europe/Minsk',3.00,NULL),(346,'Europe/Monaco','Europe/Monaco',2.00,NULL),(347,'Europe/Moscow','Europe/Moscow',3.00,NULL),(348,'Europe/Oslo','Europe/Oslo',2.00,NULL),(349,'Europe/Paris','Europe/Paris',2.00,NULL),(350,'Europe/Podgorica','Europe/Podgorica',2.00,NULL),(351,'Europe/Prague','Europe/Prague',2.00,NULL),(352,'Europe/Riga','Europe/Riga',3.00,NULL),(353,'Europe/Rome','Europe/Rome',2.00,NULL),(354,'Europe/Samara','Europe/Samara',4.00,NULL),(355,'Europe/San_Marino','Europe/San_Marino',2.00,NULL),(356,'Europe/Sarajevo','Europe/Sarajevo',2.00,NULL),(357,'Europe/Saratov','Europe/Saratov',4.00,NULL),(358,'Europe/Simferopol','Europe/Simferopol',3.00,NULL),(359,'Europe/Skopje','Europe/Skopje',2.00,NULL),(360,'Europe/Sofia','Europe/Sofia',3.00,NULL),(361,'Europe/Stockholm','Europe/Stockholm',2.00,NULL),(362,'Europe/Tallinn','Europe/Tallinn',3.00,NULL),(363,'Europe/Tirane','Europe/Tirane',2.00,NULL),(364,'Europe/Ulyanovsk','Europe/Ulyanovsk',4.00,NULL),(365,'Europe/Uzhgorod','Europe/Uzhgorod',3.00,NULL),(366,'Europe/Vaduz','Europe/Vaduz',2.00,NULL),(367,'Europe/Vatican','Europe/Vatican',2.00,NULL),(368,'Europe/Vienna','Europe/Vienna',2.00,NULL),(369,'Europe/Vilnius','Europe/Vilnius',3.00,NULL),(370,'Europe/Volgograd','Europe/Volgograd',4.00,NULL),(371,'Europe/Warsaw','Europe/Warsaw',2.00,NULL),(372,'Europe/Zagreb','Europe/Zagreb',2.00,NULL),(373,'Europe/Zaporozhye','Europe/Zaporozhye',3.00,NULL),(374,'Europe/Zurich','Europe/Zurich',2.00,NULL),(375,'Indian/Antananarivo','Indian/Antananarivo',3.00,NULL),(376,'Indian/Chagos','Indian/Chagos',6.00,NULL),(377,'Indian/Christmas','Indian/Christmas',7.00,NULL),(378,'Indian/Cocos','Indian/Cocos',6.50,NULL),(379,'Indian/Comoro','Indian/Comoro',3.00,NULL),(380,'Indian/Kerguelen','Indian/Kerguelen',5.00,NULL),(381,'Indian/Mahe','Indian/Mahe',4.00,NULL),(382,'Indian/Maldives','Indian/Maldives',5.00,NULL),(383,'Indian/Mauritius','Indian/Mauritius',4.00,NULL),(384,'Indian/Mayotte','Indian/Mayotte',3.00,NULL),(385,'Indian/Reunion','Indian/Reunion',4.00,NULL),(386,'Pacific/Apia','Pacific/Apia',13.00,NULL),(387,'Pacific/Auckland','Pacific/Auckland',12.00,NULL),(388,'Pacific/Bougainville','Pacific/Bougainville',11.00,NULL),(389,'Pacific/Chatham','Pacific/Chatham',12.75,NULL),(390,'Pacific/Chuuk','Pacific/Chuuk',10.00,NULL),(391,'Pacific/Easter','Pacific/Easter',-5.00,NULL),(392,'Pacific/Efate','Pacific/Efate',11.00,NULL),(393,'Pacific/Enderbury','Pacific/Enderbury',13.00,NULL),(394,'Pacific/Fakaofo','Pacific/Fakaofo',13.00,NULL),(395,'Pacific/Fiji','Pacific/Fiji',12.00,NULL),(396,'Pacific/Funafuti','Pacific/Funafuti',12.00,NULL),(397,'Pacific/Galapagos','Pacific/Galapagos',-6.00,NULL),(398,'Pacific/Gambier','Pacific/Gambier',-9.00,NULL),(399,'Pacific/Guadalcanal','Pacific/Guadalcanal',11.00,NULL),(400,'Pacific/Guam','Pacific/Guam',10.00,NULL),(401,'Pacific/Honolulu','Pacific/Honolulu',-10.00,'w'),(402,'Pacific/Johnston','Pacific/Johnston',-10.00,NULL),(403,'Pacific/Kiritimati','Pacific/Kiritimati',14.00,NULL),(404,'Pacific/Kosrae','Pacific/Kosrae',11.00,NULL),(405,'Pacific/Kwajalein','Pacific/Kwajalein',12.00,NULL),(406,'Pacific/Majuro','Pacific/Majuro',12.00,NULL),(407,'Pacific/Marquesas','Pacific/Marquesas',-9.50,NULL),(408,'Pacific/Midway','Pacific/Midway',-11.00,'x'),(409,'Pacific/Nauru','Pacific/Nauru',12.00,NULL),(410,'Pacific/Niue','Pacific/Niue',-11.00,NULL),(411,'Pacific/Norfolk','Pacific/Norfolk',11.00,NULL),(412,'Pacific/Noumea','Pacific/Noumea',11.00,NULL),(413,'Pacific/Pago_Pago','Pacific/Pago_Pago',-11.00,NULL),(414,'Pacific/Palau','Pacific/Palau',9.00,NULL),(415,'Pacific/Pitcairn','Pacific/Pitcairn',-8.00,NULL),(416,'Pacific/Pohnpei','Pacific/Pohnpei',11.00,NULL),(417,'Pacific/Port_Moresby','Pacific/Port_Moresby',10.00,NULL),(418,'Pacific/Rarotonga','Pacific/Rarotonga',-10.00,NULL),(419,'Pacific/Saipan','Pacific/Saipan',10.00,NULL),(420,'Pacific/Tahiti','Pacific/Tahiti',-10.00,NULL),(421,'Pacific/Tarawa','Pacific/Tarawa',12.00,NULL),(422,'Pacific/Tongatapu','Pacific/Tongatapu',13.00,NULL),(423,'Pacific/Wake','Pacific/Wake',12.00,NULL),(424,'Pacific/Wallis','Pacific/Wallis',12.00,NULL);
/*!40000 ALTER TABLE `timezones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'geo_start'
--

--
-- Dumping routines for database 'geo_start'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-17 13:00:38
