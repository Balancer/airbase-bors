<?php

if(!defined('BORS_CORE'))
	define('BORS_CORE', '/var/www/bors-dev/bors-core');

if(!defined('BORS_LOCAL'))
	define('BORS_LOCAL', '/var/www/airbase.local/bors-airbase');

if(!defined('BORS_SITE'))
	define('BORS_SITE', '/var/www/balancer.local/bors-site');

require_once(BORS_CORE.'/init.php');
config_set('mysql_use_pool', false);
bors_init();
