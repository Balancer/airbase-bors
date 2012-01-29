CREATE TABLE IF NOT EXISTS `web_files_cache` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `file` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `file_original` varchar(255) DEFAULT NULL,
  `size` int(10) unsigned NOT NULL,
  `mime` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `file_time` timestamp NULL DEFAULT NULL,
  `file_time_original` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `text` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `file` (`file`),
  KEY `file_time` (`file_time`),
  KEY `mime` (`mime`),
  KEY `size` (`size`),
  KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Кешированные внешние файлы';
