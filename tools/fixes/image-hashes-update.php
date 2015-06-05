<?php

require '../config.php';

$last_id = 1296093; // bors_find_first('airbase_image', array('order' => '-id'))->id()+1;

do
{
	$img = NULL;

	echo "\n$last_id (".(@$img?$img->ctime():'')."): ", bors_debug::memory_usage_ping(), PHP_EOL;

	foreach(bors_find_all('airbase_image', array(
		'full_file_name<>' => '',
//		'hash_y IS NULL',
		'id<' => $last_id,
		'order' => '-id',
		'limit' => 100,
	)) as $img)
	{
		$img->hash_recalculate(true,true);
		usleep(10000);
		echo '.';
	}

	if($img)
		$last_id = $img->id();

	bors()->changed_save();
	bors_object_caches_drop();
	sleep(3);

} while($img);
