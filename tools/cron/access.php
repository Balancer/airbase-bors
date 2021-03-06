<?php

define('BORS_CORE', '/var/www/bors/bors-core');
define('BORS_LOCAL', '/var/www/bors/composer/vendor/balancer/airbase-bors');
include_once(BORS_CORE.'/init.php');

$db = new driver_mysql('AB_BORS');
$db->query('DELETE FROM bors_access_log WHERE access_time < UNIX_TIMESTAMP() - 900');

foreach(bors_find_all('bors_access_log', array('was_counted' => 0)) as $x)
{
	if(!$x->is_bot() && $target = $x->target())
	{
		bors_external_referer::register($x->server_uri(), $x->referer(), $target);
		$target->visits_inc();
		$x->set_was_counted(1, true);
		echo "+";
	}
	else
	{
		bors_external_referer::register($x->server_uri(), $x->referer(), NULL);
		$x->set_was_counted(2, true);
		echo ".";
	}
}

bors_exit();
