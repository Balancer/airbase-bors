<?php

bors_url_map(array(
	'/(\d+)\.html => balancer_blogs_main(NULL,1)',
	'/ => balancer_blogs_main',

	'(/)tags/ => balancer_blogs_tags_main',

	'(/tags/)(.*)/(\d+)\.html => balancer_blogs_tags_show(2,3)',
	'(/tags/)(.*)/? => balancer_blogs_tags_show(2)',
));
