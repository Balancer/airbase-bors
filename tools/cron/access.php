<?php

define('BORS_CORE', '/var/www/bors/bors-core');
define('BORS_LOCAL', '/var/www/bors/bors-airbase');
include_once(BORS_CORE.'/init.php');

$db = new driver_mysql('BORS');
$db->query('DELETE FROM bors_access_log WHERE access_time < UNIX_TIMESTAMP() - 900');

foreach(objects_array('bors_access_log', array('was_counted' => 0)) as $x)
{
	if(!$x->is_bot() && $target = $x->target())
	{
		$target->visits_inc();
		$x->set_was_counted(1, true);
		echo "+";
	}
	else
	{
		$x->set_was_counted(2, true);
		echo ".";
	}
}

bors_exit();
