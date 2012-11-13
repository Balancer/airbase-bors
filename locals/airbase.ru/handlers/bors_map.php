<?php

$map = array(
	'(/top/)logos/(\d+)\.png => airbase_top_logo(2)',
	'(/top/)(\d+)\.png => airbase_top_logo(2)',
	'(/top/)(\d+)/logo\.png => airbase_top_logo(2)',
	'(/forums/)index\.php\?showtopic=(\d+) => redirect:balancer_board_topic(2)',
//	'.*viewtopic\.php\?id=(\d+)&p=(\d+).* => balancer_board_topic(1,2)',
//	'.*viewtopic\.php\?id=(\d+).* => balancer_board_topic(1)',
//	'(.*) => airbase_page(1,host=0)',
);
