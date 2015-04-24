#!/usr/bin/php
<?php

require_once('../config.php');
main();
bors_exit();

function main()
{
	$oss = [];
	$ovs = [];
	$osk = [];
	$years = [];
	$browsers = [];
	for($y=1998;$y<=date('Y');$y++)
	{
		echo "$y ";
		$start = strtotime("$y-01-01 00:00:00 MSK");
		$stop = strtotime("$y-12-31 23:59:59 MSK");

		foreach(bors_find_all('balancer_board_post', [
				'poster_id' => 10000,
				'posted BETWEEN' => [$start, $stop],
				'order' => '-id'
		]) as $p)
		{
			list($os, $browser, $ov, $bv) = get_browser_info($p->poster_ua());
			$year = date('Y', $p->create_time());
			@$ovs[$year.'; '.$os.' '.$ov]++;
			@$oss[$year.'; '.$os]++;
			if($os)
			{
				@$osk[$os]++;
				@$years[$year]++;
			}
		}
	}

	$osk   = array_keys($osk);
	$years = array_keys($years);

	sort($years);
	sort($osk);

	arsort($oss);
	arsort($ovs);

	print_r($ovs);
	print_r($oss);
	print_r($years);

	echo "[csv]\n*Год;*";
	$first = true;
	foreach($osk as $os)
	{
		if(!$first)
			echo ";*";

		$first = false;

		echo $os;
	}

	echo "\n";

	foreach($years as $year)
	{
		echo "$year; ";
		$first = true;
		foreach($osk as $os)
		{
			if(!$first)
				echo "; ";
			$first = false;

			echo intval(@$oss["$year; $os"]);
		}
		echo "\n";
	}

	echo "[/csv]\n\n";
}
