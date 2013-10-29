#!/usr/bin/php
<?php

$_SERVER['DOCUMENT_ROOT'] = '/var/www/balancer.ru/htdocs';

define('BORS_CORE', '/var/www/bors/bors-core');
define('BORS_LOCAL', '/var/www/bors/bors-airbase');
require_once(BORS_CORE.'/init.php');

show();


function show()
{
	$file = $GLOBALS['argv'][1];
	$data = json_decode(file_get_contents($file), true);
	$min = 9999999;
	$max = -9999999;
//	var_dump($data);
//	foreach($data as $x => $col)
//	{
//		$dy = count($col);
//		foreach($col as $y => $val)

	$dy = 50;
	$dx = 50;
	for($y=0; $y<$dy; $y++)
	{
		for($x=0; $x<$dx; $x++)

		{
			$val = $data[$x][$y];
//			echo $val;
			if($min > $val)
				$min = $val;
			if($max < $val)
				$max = $val;
		}
	}

	echo "$min .. $max\n";

	for($y=0; $y<$dy; $y++)
	{
		for($x=0; $x<$dx; $x++)
		{
			$val = $data[$x][$y];
			$color = color($val, $min, $max);
			$char = floor(max(0, 10*($val-$min)/($max-$min)-0.01));
			blib_cli::out("%$color$char$char");
		}
		echo "\n";
	}
}


function color($val, $min, $max)
{
	static $colors = 'kbBYgGyC';
	return substr($colors, floor(max(
			0,
			strlen($colors)*($val-$min)/($max-$min)-0.001
		)), 1);
}
