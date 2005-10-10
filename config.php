<?
	$GLOBALS['log_level'] = 2;

    ini_set('include_path', ini_get('include_path') . ":{$_SERVER['DOCUMENT_ROOT']}/cms:{$_SERVER['DOCUMENT_ROOT']}/cms-local:{$_SERVER['DOCUMENT_ROOT']}/include");

	include_once('sql.phtml');
	include_once('functions.phtml');
	include_once('design_templates.phtml');

    $GLOBALS['cms'] = array(
		'sites_store_path' => "{$_SERVER['DOCUMENT_ROOT']}/sites",
		'sites_store_uri' => "http://{$_SERVER['HTTP_HOST']}/sites",
		'cache_dir' => "{$_SERVER['DOCUMENT_ROOT']}/cache/system",
		'base_dir' => "{$_SERVER['DOCUMENT_ROOT']}/cms",
		'base_uri' => "http://{$_SERVER['HTTP_HOST']}/cms",
		'default_template' => "http://{$_SERVER['HTTP_HOST']}/cms/templates/skins/default/",

		'main_host_dir' => "{$_SERVER['DOCUMENT_ROOT']}/htdocs",
		'main_host_uri' => "http://{$_SERVER['HTTP_HOST']}",

		'mysql_database' => 'WWW',
		'mysql_login' => 'wwwtest',
		'mysql_pw' => 'longshine',
#		'mysql_server' => 'www.avias.local',
		'mysql_server' => 'localhost',
		'cache_disabled' => true,
		'charset'=>'koi8-r',
		'charset_u'=>'koi8r',
		'locale'=>'ru_RU.koi8r',
	);
	
	// Вторичные переменные, которые могут задаваться заданными ранее.
	$GLOBALS['cms'] += array(
		'main_base_uri' => $GLOBALS['cms']['main_host_uri'].'/cms',
		'mysql_cache_database' => $GLOBALS['cms']['mysql_database'],
		'mysql_cache_login' => $GLOBALS['cms']['mysql_login'],
		'mysql_cache_pw' => $GLOBALS['cms']['mysql_pw'],
		'mysql_cache_server' => $GLOBALS['cms']['mysql_server'],
		'default_template_file' => "{$GLOBALS['cms']['base_dir']}/funcs/templates/default.tpl",
	);
	
	// Для совместимости
	$GLOBALS['doc_root'] = $_SERVER['DOCUMENT_ROOT'];

	$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

	$GLOBALS['cms'] += array(
		'conferences_path' => 'forum-new',
		'conferences_host' => $_SERVER['HTTP_HOST'],
	);

	if(get_magic_quotes_gpc())
		foreach($_POST as $key => $val)
			 $_POST[$key] = stripslashes($val);
?>
