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
-- Table structure for table `hts_aliases`
--

CREATE TABLE `hts_aliases` (
  `alias` varchar(255) NOT NULL default '',
  `uri` varchar(255) NOT NULL default '',
  UNIQUE KEY `alias` (`alias`(80))
);

--
-- Table structure for table `hts_data_access_level`
--

CREATE TABLE `hts_data_access_level` (
  `id` varchar(255) default NULL,
  `value` int(11) NOT NULL default '0',
  UNIQUE KEY `pair` (`id`,`value`),
  KEY `id` (`id`),
  KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_author`
--

CREATE TABLE `hts_data_author` (
  `id` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`(166),`value`(166))
);

--
-- Table structure for table `hts_data_author_names`
--

CREATE TABLE `hts_data_author_name` (
  `id` varchar(255) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`(166),`value`(166))
);

--
-- Table structure for table `hts_data_autolink`
--

CREATE TABLE `hts_data_autolink` (
  `id` varchar(255) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `pairs` (`id`(166),`value`(166)),
  FULLTEXT KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_backup`
--

CREATE TABLE `hts_data_backup` (
  `id` varchar(255) default NULL,
  `value` int(11) NOT NULL auto_increment,
  `time` int(11) NOT NULL default '0',
  `member_id` int(11) NOT NULL default '0',
  `ip` varchar(255) NOT NULL default '',
  `type` varchar(16) NOT NULL default '',
  `source` longtext NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `modify_time` int(11) NOT NULL default '0',
  `description_source` text NOT NULL,
  `version` int(11) NOT NULL default '0',
  `backup_time` int(11) NOT NULL default '0',
  UNIQUE KEY `pair` (`id`,`value`,`time`,`member_id`,`modify_time`,`version`,`backup_time`),
  UNIQUE KEY `versions` (`id`,`version`),
  KEY `id` (`id`),
  KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_body`
--

CREATE TABLE `hts_data_body` (
  `id` varchar(255) NOT NULL default '',
  `value` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
);

--
-- Table structure for table `hts_data_cache_create_time`
--

CREATE TABLE `hts_data_cache_create_time` (
  `id` varchar(255) NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_cache_create_times`
--

CREATE TABLE `hts_data_cache_create_times` (
  `id` varchar(255) NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_child`
--

CREATE TABLE `hts_data_child` (
  `id` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '0',
  PRIMARY KEY  (`id`(166),`value`(166)),
  KEY `id` (`id`),
  KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_color`
--

CREATE TABLE `hts_data_color` (
  `id` varchar(255) default NULL,
  `value` varchar(32) NOT NULL default ''
);

--
-- Table structure for table `hts_data_compile_time`
--

CREATE TABLE `hts_data_compile_time` (
  `id` varchar(255) NOT NULL default '',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);

--
-- Table structure for table `hts_data_copyright`
--

CREATE TABLE `hts_data_copyright` (
  `id` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
);

--
-- Table structure for table `hts_data_cr_type`
--

CREATE TABLE `hts_data_cr_type` (
  `id` varchar(255) default NULL,
  `value` varchar(16) NOT NULL default ''
);

--
-- Table structure for table `hts_data_create_time`
--

CREATE TABLE `hts_data_create_time` (
  `id` varchar(255) NOT NULL default '',
  `value` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`)
);

--
-- Table structure for table `hts_data_description`
--

CREATE TABLE `hts_data_description` (
  `id` varchar(255) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_description_source`
--

CREATE TABLE `hts_data_description_source` (
  `id` varchar(255) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_flags`
--

CREATE TABLE `hts_data_flags` (
  `id` varchar(255) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  UNIQUE KEY `id` (`id`(166),`value`(166))
);

--
-- Table structure for table `hts_data_forum_id`
--

CREATE TABLE `hts_data_forum_id` (
  `id` varchar(255) NOT NULL default '',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);

--
-- Table structure for table `hts_data_h1s`
--

CREATE TABLE `hts_data_h1s` (
  `id` varchar(255) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_h2s`
--

CREATE TABLE `hts_data_h2s` (
  `id` varchar(255) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_h3s`
--

CREATE TABLE `hts_data_h3s` (
  `id` varchar(255) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_height`
--

CREATE TABLE `hts_data_height` (
  `id` varchar(255) NOT NULL default '',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);

--
-- Table structure for table `hts_data_images_upload`
--

CREATE TABLE `hts_data_images_upload` (
  `id` varchar(255) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id` (`id`(166),`value`(166)),
  FULLTEXT KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_keyword`
--

CREATE TABLE `hts_data_keyword` (
  `id` varchar(255) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  UNIQUE KEY `id_2` (`id`(166),`value`(166)),
  KEY `id` (`id`),
  KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_local_path`
--

CREATE TABLE `hts_data_local_path` (
  `id` varchar(255) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  UNIQUE KEY `id_2` (`id`(166),`value`(166)),
  KEY `id` (`id`),
  FULLTEXT KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_logdir`
--

CREATE TABLE `hts_data_logdir` (
  `id` varchar(255) default NULL,
  `value` varchar(255) NOT NULL default ''
);

--
-- Table structure for table `hts_data_modify_time`
--

CREATE TABLE `hts_data_modify_time` (
  `id` varchar(255) NOT NULL default '',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`,`value`),
  KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_nav_name`
--

CREATE TABLE `hts_data_nav_name` (
  `id` varchar(255) default NULL,
  `value` varchar(255) NOT NULL default '',
  KEY `id` (`id`),
  FULLTEXT KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_origin_uri`
--

CREATE TABLE `hts_data_origin_uri` (
  `id` varchar(255) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  UNIQUE KEY `id_2` (`id`(166),`value`(166)),
  KEY `id` (`id`),
  FULLTEXT KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_parent`
--

CREATE TABLE `hts_data_parent` (
  `id` varchar(255) default NULL,
  `value` varchar(255) NOT NULL default '0',
  UNIQUE KEY `pairs` (`id`(166),`value`(166)),
  KEY `id` (`id`),
  KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_positions`
--

CREATE TABLE `hts_data_positions` (
  `id` varchar(255) NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `pair` (`id`,`value`),
  KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_rcolumn`
--

CREATE TABLE `hts_data_rcolumn` (
  `id` varchar(255) NOT NULL default '0',
  `value` text NOT NULL,
  `index` int(11) NOT NULL default '0',
  `last_modify` int(11) NOT NULL default '0',
  `type` varchar(16) NOT NULL default '',
  UNIQUE KEY `id_2` (`id`(160),`value`(166),`index`,`last_modify`),
  KEY `id` (`id`),
  FULLTEXT KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_referer`
--

CREATE TABLE `hts_data_referer` (
  `id` varchar(255) default NULL,
  `value` varchar(255) NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `first_visit` int(11) NOT NULL default '0',
  `last_visit` int(11) NOT NULL default '0',
  UNIQUE KEY `id_2` (`id`(166),`value`(166)),
  KEY `id` (`id`),
  KEY `value` (`value`),
  KEY `count` (`count`,`first_visit`,`last_visit`)
);

--
-- Table structure for table `hts_data_right_column`
--

CREATE TABLE `hts_data_right_column` (
  `id` varchar(255) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_site_store`
--

CREATE TABLE `hts_data_site_store` (
  `id` varchar(255) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  UNIQUE KEY `id_2` (`id`(166),`value`(166)),
  KEY `id` (`id`),
  FULLTEXT KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_size`
--

CREATE TABLE `hts_data_size` (
  `id` varchar(255) NOT NULL default '',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);

--
-- Table structure for table `hts_data_source`
--

CREATE TABLE `hts_data_source` (
  `id` varchar(255) NOT NULL default '',
  `value` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
);

--
-- Table structure for table `hts_data_split_type`
--

CREATE TABLE `hts_data_split_type` (
  `id` varchar(255) default NULL,
  `value` varchar(16) NOT NULL default ''
);

--
-- Table structure for table `hts_data_style`
--

CREATE TABLE `hts_data_style` (
  `id` varchar(255) default NULL,
  `value` varchar(32) NOT NULL default ''
);

--
-- Table structure for table `hts_data_template`
--

CREATE TABLE `hts_data_template` (
  `id` varchar(255) NOT NULL default '',
  `value` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`)
);

--
-- Table structure for table `hts_data_title`
--

CREATE TABLE `hts_data_title` (
  `id` varchar(255) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_type`
--

CREATE TABLE `hts_data_type` (
  `id` varchar(255) NOT NULL default '',
  `value` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`id`)
);

--
-- Table structure for table `hts_data_version`
--

CREATE TABLE `hts_data_version` (
  `id` varchar(255) default NULL,
  `value` int(11) NOT NULL default '0',
  UNIQUE KEY `pair` (`id`,`value`),
  KEY `id` (`id`),
  KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_views`
--

CREATE TABLE `hts_data_views` (
  `id` varchar(255) default NULL,
  `value` int(11) NOT NULL default '0',
  KEY `id` (`id`),
  KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_views_first`
--

CREATE TABLE `hts_data_views_first` (
  `id` varchar(255) default NULL,
  `value` int(11) NOT NULL default '0',
  UNIQUE KEY `pair` (`id`,`value`),
  KEY `id` (`id`),
  KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_views_last`
--

CREATE TABLE `hts_data_views_last` (
  `id` varchar(255) default NULL,
  `value` int(11) NOT NULL default '0',
  UNIQUE KEY `pair` (`id`,`value`),
  KEY `id` (`id`),
  KEY `value` (`value`)
);

--
-- Table structure for table `hts_data_width`
--

CREATE TABLE `hts_data_width` (
  `id` varchar(255) NOT NULL default '',
  `value` int(11) default NULL,
  PRIMARY KEY  (`id`)
);

--
-- Table structure for table `hts_ext_log`
--

CREATE TABLE `hts_ext_log` (
  `id` int(11) NOT NULL auto_increment,
  `time` int(11) NOT NULL default '0',
  `pid` varchar(255) NOT NULL default '0',
  `version` int(11) NOT NULL default '0',
  `member_id` int(11) NOT NULL default '0',
  `user` varchar(255) NOT NULL default '',
  `action` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`id`)
);

--
-- Table structure for table `hts_ext_referers`
--

CREATE TABLE `hts_ext_referers` (
  `id` varchar(255) NOT NULL default '0',
  `referer` varchar(255) NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `first_enter` int(11) NOT NULL default '0',
  `last_enter` int(11) NOT NULL default '0',
  UNIQUE KEY `id_2` (`id`(166),`referer`(166)),
  KEY `id` (`id`)
);

--
-- Table structure for table `hts_ext_system_data`
--

CREATE TABLE `hts_ext_system_data` (
  `key` varchar(32) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  `type` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`key`),
  KEY `key` (`key`,`value`)
);

--
-- Table structure for table `hts_host_redirect`
--

CREATE TABLE `hts_host_redirect` (
  `from` varchar(255) NOT NULL default '',
  `to` varchar(255) NOT NULL default '',
  UNIQUE KEY `from` (`from`(80))
);

--
-- Table structure for table `hts_hosts`
--

CREATE TABLE `hts_hosts` (
  `host` varchar(255) NOT NULL default '',
  `doc_root` varchar(255) NOT NULL default '',
  `default_access_level` int(11) NOT NULL default '3',
  UNIQUE KEY `host` (`host`),
  KEY `doc_root` (`doc_root`)
);

--
-- Table structure for table `hts_id`
--

CREATE TABLE `hts_id` (
  `id` int(11) NOT NULL auto_increment,
  `uri` varchar(255) NOT NULL default '',
  `tmp` int(11) NOT NULL default '0',
  UNIQUE KEY `id_2` (`id`,`uri`),
  UNIQUE KEY `uri` (`uri`),
  KEY `id` (`id`),
  KEY `url` (`uri`(80)),
  KEY `tmp` (`tmp`)
);


--
-- Table structure for table `hts_save`
--

CREATE TABLE `hts_save` (
  `id` varchar(255) default NULL,
  `value` int(11) NOT NULL default '0'
);

--
-- Table structure for table `hts_save2`
--

CREATE TABLE `hts_save2` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default ''
);

--
-- Table structure for table `hts_save3`
--

CREATE TABLE `hts_save3` (
  `id` int(11) NOT NULL default '0',
  `value` text NOT NULL
);

--
-- Table structure for table `sources`
--

CREATE TABLE `sources` (
  `id` int(11) NOT NULL default '0',
  `value` text NOT NULL,
  FULLTEXT KEY `value` (`value`)
);

--
-- Table structure for table `tables_data`
--

CREATE TABLE `tables_data` (
  `id` int(11) NOT NULL auto_increment,
  `object` varchar(166) NOT NULL default '',
  `field` varchar(166) NOT NULL default '',
  `value` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `object` (`object`(80)),
  KEY `field` (`field`(80))
);

--
-- Table structure for table `test`
--

CREATE TABLE `test` (
  `id` int(11) NOT NULL default '0',
  `val` varchar(255) NOT NULL default '',
  KEY `val` (`val`)
);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

