<?php

// Чистит превьюшки с не соответствующими картинками именами файлов.

require_once('../config.php');

main();
bors_exit();

function main()
{
	$deleted = 0;
	$updated = 0;

	$thumbs = bors_find_all('bors_image_thumb', array(
		'left_join' => '`AB_BORS`.`bors_images` i ON (`bors_pictures_thumbs`.id LIKE CONCAT(i.id, ",%") AND i.file_name = `bors_pictures_thumbs`.file_name)',
//		'`bors_pictures_thumbs`.file_name',
		'i.file_name IS NULL',
//		'limit' => 1000,
	));

	foreach($thumbs as $t)
	{
		@unlink($t->full_file_name());
		$t->delete();
//		echo $t->file_name(), ': ', $t->full_file_name(), PHP_EOL;
		$deleted++;
	}

	if($deleted)
		echo "[".date('r')."] Удалено превью с несоответствующием именем изображения: $deleted\n";
}
