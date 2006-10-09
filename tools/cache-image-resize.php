<?
	require_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config.php");
	require_once("funcs/filesystem_ext.php");
	
	$dir = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['REQUEST_URI']);
	
	mkpath($dir);
	
	$type = basename($dir);
	
	if(!preg_match("!^(\d*)x(\d*)$!", $type, $m))
		exit("Ошибка! Неверный type=$type для ссылки {$_SERVER['REQUEST_URI']}");
		
	$need_width  = @$m[1];
	$need_height = @$m[2];

	
	$source_image = preg_replace("!/cache(.+)/\d*x\d*/([^/]+\.(jpg|png|gif))$!", "$1/$2", $_SERVER['REQUEST_URI']);

	$source_file = $_SERVER['DOCUMENT_ROOT'].$source_image;
	$target_file = $dir."/".basename($_SERVER['REQUEST_URI']);
	
	if(!file_exists($source_file))
		exit("Ошибка! Отсутствует изображение $source_image");

	if(filesize($source_file) == 0)
		exit("Ошибка! Нулевой размер файла $source_image");
	
	$imd = getimagesize($source_file);

//	echo "<xmp>"; print_r($imd); echo "</xmp>";

	$source_width = $imd[0];
	$source_height = $imd[1];
		
	if(!$source_width || !$source_height)
		exit("Ошибка! Неправильный размер {$source_width}x$source_height изображения $source_image");

	if($need_width && !$need_height)
		$need_height = intval($source_height*$need_width/$source_width+0.5);

	if($need_height && !$need_width)
		$need_width = intval($source_width*$need_height/$source_height+0.5);

	$resize = ($need_width<$source_width || $need_height<$source_height);

	if(!$need_width || !$need_height)
		exit("Не могу определить нужные размеры изображения $source_image");

	$size = $need_width."x".$need_height;

	if($resize)
		echo system("{$GLOBALS['cms']['convert_cmd']} -geometry $size ".escapeshellarg($source_file)." ".escapeshellarg($target_file));
	else
		copy($source_file, $target_file);

	chmod($target_file, 0666);
		
	header("Status: 200 OK");
	header("Content-Length: ".filesize($target_file));
	header("Content-Type: {$imd['mime']}");
	readfile($target_file);
