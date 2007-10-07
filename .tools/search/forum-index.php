<?php
	setlocale(LC_ALL, 'ru_RU.KOI8-R');

	$_SERVER['DOCUMENT_ROOT'] = "/var/www/balancer.ru/htdocs";
	$_SERVER['HTTP_HOST'] = "balancer.ru";

	require_once($_SERVER['DOCUMENT_ROOT'].'/cms/config.php');
	require_once('funcs/DataBase.php');
	
	config_set('mysql_persistent', true);
	config_set('mysql_disable_autoselect_db', false);

//	$GLOBALS['cms']['mysql_server'] = 'www.avias.loca';

	include_once('engines/search.php');

	$db = &new DataBase(config('search_db'));
	$pundb = &new DataBase('punbb');
	$max = $pundb->get('SELECT MAX(id) FROM posts');
//	$min = $db->get('SELECT MIN(class_id) FROM bors_search_titles')-1;

	for($i = $max; $i>0; $i--)
	{
		$obj = class_load('forum_post', $i);

		if(!$obj)
			continue;
			
		$GLOBALS['bors']->_main_obj=$obj;
		bors_search_object_index($obj, $db);
		echo dc("{$i}: {$obj->title()} [".sizeof($GLOBALS['bors_search_get_word_id_cache'])."]\n");
	}
