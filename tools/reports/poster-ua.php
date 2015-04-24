#!/usr/bin/php
<?php

require_once('../config.php');
main();
bors_exit();

function main()
{
	$oses = [];
	$browsers = [];
	foreach(bors_each('balancer_board_post', ['poster_id' => 47366, 'posted>' => time()-86400*365]) as $p)
	{
		list($os, $browser, $ov, $bv) = get_browser_info($p->poster_ua());
		@$oses[$os.' '.$ov]++;
	}

	echo "[csv]\n*OS;*Число сообщений\n";
	foreach($oses as $os => $count)
		echo "$os; $count\n";
	echo "[/csv]\n";
}
