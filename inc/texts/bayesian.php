<?php

function bors_nb_train($object, $category)
{
	$db     = config('main_bors_db');
	$login  = config_mysql_login($db);
	$pass   = config_mysql_password($db);
	$server = config_mysql_server($db);

	include_once 'phpnaivebayesian-1.0/class.naivebayesian.php';
	include_once 'phpnaivebayesian-1.0/class.naivebayesianstorage.php';
	include_once 'phpnaivebayesian-1.0/class.mysql.php';

	$nbs = new NaiveBayesianStorage($login, $pass, $server, $db);
	$nb  = new NaiveBayesian($nbs);

	if($nb->train($object->internal_uri(), $category, $object->source()))
	    $nb->updateProbabilities();
}

function bors_nb_cat($object)
{
	$db     = config('main_bors_db');
	$login  = config_mysql_login($db);
	$pass   = config_mysql_password($db);
	$server = config_mysql_server($db);

	include_once 'phpnaivebayesian-1.0/class.naivebayesian.php';
	include_once 'phpnaivebayesian-1.0/class.naivebayesianstorage.php';
	include_once 'phpnaivebayesian-1.0/class.mysql.php';

	$nbs = new NaiveBayesianStorage($login, $pass, $server, $db);
	$nb  = new NaiveBayesian($nbs);

	return $nb->categorize($txt);
}
