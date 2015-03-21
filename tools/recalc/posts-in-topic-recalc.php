<?php

require_once('../config.php');

main();
bors_exit();

function main()
{
	config_set('lcml_cache_disable_full', true);
	config_set('lcml.timeout', 999999);

	foreach(bors_each('balancer_board_post', ['topic_id' => 65918]) as $p)
	{
		$p->do_lcml_full_compile();
		$p->recalculate();
		$p->cache_clean();
		echo $p, PHP_EOL;
	}

	$p->topic()->recalculate();
}
