CREATE TABLE IF NOT EXISTS `cache` (
  `type` varchar(255) collate utf8_unicode_ci default NULL,
  `key` varchar(255) collate utf8_unicode_ci default NULL,
  `hmd` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `value` text collate utf8_unicode_ci NOT NULL,
  `access_time` int(11) NOT NULL default '0',
  `create_time` int(11) NOT NULL default '0',
  `expire_time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`hmd`),
  KEY `type` (`type`),
  KEY `key` (`key`),
  KEY `access_time` (`access_time`),
  KEY `create_time` (`create_time`),
  KEY `expire_time` (`expire_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `hts_aliases` (
  `alias` varchar(255) NOT NULL default '',
  `uri` varchar(255) NOT NULL default '',
  UNIQUE KEY `alias` (`alias`(80))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hts_data_access_level` (
  `id` varchar(255) default NULL,
  `value` int(11) NOT NULL default '0',
  UNIQUE KEY `pair` (`id`,`value`),
  KEY `id` (`id`),
  KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hts_data_author` (
  `id` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hts_data_author_name` (
  `id` varchar(255) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`(166),`value`(166))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hts_data_autolink` (
  `id` varchar(255) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `pairs` (`id`(166),`value`(166)),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hts_data_backup` (
  `id` varchar(255) default NULL,
  `value` int(11) NOT NULL auto_increment,
  `time` int(11) NOT NULL default '0',
  `member_id` int(11) NOT NULL default '0',
  `ip` varchar(255) NOT NULL default '',
  `type` varchar(16) NOT NULL default '',
  `source` text NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `modify_time` int(11) NOT NULL default '0',
  `description_source` text NOT NULL,
  `version` int(11) NOT NULL default '0',
  `backup_time` int(11) NOT NULL default '0',
  UNIQUE KEY `pair` (`id`,`value`,`time`,`member_id`,`modify_time`,`version`,`backup_time`),
  UNIQUE KEY `versions` (`id`,`version`),
  KEY `id` (`id`),
  KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=379 ;

CREATE TABLE IF NOT EXISTS `hts_data_body` (
  `id` varchar(255) NOT NULL default '',
  `value` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hts_data_cache_create_time` (
  `id` varchar(255) NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_child`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_child` (
  `id` varchar(255) default NULL,
  `value` varchar(255) NOT NULL default '0',
  UNIQUE KEY `pairs` (`id`(166),`value`(166)),
  KEY `id_2` (`id`),
  KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_color`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_color` (
  `id` varchar(255) default NULL,
  `value` varchar(32) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_compile_time`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_compile_time` (
  `id` varchar(255) NOT NULL default '',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_copyright`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_copyright` (
  `id` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_cr_type`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_cr_type` (
  `id` varchar(255) default NULL,
  `value` varchar(16) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_create_time`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_create_time` (
  `id` varchar(255) NOT NULL default '',
  `value` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_description`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_description` (
  `id` varchar(255) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_description_source`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_description_source` (
  `id` varchar(255) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_flags`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_flags` (
  `id` varchar(255) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  UNIQUE KEY `id` (`id`(166),`value`(166))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_forum_id`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_forum_id` (
  `id` varchar(255) NOT NULL default '',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_height`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_height` (
  `id` varchar(255) NOT NULL default '',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_images_upload`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_images_upload` (
  `id` varchar(255) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id` (`id`(166),`value`(166)),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_keyword`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_keyword` (
  `id` varchar(255) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  UNIQUE KEY `id_2` (`id`(166),`value`(166)),
  KEY `id` (`id`),
  KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_local_path`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_local_path` (
  `id` varchar(255) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  UNIQUE KEY `id_2` (`id`(166),`value`(166)),
  KEY `id` (`id`),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_logdir`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_logdir` (
  `id` varchar(255) default NULL,
  `value` varchar(255) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_modify_time`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_modify_time` (
  `id` varchar(255) NOT NULL default '',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`,`value`),
  KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_nav_name`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_nav_name` (
  `id` varchar(255) default NULL,
  `value` varchar(255) NOT NULL default '',
  KEY `id` (`id`),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_origin_uri`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_origin_uri` (
  `id` varchar(255) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  UNIQUE KEY `id_2` (`id`(166),`value`(166)),
  KEY `id` (`id`),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_parent`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_parent` (
  `id` varchar(255) default NULL,
  `value` varchar(255) NOT NULL default '0',
  UNIQUE KEY `pairs` (`id`(166),`value`(166)),
  KEY `id` (`id`),
  KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_position`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_position` (
  `id` int(11) NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=koi8r;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_referer`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_referer` (
  `id` varchar(255) default NULL,
  `value` varchar(255) NOT NULL default '0',
  UNIQUE KEY `id_2` (`id`(166),`value`(166)),
  KEY `id` (`id`),
  KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_right_column`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_right_column` (
  `id` varchar(255) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_site_store`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_site_store` (
  `id` varchar(255) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  UNIQUE KEY `id_2` (`id`(166),`value`(166)),
  KEY `id` (`id`),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_size`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_size` (
  `id` varchar(255) NOT NULL default '',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_source`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_source` (
  `id` varchar(255) NOT NULL default '',
  `value` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_split_type`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_split_type` (
  `id` varchar(255) default NULL,
  `value` varchar(16) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_style`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_style` (
  `id` varchar(255) default NULL,
  `value` varchar(32) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_subscribe`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_subscribe` (
  `id` varchar(166) NOT NULL default '',
  `value` varchar(255) character set utf8 NOT NULL default '',
  `visited` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`,`value`),
  KEY `id` (`id`),
  KEY `updated` (`visited`),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=koi8r;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_template`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_template` (
  `id` varchar(255) NOT NULL default '',
  `value` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_title`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_title` (
  `id` varchar(255) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_type`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_type` (
  `id` varchar(255) NOT NULL default '',
  `value` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_version`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_version` (
  `id` varchar(255) default NULL,
  `value` int(11) NOT NULL default '0',
  UNIQUE KEY `pair` (`id`,`value`),
  KEY `id` (`id`),
  KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_views`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_views` (
  `id` varchar(255) default NULL,
  `value` int(11) NOT NULL default '0',
  KEY `id` (`id`),
  KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_views_first`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_views_first` (
  `id` varchar(255) default NULL,
  `value` int(11) NOT NULL default '0',
  UNIQUE KEY `pair` (`id`,`value`),
  KEY `id` (`id`),
  KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_views_last`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_views_last` (
  `id` varchar(255) default NULL,
  `value` int(11) NOT NULL default '0',
  UNIQUE KEY `pair` (`id`,`value`),
  KEY `id` (`id`),
  KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_data_width`
-- 

CREATE TABLE IF NOT EXISTS `hts_data_width` (
  `id` varchar(255) NOT NULL default '',
  `value` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_ext_log`
-- 

CREATE TABLE IF NOT EXISTS `hts_ext_log` (
  `id` int(11) NOT NULL auto_increment,
  `time` int(11) NOT NULL default '0',
  `pid` varchar(255) NOT NULL default '0',
  `version` int(11) NOT NULL default '0',
  `member_id` int(11) NOT NULL default '0',
  `user` varchar(255) NOT NULL default '',
  `action` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_ext_referers`
-- 

CREATE TABLE IF NOT EXISTS `hts_ext_referers` (
  `id` varchar(255) NOT NULL default '0',
  `referer` varchar(255) NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `first_enter` int(11) NOT NULL default '0',
  `last_enter` int(11) NOT NULL default '0',
  UNIQUE KEY `id_2` (`id`(166),`referer`(166)),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_ext_system_data`
-- 

CREATE TABLE IF NOT EXISTS `hts_ext_system_data` (
  `key` varchar(32) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  `type` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`key`),
  KEY `key` (`key`,`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_host_redirect`
-- 

CREATE TABLE IF NOT EXISTS `hts_host_redirect` (
  `from` varchar(255) NOT NULL default '',
  `to` varchar(255) NOT NULL default '',
  UNIQUE KEY `from` (`from`(80))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_hosts`
-- 

CREATE TABLE IF NOT EXISTS `hts_hosts` (
  `host` varchar(255) NOT NULL default '',
  `doc_root` varchar(255) NOT NULL default '',
  `default_access_level` int(11) NOT NULL default '3',
  UNIQUE KEY `host` (`host`),
  KEY `doc_root` (`doc_root`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `hts_keys`
-- 

CREATE TABLE IF NOT EXISTS `hts_keys` (
  `name` varchar(166) NOT NULL default '',
  `type` varchar(166) NOT NULL default '',
  `protected` tinyint(4) NOT NULL default '1',
  `id_in_value` smallint(6) NOT NULL default '0',
  `array` int(11) NOT NULL default '0',
  `unique_id` int(11) NOT NULL default '1',
  `autoinc_value` smallint(6) default NULL,
  `params` text,
  PRIMARY KEY  (`name`(80))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Структура таблицы `users_data`
-- 

CREATE TABLE IF NOT EXISTS `users_data` (
  `user_id` int(11) NOT NULL default '0',
  `key` varchar(166) NOT NULL default '',
  `value` varchar(166) NOT NULL default '',
  KEY `member_id` (`user_id`),
  KEY `key` (`key`(80)),
  KEY `value` (`value`(80))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
         
