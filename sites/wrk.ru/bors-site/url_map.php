<?php

bors_vhost_routes('wrk.ru', array(
	'/ => balancer_board_main',
	'/blogs/ => wrk_blogs_main',
	'/login/ => wrk_mauth_login',
	'/~(\w)(\w+) => wrk_go(1,2)',
	'/news/tags/(.+)/last\.json => wrk_news_tags_json(1)',
	'/news/tags/(.+)/(\d+)\.html => wrk_news_tags_view(1,2)',
	'/news/tags/(.+) => wrk_news_tags_view(1)',
));

bors_vhost_routes('tanzpol.org', array(
	'/ => balancer_board_main',
	'/blogs/ => wrk_blogs_main',
	'/login/ => wrk_mauth_login',
	'/~(\w)(\w+) => wrk_go(1,2)',
	'/news/tags/(.+)/last\.json => wrk_news_tags_json(1)',
	'/news/tags/(.+)/(\d+)\.html => wrk_news_tags_view(1,2)',
	'/news/tags/(.+) => wrk_news_tags_view(1)',
	'/news/(\d+)/? => wrk_news_topic(1)',
));
