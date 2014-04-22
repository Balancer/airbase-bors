<?php

require_once('../config.php');

main();
bors_exit();

function main()
{
	$step = 1000;
	$data_dir = '/var/www/forums.balancer.ru/data';

	$rdbh = new driver_mysql('AB_FORUMS');
	$max_id = $rdbh->select('posts', 'MAX(id)', array());
	echo "Total posts: $max_id\n";
	for($i=$max_id; $i>=0; $i-=$step)
	{
		echo ($i-$step).".. $i\n";
		$posts = bors_find_all('balancer_board_post', array(
			'id BETWEEN '.($i-$step).' AND '.($i+1),
			'by_id' => true,
		));

		foreach($posts as $pid => $p)
		{
			$dir = $data_dir . sprintf('/%04d', floor($pid/10000));
			$file = sprintf('%04d.json', $pid%10000);
			echo "$pid: $dir/$file\n";
			mkpath($dir, 0775);
			file_put_contents($f = $dir.'/'.$file, json_encode($p->data, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
			touch($f, $p->modify_time());
		}
	}
}
