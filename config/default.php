<?php

function config_set($key, $value) { $GLOBALS['cms']['config'][$key] = $value; }
function config($key) { return @$GLOBALS['cms']['config'][$key]; }

$GLOBALS['log_level'] = 2;

	if(!defined("BORS_INCLUDE"))
		define("BORS_INCLUDE", "{$_SERVER['DOCUMENT_ROOT']}/cms/");

	if(!defined("BORS_INCLUDE_LOCAL"))
		define("BORS_INCLUDE_LOCAL", "{$_SERVER['DOCUMENT_ROOT']}/cms-local/");

	$includes = array(
		BORS_INCLUDE_LOCAL,
		BORS_INCLUDE."vhosts/{$_SERVER['HTTP_HOST']}",
		BORS_INCLUDE,
		"{$_SERVER['DOCUMENT_ROOT']}/include",
		BORS_INCLUDE.'PEAR'
	);

	$delim = empty($_ENV['windir']) ? ":" : ";";

    ini_set('include_path', ini_get('include_path') . $delim . join($delim, $includes));

	if(!function_exists('file_put_contents'))
		include_once('include/php4/file_put_contents.php');

	require_once('classes/objects/Bors.php');

    $GLOBALS['cms'] = array(
		'sites_store_path' => "{$_SERVER['DOCUMENT_ROOT']}/sites",
		'sites_store_uri' => "http://{$_SERVER['HTTP_HOST']}/sites",
		'cache_dir' => "{$_SERVER['DOCUMENT_ROOT']}/cache/system",
		'base_dir' => BORS_INCLUDE,
		'local_dir' => @BORS_INCLUDE_LOCAL,
		'base_uri' => "http://{$_SERVER['HTTP_HOST']}/cms",
		'default_template' => "xfile:{$_SERVER['DOCUMENT_ROOT']}/cms/templates/default/index.html",

		'main_host_dir' => $_SERVER['DOCUMENT_ROOT'],
		'main_host_uri' => "http://{$_SERVER['HTTP_HOST']}/",

		'cache_disabled' => true,
		'charset'=>'utf-8',
		'charset_u'=>'utf8',
		'locale'=>'ru_RU.utf8',
		'user_engine'=>'test',
		'cache_disabled' => true,
        'cache_static'  => false,
		'templates_cache_disabled' => true,
		'cache_engine' => 'Cache',

		'mysql_server' => 'localhost',

		'referer' => @$_SERVER['HTTP_REFERER'],
	);

	// Вторичные переменные, которые могут задаваться заданными ранее.
	$GLOBALS['cms'] += array(
		'main_base_uri' => $GLOBALS['cms']['main_host_uri'].'/cms',
		'default_template_file' => "{$GLOBALS['cms']['base_dir']}/funcs/templates/default.tpl",

		'smilies_dir' => "{$GLOBALS['cms']['main_host_dir']}/forum/smilies",
		'smilies_uri' => "{$GLOBALS['cms']['main_host_uri']}/forum/smilies",

	);
	
	// Для совместимости
	$GLOBALS['doc_root'] = $_SERVER['DOCUMENT_ROOT'];

	$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

	if(get_magic_quotes_gpc())
		foreach($_POST as $key => $val)
			if(is_array($val))
			{
				$res = array();
				foreach($val as $v)
					$res[] = stripslashes($v);
				$_POST[$key] = $res;
			}
			else
				$_POST[$key] = stripslashes($val);

	$GLOBALS['bors_data']['config'] = array(
		'gpc' => get_magic_quotes_gpc(),
	);

config_set('search_autoindex', true);
