-- MySQL dump 10.9
--
-- Host: localhost    Database: CACHE
-- ------------------------------------------------------
-- Server version	4.1.14-log
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO,MYSQL40' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `type` varchar(255) default NULL,
  `key` varchar(255) default NULL,
  `hmd` varchar(32) NOT NULL default '',
  `value` text NOT NULL,
  `access_time` int(11) NOT NULL default '0',
  `create_time` int(11) NOT NULL default '0',
  `expire_time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`hmd`),
  KEY `type` (`type`),
  KEY `key` (`key`),
  KEY `access_time` (`access_time`),
  KEY `create_time` (`create_time`),
  KEY `expire_time` (`expire_time`)
);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

