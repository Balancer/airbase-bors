<?php

require_once('../config.php');

main();
bors_exit();

function main()
{
	foreach(bors_each('balancer_board_post', ['topic_id' => 90655]) as $p)
	{
		$p->recalculate();
		echo $p, PHP_EOL;
	}
}
