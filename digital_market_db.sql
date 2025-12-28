-- MySQL dump 10.13  Distrib 8.0.44, for Linux (x86_64)
--
-- Host: localhost    Database: digital_market_db
-- ------------------------------------------------------
-- Server version	8.0.44-0ubuntu0.24.04.2

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
-- Table structure for table `checkouts`
--

DROP TABLE IF EXISTS `checkouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `checkouts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `user_id` int NOT NULL,
  `payment_total` decimal(10,2) NOT NULL,
  `game_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_game_id` (`game_id`),
  CONSTRAINT `fk_checkouts_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_checkouts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `checkouts`
--

LOCK TABLES `checkouts` WRITE;
/*!40000 ALTER TABLE `checkouts` DISABLE KEYS */;
INSERT INTO `checkouts` VALUES (2,'2025-12-22',5,69.31,31),(4,'2025-12-26',1,59.99,21),(5,'2025-12-26',1,59.99,21),(6,'2025-12-26',1,49.99,21),(7,'2025-12-26',1,49.99,21),(8,'2025-12-26',1,49.99,21),(9,'2025-12-26',1,49.99,21),(10,'2025-12-27',35,49.99,21),(11,'2025-12-27',5,10.00,34),(12,'2025-12-27',35,59.99,37),(13,'2025-12-27',35,19.99,22),(14,'2025-12-27',35,19.99,22),(15,'2025-12-27',35,19.99,26),(16,'2025-12-27',21,19.99,22),(17,'2025-12-27',21,19.99,24),(18,'2025-12-27',35,19.99,22),(20,'2025-12-28',35,49.99,32),(21,'2025-12-28',35,49.99,21),(23,'2025-12-28',21,59.99,27),(24,'2025-12-28',21,69.99,31),(25,'2025-12-28',21,19.99,23),(26,'2025-12-28',21,59.99,25),(29,'2025-12-28',32,65.99,33),(30,'2025-12-28',32,21.99,24);
/*!40000 ALTER TABLE `checkouts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `game_media`
--

DROP TABLE IF EXISTS `game_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `game_media` (
  `id` int NOT NULL AUTO_INCREMENT,
  `game_id` int NOT NULL,
  `media_type` enum('image','video') NOT NULL,
  `file_path` varchar(255) NOT NULL DEFAULT '.',
  `file_name` varchar(255) NOT NULL,
  `display_order` int NOT NULL DEFAULT '0',
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_game_id` (`game_id`),
  KEY `idx_media_type` (`media_type`),
  KEY `idx_display_order` (`display_order`),
  CONSTRAINT `fk_game_media_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `game_media`
--

LOCK TABLES `game_media` WRITE;
/*!40000 ALTER TABLE `game_media` DISABLE KEYS */;
INSERT INTO `game_media` VALUES (35,21,'image','https://cdn2.steamgriddb.com/grid/86f77fb5fa5b26fbcb103e7de206d2f4.png','image_21_banner',0,'2025-12-24 15:18:30'),(36,22,'image','https://cdn2.steamgriddb.com/grid/41f3d4b31e21e50ada1e713a3291296e.jpg','image_22_banner',0,'2025-12-26 18:54:34'),(37,23,'image','https://cdn2.steamgriddb.com/grid/3703e2930fe831e52676c8ed9892169d.png','image_23_banner',0,'2025-12-26 19:10:37'),(38,24,'image','https://cdn2.steamgriddb.com/grid/a88683c2252becfbb7cc738556f86916.png','image_24_banner',0,'2025-12-26 19:10:37'),(39,25,'image','https://cdn2.steamgriddb.com/grid/936d98d7623eee675d33fae754cb399c.jpg','image_25_banner',0,'2025-12-26 19:10:37'),(40,26,'image','https://cdn2.steamgriddb.com/grid/8419b83db387680764850f9b294a1e23.png','image_26_banner',0,'2025-12-26 19:10:37'),(41,27,'image','https://cdn2.steamgriddb.com/grid/a779abf92aeec12331d10524426171fb.png','image_27_banner',0,'2025-12-26 19:10:37'),(42,28,'image','https://cdn2.steamgriddb.com/grid/c0858d31da09dfc8bfef689f7f033d74.png','image_28_banner',0,'2025-12-26 19:10:37'),(43,29,'image','https://cdn2.steamgriddb.com/grid/00c851d26bf84acd80c492e68bb73c71.jpg','image_29_banner',0,'2025-12-26 19:10:37'),(44,30,'image','https://cdn2.steamgriddb.com/grid/84f4509e7cb65029910554f8cde96ac0.png','image_30_banner',0,'2025-12-26 19:10:37'),(45,31,'image','https://cdn2.steamgriddb.com/grid/409d51434ad8f430ae641b16b02bd07d.png','image_31_banner',0,'2025-12-26 19:10:37'),(46,32,'image','https://cdn2.steamgriddb.com/grid/ef62225f5dfdfa202cdf5c2ebd3e8d0e.png','image_32_banner',0,'2025-12-26 19:10:37'),(47,33,'image','https://cdn2.steamgriddb.com/grid/90416fb17337001d532f7ac51f1df40f.png','image_33_banner',0,'2025-12-26 19:10:37'),(48,34,'image','https://cdn2.steamgriddb.com/grid/600e6edf82ef08b9740d13bbee7b4b57.png','image_34_banner',0,'2025-12-26 19:10:37'),(49,35,'image','https://cdn2.steamgriddb.com/grid/6beef349003c2fd85d8785d8088f8aff.png','image_35_banner',0,'2025-12-26 19:10:37'),(50,36,'image','https://cdn2.steamgriddb.com/grid/09a605b34f5654f035ca0bab1a545622.png','image_36_banner',0,'2025-12-26 19:10:37'),(51,37,'image','https://cdn2.steamgriddb.com/grid/41b28a11da13a0384a9b75f95244e8e8.png','image_37_banner',0,'2025-12-26 19:10:37'),(52,38,'image','https://cdn2.steamgriddb.com/grid/ce8839a5ecda19985f2dc1129646bee7.png','image_38_banner',0,'2025-12-26 19:10:37'),(56,44,'image','games/44/media/image_44_69511249490758.00777537_9c047bb61714781b.jpg','image_44_69511249490758.00777537_9c047bb61714781b.jpg',0,'2025-12-28 11:19:07'),(65,53,'image','games/53/media/image_53_695190713f61a7.74909810_78abb4428a656819.jpg','image_53_695190713f61a7.74909810_78abb4428a656819.jpg',0,'2025-12-28 20:17:22');
/*!40000 ALTER TABLE `game_media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `games`
--

