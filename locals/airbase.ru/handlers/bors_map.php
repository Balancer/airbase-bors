<?php

$map = array(
	'(/top/)logos/(\d+)\.png => airbase_top_logo(2)',
	'(/top/)(\d+)\.png => airbase_top_logo(2)',
	'(/top/)(\d+)/logo\.png => airbase_top_logo(2)',
	'(/forums/)index\.php\?showtopic=(\d+) => redirect:forum_topic(2)',
//	'.*viewtopic\.php\?id=(\d+)&p=(\d+).* => forum_topic(1,2)',
//	'.*viewtopic\.php\?id=(\d+).* => forum_topic(1)',
//	'(.*) => airbase_page(1,host=0)',
);
