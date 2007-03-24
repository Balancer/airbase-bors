<?
	$GLOBALS['log_level'] = 2;

	$includes = array(
		CMS_INCLUDE_LOCAL.'cms-local',
		CMS_INCLUDE."vhosts/{$_SERVER['HTTP_HOST']}",
		CMS_INCLUDE,
		"{$_SERVER['DOCUMENT_ROOT']}/include",
		CMS_INCLUDE.'PEAR'
	);

	$delim = empty($_ENV['windir']) ? ":" : ";";

    ini_set('include_path', ini_get('include_path') . $delim . join($delim, $includes));

	if(!function_exists('file_put_contents'))
		include_once('include/php4/file_put_contents.php');

    $GLOBALS['cms'] = array(
		'sites_store_path' => "{$_SERVER['DOCUMENT_ROOT']}/sites",
		'sites_store_uri' => "http://{$_SERVER['HTTP_HOST']}/sites",
		'cache_dir' => "{$_SERVER['DOCUMENT_ROOT']}/cache/system",
		'base_dir' => CMS_INCLUDE,
		'local_dir' => CMS_INCLUDE_LOCAL,
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
			 $_POST[$key] = stripslashes($val);

	$GLOBALS['bors_data']['config'] = array(
		'gpc' => get_magic_quotes_gpc(),
	);