DROP TABLE IF EXISTS `games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `games` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` longtext NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_approved` int NOT NULL DEFAULT '0',
  `logo_path` varchar(255) DEFAULT NULL,
  `developer_id` int NOT NULL,
  `genre` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_developer_id` (`developer_id`),
  KEY `idx_genre` (`genre`),
  KEY `idx_is_approved` (`is_approved`),
  CONSTRAINT `fk_games_developer` FOREIGN KEY (`developer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `games`
--

LOCK TABLES `games` WRITE;
/*!40000 ALTER TABLE `games` DISABLE KEYS */;
INSERT INTO `games` VALUES (21,'The Witcher 3: Wild Hunt - Game of the Year Editio','The Witcher 3: Wild Hunt – Game of the Year Edition is a complete version of the game released in August 2016. It includes the base game along with all post-launch content, including the two major expansions.',49.99,1,'https://images.igdb.com/igdb/image/upload/t_cover_big/co1wz4.jpg',20,'Role-playing (RPG)','2025-12-12 20:17:41','2025-12-12 20:17:41'),(22,'The Legend of Zelda: A Link to the Past','Venture back to Hyrule and an age of magic and heroes. The predecessors of Link and Zelda face monsters on the march when a menacing magician takes over the kingdom.',19.99,1,'https://images.igdb.com/igdb/image/upload/t_cover_big/co3vzn.jpg',21,'Adventure','2025-12-12 20:17:41','2025-12-12 20:17:41'),(23,'Super Metroid','The Space Pirates, merciless agents of the evil Mother Brain, have stolen the last Metroid from a research station, and once again Mother Brain threatens the safety of the galaxy!',19.99,1,'https://images.igdb.com/igdb/image/upload/t_cover_big/co5osy.jpg',21,'Shooter','2025-12-12 20:17:41','2025-12-12 20:17:41'),(24,'Super Mario World','A 2D platformer and first entry on the SNES in the Super Mario franchise, Super Mario World follows Mario as he attempts to defeat Bowser\'s underlings and rescue Princess Peach.',19.99,1,'https://images.igdb.com/igdb/image/upload/t_cover_big/co8lo8.jpg',21,'Platform','2025-12-12 20:17:41','2025-12-12 20:17:41'),(25,'Elden Ring','Elden Ring is an action RPG developed by FromSoftware. Players assume the role of a customisable character known as the Tarnished, who must explore the Lands Between and seek to become the Elden Lord.',59.99,1,'https://images.igdb.com/igdb/image/upload/t_cover_big/co4jni.jpg',22,'Role-playing (RPG)','2025-12-12 20:17:41','2025-12-12 20:17:41'),(26,'The Last of Us Remastered','The Last of Us Remastered is an updated release of the PS3 game. It runs at 1080p resolution with higher resolution character models, improved shadows and lighting.',19.99,1,'https://images.igdb.com/igdb/image/upload/t_cover_big/co5zks.jpg',23,'Shooter','2025-12-12 20:17:41','2025-12-12 20:17:41'),(27,'Baldur\'s Gate III','An ancient evil has returned to Baldur\'s Gate, intent on devouring it from the inside out. The fate of Faerun lies in your hands. Alone, you may resist. But together, you can overcome.',59.99,1,'https://images.igdb.com/igdb/image/upload/t_cover_big/co670h.jpg',24,'Role-playing (RPG)','2025-12-12 20:17:41','2025-12-12 20:17:41'),(28,'The Legend of Zelda: Breath of the Wild','The Legend of Zelda: Breath of the Wild is the first 3D open-world game in the Zelda series. Link can travel anywhere and be equipped with weapons and armor found throughout the world.',59.99,1,'https://images.igdb.com/igdb/image/upload/t_cover_big/co3p2d.jpg',21,'Adventure','2025-12-12 20:17:41','2025-12-12 20:17:41'),(29,'Final Fantasy III','Final Fantasy III is the sixth main installment in the Final Fantasy series. It was the final title to feature two-dimensional graphics and the first story that did not revolve around crystals.',14.99,1,'https://images.igdb.com/igdb/image/upload/t_cover_big/coaq5k.jpg',25,'Role-playing (RPG)','2025-12-12 20:17:41','2025-12-12 20:17:41'),(30,'Super Smash Bros. Melee','Super Smash Bros. Melee includes all playable characters from the first game and adds characters from franchises such as Fire Emblem. Its major focus is the multiplayer mode.',59.99,1,'https://images.igdb.com/igdb/image/upload/t_cover_big/co21yv.jpg',26,'Fighting','2025-12-12 20:17:41','2025-12-12 20:17:41'),(31,'The Legend of Zelda: Tears of the Kingdom','The Legend of Zelda: Tears of the Kingdom is the sequel to Breath of the Wild. The setting for Link\'s adventure has been expanded to include the skies above the vast lands of Hyrule.',69.99,1,'https://images.igdb.com/igdb/image/upload/t_cover_big/co5vmg.jpg',21,'Adventure','2025-12-12 20:17:41','2025-12-12 20:17:41'),(32,'God of War','This game focuses on Norse mythology and follows an older and more seasoned Kratos and his son Atreus in the years since the third game.',49.99,1,'https://images.igdb.com/igdb/image/upload/t_cover_big/co1tmu.jpg',27,'Role-playing (RPG)','2025-12-12 20:17:41','2025-12-12 20:17:41'),(33,'Persona 5 Royal','An enhanced version of Persona 5 with some new characters and a third semester added to the game. Released Internationally in 2020.',59.99,1,'https://images.igdb.com/igdb/image/upload/t_cover_big/coateg.jpg',28,'Role-playing (RPG)','2025-12-12 20:17:41','2025-12-12 20:17:41'),(34,'Super Mario Galaxy','A 3D platformer where Mario jumps across planets and galaxies with varying items, enemies, geographies and gravity mechanics in order to reach his enemy Bowser.',29.99,1,'https://images.igdb.com/igdb/image/upload/t_cover_big/co21ro.jpg',21,'Platform','2025-12-12 20:17:41','2025-12-12 20:17:41'),(35,'Super Mario World 2: Yoshi\'s Island','The game casts players as Yoshi as he escorts Baby Mario through 48 levels in order to reunite him with his brother Luigi. It features a hand-drawn aesthetic.',19.99,1,'https://images.igdb.com/igdb/image/upload/t_cover_big/co2kn9.jpg',21,'Platform','2025-12-12 20:17:41','2025-12-12 20:17:41'),(36,'Metroid Prime','A 3D exploration-focused metroidvania. Samus Aran boards a Space Pirate frigate, then chases her escaping archrival Ridley into the intricately structured Tallon IV.',39.99,1,'https://images.igdb.com/igdb/image/upload/t_cover_big/co3w4w.jpg',29,'Shooter','2025-12-12 20:17:41','2025-12-12 20:17:41'),(37,'Red Dead Redemption 2','Red Dead Redemption 2 is the epic tale of outlaw Arthur Morgan and the infamous Van der Linde gang, on the run across America at the dawn of the modern age.',59.99,1,'https://images.igdb.com/igdb/image/upload/t_cover_big/co1q1f.jpg',30,'Shooter','2025-12-12 20:17:41','2025-12-12 20:17:41'),(38,'Mass Effect 2','It is time to bring together your greatest allies and recruit the galaxy\'s fighting elite to continue the resistance against the invading Reapers.',19.99,1,'https://images.igdb.com/igdb/image/upload/t_cover_big/co20ac.jpg',31,'Shooter','2025-12-12 20:17:41','2025-12-12 20:17:41'),(44,'NOARTIOKYESS','CYBERPUNK',35.00,-1,NULL,20,'Shooter','2025-12-28 11:19:06','2025-12-28 20:17:00'),(53,'MY GAME','MY GAME',2.00,-1,NULL,20,'RPG','2025-12-28 20:17:19','2025-12-28 20:17:52');
/*!40000 ALTER TABLE `games` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Store hashed passwords',
  `gender` char(10) NOT NULL,
  `type` enum('admin','game_developer','user','guest') NOT NULL DEFAULT 'user',
  `registered_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `birth_date` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_username` (`username`),
  UNIQUE KEY `unique_email` (`email`),
  UNIQUE KEY `unique_phone` (`phone`),
  KEY `idx_type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin_john','admin@gamestore.com','+90 555 111 2233','$2a$10$aGcRvPZ7H.0D4wFQOKdLsuYhCxA0/DFA/nhic8OawFwH7mJgGe4XO','male','admin','2024-01-15 10:00:00',1,'1985-03-20'),(4,'dev_emily','emily@dreamforge.com','+90 555 444 5566','$2a$10$aGcRvPZ7H.0D4wFQOKdLsuYhCxA0/DFA/nhic8OawFwH7mJgGe4XO','female','game_developer','2024-03-20 16:45:00',0,'1992-05-22'),(5,'gamer_alex','alex.gaming@email.com','+90 555 555 6677','$2a$10$aGcRvPZ7H.0D4wFQOKdLsuYhCxA0/DFA/nhic8OawFwH7mJgGe4XO','male','user','2024-04-01 11:20:00',1,'1995-09-10'),(6,'player_jessica','jessica.plays@email.com','+90 555 666 7788','$2a$10$aGcRvPZ7H.0D4wFQOKdLsuYhCxA0/DFA/nhic8OawFwH7mJgGe4XO','female','user','2024-05-15 13:30:00',1,'1998-12-05'),(7,'casual_tom','tom.casual@email.com','+90 555 777 8899','$2a$10$aGcRvPZ7H.0D4wFQOKdLsuYhCxA0/DFA/nhic8OawFwH7mJgGe4XO','male','user','2024-06-20 15:00:00',1,'2000-03-18'),(8,'hardcore_lisa','lisa.hardcore@email.com','+90 555 888 9900','$2a$10$aGcRvPZ7H.0D4wFQOKdLsuYhCxA0/DFA/nhic8OawFwH7mJgGe4XO','female','user','2024-07-10 10:45:00',1,'1997-08-25'),(9,'guest_visitor1','guest1@temp.com','+90 555 999 0011','$2a$10$aGcRvPZ7H.0D4wFQOKdLsuYhCxA0/DFA/nhic8OawFwH7mJgGe4XO','male','guest','2024-11-25 18:20:00',0,'2001-06-14'),(10,'guest_visitor2','guest2@temp.com','+90 555 000 1122','$2a$10$aGcRvPZ7H.0D4wFQOKdLsuYhCxA0/DFA/nhic8OawFwH7mJgGe4XO','female','guest','2024-11-28 20:15:00',0,'1999-02-28'),(20,'CD Projekt RED','contact@cdprojekt.com','+48 22 519 69 00','$2a$10$aGcRvPZ7H.0D4wFQOKdLsuYhCxA0/DFA/nhic8OawFwH7mJgGe4XO','other','game_developer','2025-12-12 20:17:01',1,'2002-02-01'),(21,'Nintendo','contact@nintendo.com','+81 75 662 9600','$2a$10$aGcRvPZ7H.0D4wFQOKdLsuYhCxA0/DFA/nhic8OawFwH7mJgGe4XO','other','game_developer','2025-12-12 20:17:01',1,'1889-09-23'),(22,'FromSoftware','contact@fromsoftware.jp','+81 3 3320 6000','$2a$10$aGcRvPZ7H.0D4wFQOKdLsuYhCxA0/DFA/nhic8OawFwH7mJgGe4XO','other','game_developer','2025-12-12 20:17:01',1,'1986-11-01'),(23,'Naughty Dog','contact@naughtydog.com','+1 310 631 8500','$2a$10$aGcRvPZ7H.0D4wFQOKdLsuYhCxA0/DFA/nhic8OawFwH7mJgGe4XO','other','game_developer','2025-12-12 20:17:01',1,'1984-09-27'),(24,'Larian Studios','contact@larian.com','+32 9 243 76 60','$2a$10$aGcRvPZ7H.0D4wFQOKdLsuYhCxA0/DFA/nhic8OawFwH7mJgGe4XO','other','game_developer','2025-12-12 20:17:01',1,'1996-01-01'),(25,'Square Enix','contact@square-enix.com','+81 3 5292 8000','$2a$10$aGcRvPZ7H.0D4wFQOKdLsuYhCxA0/DFA/nhic8OawFwH7mJgGe4XO','other','game_developer','2025-12-12 20:17:01',1,'1975-09-22'),(26,'HAL Laboratory','contact@hallab.co.jp','+81 3 1111 2222','$2a$10$aGcRvPZ7H.0D4wFQOKdLsuYhCxA0/DFA/nhic8OawFwH7mJgGe4XO','other','game_developer','2025-12-12 20:17:01',1,'1980-02-21'),(27,'Santa Monica Studio','contact@sms.playstation.com','+1 310 309 3700','$2a$10$aGcRvPZ7H.0D4wFQOKdLsuYhCxA0/DFA/nhic8OawFwH7mJgGe4XO','other','game_developer','2025-12-12 20:17:01',1,'1999-01-01'),(28,'Atlus','contact@atlus.com','+1 949 788 0455','$2a$10$aGcRvPZ7H.0D4wFQOKdLsuYhCxA0/DFA/nhic8OawFwH7mJgGe4XO','other','game_developer','2025-12-12 20:17:01',1,'1986-04-07'),(29,'Retro Studios','contact@retrostudios.com','+1 512 111 2222','$2a$10$aGcRvPZ7H.0D4wFQOKdLsuYhCxA0/DFA/nhic8OawFwH7mJgGe4XO','other','game_developer','2025-12-12 20:17:01',1,'1998-09-21'),(30,'Rockstar Games','contact@rockstargames.com','+1 212 334 6633','$2a$10$aGcRvPZ7H.0D4wFQOKdLsuYhCxA0/DFA/nhic8OawFwH7mJgGe4XO','other','game_developer','2025-12-12 20:17:01',1,'1998-12-01'),(31,'BioWare','contact@bioware.com','+1 780 430 0430','$2a$10$aGcRvPZ7H.0D4wFQOKdLsuYhCxA0/DFA/nhic8OawFwH7mJgGe4XO','other','game_developer','2025-12-12 20:17:01',1,'1995-02-01'),(32,'Oktay','abc@gmail.com','351253','$2y$10$f1fCIqYEc7n9BvRlfjV7R.N5VCt6VJH1XH6FjCToKU.tA4Jeb.KNS','male','user','2025-12-27 15:21:07',0,'2023-08-04'),(35,'Oktay2','abcd@gmail.com','734437347','$2y$10$f1fCIqYEc7n9BvRlfjV7R.N5VCt6VJH1XH6FjCToKU.tA4Jeb.KNS','male','user','2025-12-27 15:23:12',0,'2023-08-04'),(36,'sezer','sezer@asfasf','1205125','$2y$10$lm2CgVc2KcSHoVEwboVEF.yZO0MpJ0zpMJzcROMVL7WWZ7HWVphL2','male','game_developer','2025-12-27 20:11:57',1,'2025-12-11');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'digital_market_db'
--

--
-- Dumping routines for database 'digital_market_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-28 20:33:00
