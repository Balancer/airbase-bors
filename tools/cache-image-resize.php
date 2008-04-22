<?
	require_once(BORS_CORE.'/config.php');
	require_once("inc/filesystem.php");
	require_once('inc/images.php');

    header("HTTP/1.1 200 OK\n");
    header("Status: 200 OK\n");

//	echo "<xmp>"; print_r($_SERVER); echo "</xmp>";
	
	$dir = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['REQUEST_URI']);
	
	mkpath($dir);
	
	$type = basename($dir);
	
	if(!preg_match("!^(\d*)x(\d*)$!", $type, $m))
		bors_exit(ec("Ошибка! Неверный type=$type для ссылки {$_SERVER['REQUEST_URI']}"));
		
	$need_width  = @$m[1];
	$need_height = @$m[2];
	
	$source_image = preg_replace("!/cache(.+)/\d*x\d*/([^/]+(\.jpe?g|\.png|\.gif|))$!i", "$1/$2", $_SERVER['REQUEST_URI']);
	$source_image_url = preg_replace("!/cache(.+)/\d*x\d*/([^/]+\.(jpe?g|png|gif))$!i", "http://{$_SERVER['HTTP_HOST']}$1/$2", $_SERVER['REQUEST_URI']);

	$source_file = $_SERVER['DOCUMENT_ROOT'].$source_image;
	if(!defined('BORS_IMG_CACHE_TARGET'))
		$target_file = $dir."/".basename($_SERVER['REQUEST_URI']);
	else
	{
		mkpath(BORS_IMG_CACHE_TARGET.dirname($_SERVER['REQUEST_URI']));
		$target_file = BORS_IMG_CACHE_TARGET.$_SERVER['REQUEST_URI'];
	}
	
	if(!file_exists($source_file))
		bors_exit(ec("Ошибка! Отсутствует изображение $source_image [{$source_file}]"));

	if(!config('pics_base_safemodded') && @filesize($source_file) == 0)
		exit(ec("Ошибка! Нулевой размер файла $source_image"));
	
	$imd = getimagesize(config('pics_base_safemodded') ? $source_image_url : $source_file);

//	echo "<xmp>"; print_r($imd); echo "</xmp>";

	$source_width = $imd[0];
	$source_height = $imd[1];
		
	if(!$source_width || !$source_height)
		exit(ec("Ошибка! Неправильный размер {$source_width}x$source_height изображения $source_image"));

	if($need_width && !$need_height)
		$need_height = intval($source_height*$need_width/$source_width+0.5);

	if($need_height && !$need_width)
		$need_width = intval($source_width*$need_height/$source_height+0.5);

	$resize = ($need_width<$source_width || $need_height<$source_height);

	if(!$need_width || !$need_height)
		exit(ec("Не могу определить нужные размеры изображения $source_image"));

	if(config('pics_base_safemodded'))
		image_file_scale($source_image_url, $target_file, $need_width, $need_height);
	else
		image_file_scale($source_file, $target_file, $need_width, $need_height);

	@chmod($target_file, 0666);
		
	header("Status: 200 OK");
	header("Content-Length: ".filesize($target_file));
	header("Content-Type: {$imd['mime']}");
	readfile($target_file);
