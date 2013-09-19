<?php

// Чистит превьюшки, которым не соответствует ни один файл

require_once('../config.php');

main();
bors_exit();

function main()
{
	$deleted = 0;

	$thumbs = bors_each('bors_image_thumb', array(
//		'limit' => 100000,
	));

	foreach($thumbs as $t)
	{
		if(!file_exists($t->full_file_name()))
		{
			$t->delete();
			$deleted++;
			echo '-';
		}
//		echo '.';
	}

	if($deleted)
		echo "\n[".date('r')."] Удалено в БД превьюшек записей без реальных файлов: $deleted\n";
}
