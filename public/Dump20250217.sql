-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: stoom
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `achievement`
--

DROP TABLE IF EXISTS `achievement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `achievement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `date` date NOT NULL COMMENT '(DC2Type:date_immutable)',
  `game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_96737FF1E48FD905` (`game_id`),
  KEY `IDX_96737FF1A76ED395` (`user_id`),
  CONSTRAINT `FK_96737FF1A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_96737FF1E48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `achievement`
--

LOCK TABLES `achievement` WRITE;
/*!40000 ALTER TABLE `achievement` DISABLE KEYS */;
INSERT INTO `achievement` VALUES (1,'silver','http://localhost/stoom/games/quizgame/images/medaillezilver.png','2025-02-09',1,3),(2,'bronze','http://localhost/stoom/games/quizgame/images/medaillebrons.png','2025-02-09',1,3),(3,'gold','http://localhost/stoom/games/quizgame/images/medaillegoud.png','2025-02-09',5,3),(4,'platinum','http://localhost/stoom/games/quizgame/images/medailleplatinum.png','2025-02-09',5,3),(5,'silver','http://localhost/stoom/games/quizgame/images/medaillezilver.png','2025-02-11',5,1),(6,'bronze','http://localhost/stoom/games/quizgame/images/medaillebrons.png','2025-02-11',5,1),(7,'platinum','http://localhost/stoom/games/quizgame/images/medailleplatinum.png','2025-02-12',5,1),(8,'gold','http://localhost/stoom/games/quizgame/images/medaillegoud.png','2025-02-12',5,1),(9,'bronze','http://localhost/stoom/games/quizgame/images/medaillebrons.png','2025-02-12',5,5),(10,'bronze','http://localhost/stoom/games/quizgame/images/medaillebrons.png','2025-02-14',5,4),(11,'silver','http://localhost/stoom/games/quizgame/images/medaillezilver.png','2025-02-14',5,4);
/*!40000 ALTER TABLE `achievement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctrine_migration_versions`
--

LOCK TABLES `doctrine_migration_versions` WRITE;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20250125144019','2025-01-30 20:38:07',32),('DoctrineMigrations\\Version20250128151020','2025-01-30 20:38:07',9),('DoctrineMigrations\\Version20250129151743','2025-01-30 20:38:07',8),('DoctrineMigrations\\Version20250131154442','2025-01-31 16:45:12',142),('DoctrineMigrations\\Version20250203132051','2025-02-03 14:21:58',24),('DoctrineMigrations\\Version20250208180519','2025-02-08 19:06:09',171),('DoctrineMigrations\\Version20250209135318','2025-02-09 14:53:42',316),('DoctrineMigrations\\Version20250211150014','2025-02-11 16:00:26',25),('DoctrineMigrations\\Version20250213155511','2025-02-13 16:55:29',11);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `friendship`
--

DROP TABLE IF EXISTS `friendship`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `friendship` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `friendship`
--

LOCK TABLES `friendship` WRITE;
/*!40000 ALTER TABLE `friendship` DISABLE KEYS */;
INSERT INTO `friendship` VALUES (1,1,3),(2,3,1),(3,1,2),(4,2,1),(5,2,4),(6,4,2),(7,4,1),(8,1,4),(9,7,1),(10,1,7);
/*!40000 ALTER TABLE `friendship` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `game`
--

DROP TABLE IF EXISTS `game`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `game` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `is_public` tinyint(1) NOT NULL,
  `owner` int(11) NOT NULL,
  `api_key` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `game`
--

LOCK TABLES `game` WRITE;
/*!40000 ALTER TABLE `game` DISABLE KEYS */;
INSERT INTO `game` VALUES (1,'GTA V','uploads/Afbeelding van WhatsApp op 2025-01-28 om 17.07.35_ea0e6c58.jpg','https://www.rockstargames.com/',1,2,'21ki4715523m51.78945121'),(2,'Subnautica','uploads/Afbeelding van WhatsApp op 2025-01-28 om 16.42.24_38867a8c.jpg','https://subnautica.com/en',0,1,'78ml4562187p54.78894512'),(3,'The Witcher 3','uploads/the_witcher_3_wild_hunt_4-wallpaper-3554x1999.jpg','https://www.cdprojektred.com/en',1,1,'15fr5841182y98.56415881'),(5,'QuizGame','uploads/22169932-3d-quiz-goud-titel-voor-wedstrijd-show-goud-doopvont-voor-trivia-typografie-ontwerp-sjabloon-aan-zwart-tegel-achtergrond-lettertype-in-licht-schitteren-kader-creatief-retro-achtergrond-voor-wedstrijd-vector.jpg','http://localhost/Stoom/Games/QuizGame/index.html',1,1,'67ab69411057a9.53737268'),(10,'test','uploads/board-361516_1280.webp','fd',0,3,'67b0df8deeabf0.14245915');
/*!40000 ALTER TABLE `game` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `content` varchar(1000) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `is_read` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B6BD307FF624B39D` (`sender_id`),
  KEY `IDX_B6BD307FCD53EDB6` (`receiver_id`),
  CONSTRAINT `FK_B6BD307FCD53EDB6` FOREIGN KEY (`receiver_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_B6BD307FF624B39D` FOREIGN KEY (`sender_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message`
--

LOCK TABLES `message` WRITE;
/*!40000 ALTER TABLE `message` DISABLE KEYS */;
INSERT INTO `message` VALUES (1,1,2,'kaka','2025-01-31 17:20:36',1),(2,1,3,'hallooooo','2025-01-31 19:57:51',1),(3,1,3,'hey','2025-01-31 20:06:56',1),(4,1,2,'hallo','2025-02-05 15:58:26',1),(5,2,1,'hallo ik ben test','2025-02-06 15:10:56',1),(6,2,1,'admiiiiiin','2025-02-06 17:08:47',1),(7,2,1,'halloooo','2025-02-06 17:08:53',1),(8,1,3,'hallo','2025-02-07 16:33:41',1),(9,1,3,'ik ben het','2025-02-07 16:33:49',1),(10,3,1,'hey hoi','2025-02-07 16:35:33',1),(11,4,2,'Friend request sent','2025-02-07 17:37:13',1),(12,2,4,'Friend request accepted','2025-02-07 17:37:47',0),(14,1,3,'keekekekekek','2025-02-10 20:05:20',1),(15,1,4,'Friend request sent','2025-02-13 16:20:50',1),(16,4,1,'Friend request accepted','2025-02-13 20:44:18',1),(17,1,2,'hallo','2025-02-15 16:46:21',0),(18,1,7,'Friend request sent','2025-02-15 17:26:31',1),(19,7,1,'Friend request accepted','2025-02-15 17:27:00',1),(20,1,7,'hallo','2025-02-16 17:03:03',0);
/*!40000 ALTER TABLE `message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messenger_messages`
--

LOCK TABLES `messenger_messages` WRITE;
/*!40000 ALTER TABLE `messenger_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messenger_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `score`
--

DROP TABLE IF EXISTS `score`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `score` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `score` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `date` date NOT NULL COMMENT '(DC2Type:date_immutable)',
  `game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_32993751E48FD905` (`game_id`),
  KEY `IDX_32993751A76ED395` (`user_id`),
  CONSTRAINT `FK_32993751A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_32993751E48FD905` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `score`
--

LOCK TABLES `score` WRITE;
/*!40000 ALTER TABLE `score` DISABLE KEYS */;
INSERT INTO `score` VALUES (1,123,120,'2025-02-09',1,3),(2,500,300,'2025-02-09',1,3),(3,123,120,'2025-02-09',5,1),(4,150,225,'2025-02-09',5,1),(5,2500,165,'2025-02-12',5,1),(6,790,93,'2025-02-13',5,3),(7,370,344,'2025-02-14',5,4),(8,300,71,'2025-02-16',5,1);
/*!40000 ALTER TABLE `score` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `birthday` date DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
  `profilepicture` varchar(255) DEFAULT NULL,
  `isadmin` tinyint(1) NOT NULL,
  `is_blocked` tinyint(1) NOT NULL,
  `is_muted` tinyint(1) NOT NULL,
  `is_restricted_from_friend_requests` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin@admin','[\"ROLE_ADMIN\"]','$2y$13$679pRe.cT53A04Aft/1Yz.2Pfk3tR5SHMsEOxhDRqxP/VIy13Q4o2','admin','female','1951-10-19','uploads/admin.jpg',0,0,0,0),(2,'test@test','[\"ROLE_USER\"]','$2y$13$c8rlNvkHswQDw73ruATMkucP6HiiNkYeZ/TYsZuSBeJ.SaVYWQDaW','test',NULL,NULL,'uploads/Dummy_User.jpg',0,0,0,0),(3,'kenny@kenny','[\"ROLE_USER\"]','$2y$13$CQoEvEzyKhGZyQNFQCHYxOUa5nyCi/RX9yqu40fBj1tEYAw/3ASie','Kenny',NULL,NULL,'uploads/images.jpg',0,0,1,1),(4,'walter@walter','[\"ROLE_USER\"]','$2y$13$V3RMh7Su/B571Fal9ZFZVOYfx9N/VIMqWMZBhrI.s8HxgnvT1RPa.','Walter',NULL,NULL,'uploads/51oRwIOTcIL._AC_UF894,1000_QL80_.jpg',0,0,0,0),(5,'kevin@kevin','[\"ROLE_USER\"]','$2y$13$YUna9wS2ywVQrm/7KX6DL.wper4TC9qBkWTNG83ml/fza7MyqnLwS','Den Kevin',NULL,NULL,'uploads/Dummy_User.jpg',0,0,1,1),(6,'hallo@hallo.com','[\"ROLE_USER\"]','$2y$13$L58/WKsJ2oQB/8lMQqDgpO/fTb14Yb3M.ZSfU7a3tXeqvxYqyGuui','123456',NULL,NULL,'uploads/Dummy_User.jpg',0,0,0,0),(7,'kanzi@kanzi.com','[\"ROLE_USER\"]','$2y$13$FFbRPCTWY4oECGLqh3h7DOtltMxLtPCpiaRpdFLy7ruY1ULaPYFxW','Kanzi',NULL,NULL,'uploads/Kanzi_in_the_indoor_test_apparatus.jpg',0,0,0,0);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-17 14:19:38
