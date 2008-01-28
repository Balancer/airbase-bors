<?php
	require_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config.php");
	require_once("funcs/filesystem_ext.php");
	require_once('inc/images.php');
	
	$dir = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['REQUEST_URI']);
	
	mkpath($dir);
	
	$type = basename($dir);
	
	if(!preg_match("!^(\d*)x(\d*)$!", $type, $m))
		exit(ec("Ошибка! Неверный type=$type для ссылки {$_SERVER['REQUEST_URI']}"));
		
	$need_width  = @$m[1];
	$need_height = @$m[2];
	
	$source_image = preg_replace("!/cache(.+)/\d*x\d*/([^/]+\.(jpe?g|png|gif))$!i", "$1/$2", $_SERVER['REQUEST_URI']);

	$source_file = $_SERVER['DOCUMENT_ROOT'].$source_image;
	$target_file = $dir."/".basename($_SERVER['REQUEST_URI']);
	
	if(!file_exists($source_file))
		exit(ec("Ошибка! Отсутствует изображение $source_image"));

	if(@filesize($source_file) == 0)
		exit(ec("Ошибка! Нулевой размер файла $source_image"));
	
	$imd = getimagesize($source_file);

//	echo "<xmp>"; print_r($imd); echo "</xmp>";

	$source_width = $imd[0];
	$source_height = $imd[1];
		
	if(!$source_width || !$source_height)
		exit(ec("Ошибка! Неправильный размер {$source_width}x$source_height изображения $source_image"));

	if($need_width && !$need_height)
		$need_height = intval($source_height*$need_width/$source_width+0.5);

	if($need_height && !$need_width)
		$need_width = intval($source_width*$need_height/$source_height+0.5);

	if(!$need_width || !$need_height)
		exit(ec("Не могу определить нужные размеры изображения $source_image"));

	image_file_scale($source_file, $target_file, $need_width, $need_height);

	@chmod($target_file, 0666);
		
	header("Status: 200 OK");
	header("Content-Length: ".filesize($target_file));
	header("Content-Type: {$imd['mime']}");
	readfile($target_file);
