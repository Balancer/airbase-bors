-- MySQL dump 10.9
--
-- Host: localhost    Database: HTS
-- ------------------------------------------------------
-- Server version	4.1.14-log
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO,MYSQL40' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `hts_keys`
--

CREATE TABLE `hts_keys` (
  `name` varchar(166) NOT NULL default '',
  `type` varchar(166) NOT NULL default '',
  `protected` tinyint(4) NOT NULL default '1',
  `id_in_value` smallint(6) NOT NULL default '0',
  `array` int(11) NOT NULL default '0',
  `unique_id` int(11) NOT NULL default '1',
  `autoinc_value` smallint(6) default NULL,
  `params` text,
  UNIQUE KEY `name` (`name`(80))
);

--
-- Dumping data for table `hts_keys`
--

INSERT INTO `hts_keys` VALUES ('nav_name','VARCHAR(255)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('source','TEXT',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('parent','INT',1,1,1,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('child','INT',1,1,1,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('forum_id','INT',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('type','VARCHAR(32)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('cr_type','VARCHAR(16)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('split_type','VARCHAR(16)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('h1','VARCHAR(255)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('h2','VARCHAR(255)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('h3','VARCHAR(255)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('title','VARCHAR(255)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('template','VARCHAR(64)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('create_time','VARCHAR(32)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('copyright','VARCHAR(255)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('style','VARCHAR(32)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('color','VARCHAR(32)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('logdir','VARCHAR(255)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('flags','VARCHAR(255)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('compile_time','INT',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('modify_time','INT',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('description','TEXT',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('author','VARCHAR(166)',1,0,1,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('width','INT(11)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('height','INT(11)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('size','INT(11)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('referer','INT',1,0,1,1,NULL,'count=INT,first_visit=INT,last_visit=INT');
INSERT INTO `hts_keys` VALUES ('keyword','VARCHAR(255)',1,0,1,0,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('body','TEXT',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('description_source','TEXT',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('site_store','VARCHAR(255)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('backup','INT',1,0,1,0,1,'time=INT,member_id=INT,ip=VARCHAR(255),type=VARCHAR(16),source=TEXT,title=VARCHAR(255),modify_time=INT,description_source=TEXT,version=INT,backup_time=INT');
INSERT INTO `hts_keys` VALUES ('version','INT',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('origin_uri','VARCHAR(255)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('local_path','VARCHAR(255)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('views','INT',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('views_first','INT',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('views_last','INT',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('access_level','INT',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('autolink','VARCHAR(255)',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('right_column','TEXT',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('images_upload','TEXT',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('rcolumn','TEXT',1,0,1,0,NULL,'index=INT,last_modify=INT,type=VARCHAR(16)');
INSERT INTO `hts_keys` VALUES ('cache_create_time','INT',1,0,0,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('author_name','VARCHAR(255)',1,0,1,1,NULL,NULL);
INSERT INTO `hts_keys` VALUES ('position','INT',1,0,0,1,NULL,NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

