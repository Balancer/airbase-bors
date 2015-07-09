<?php

require_once('../config.php');

main();
bors_exit();

function main()
{
	$deleted = 0;
	$updated = 0;

	$sites = array(
		'files.balancer.ru/files' => 'http://files.balancer.ru/files/forums/attaches',
		'airbase.ru/htdocs' => 'http://www.airbase.ru',
		'balancer.ru/htdocs' => 'http://www.balancer.ru',
	);

	$thumbs = bors_find_all('bors_image_thumb', array(
		'create_time' => 0,
		'limit' => 10000,
	));
	foreach($thumbs as $t)
	{
		$full_path = NULL;
		foreach($sites as $base_dir => $base_url)
		{
			if(file_exists($test = '/var/www/'.$base_dir.$t->relative_path().'/'.$t->file_name()))
			{
				$full_path = $test;
				break;
			}
		}

		if(!file_exists($full_path) || !$t->file_name())
		{
//			echo "{$t->id()}: not exists '{$t->relative_path()}' / '{$t->file_name()}'\n";
			echo 'x';
			$t->delete();
			$deleted++;
			continue;
		}

		if(!filesize($full_path))
		{
			echo "{$t->id()}: zero size $full_path '{$t->relative_path()}' / '{$t->file_name()}'\n";
			continue;
		}

//		$t->set_full_file_name($full_path, true);
//		echo "{$t->id()}: Found {$full_path}: ".filesize($full_path)."\n";
		if(!$t->create_time(true))
			$ct = $t->set_create_time(filectime($full_path), true);
		$updated++;
	}
	echo "Updated: $updated, deleted: $deleted\n";
}
