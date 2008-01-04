<?php

$map = array(
	'(/top/)logos/(\d+)\.png => airbase_top_logo(2)',
	'(/top/)(\d+)\.png => airbase_top_logo(2)',
	'(/top/)(\d+)/logo\.png => airbase_top_logo(2)',
	'(/forums/)index\.php\?showtopic=(\d+) => redirect:forum_topic(2)',
	'.* => base_page_hts',
);
