<?php

bors_vhost_routes('wrk.ru', array(
	'/ => balancer_board_main',
	'/blogs/ => wrk_blogs_main',
	'/login/ => wrk_mauth_login',
	'/~(\w)(\w+) => wrk_go(1,2)',
	'/news/tags/(.+)/(\d+)\.html => wrk_news_tags_view(1,2)',
	'/news/tags/(.+) => wrk_news_tags_view(1)',
));
