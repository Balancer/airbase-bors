<?php

// use Tracy\Debugger;

if(!defined('COMPOSER_ROOT'))
	define('COMPOSER_ROOT', '/var/www/bors/composer');

if(!defined('BORS_CORE'))
{
	define('BORS_CORE', COMPOSER_ROOT.'/vendor/balancer/bors-core');
	define('BORS_3RD_PARTY', '/var/www/repos/bors-third-party');
}

if(!defined('BORS_LOCAL'))
	define('BORS_LOCAL', '/var/www/bors/composer/vendor/balancer/airbase-bors');

if(!defined('BORS_SITE'))
	define('BORS_SITE', '/var/www/bors/composer/vendor/balancer/airbase-bors');

require_once(COMPOSER_ROOT.'/vendor/autoload.php');

require_once(BORS_CORE.'/init.php');
config_set('mysql_use_pool', false);
bors_init();

// Debugger::enable(Debugger::DEVELOPMENT);

require_once BORS_CORE.'/inc/strings.php';
