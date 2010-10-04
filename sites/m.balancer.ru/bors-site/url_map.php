<?php

bors_url_map(array(
	'/ => balancer_board_mobile_main',
	'/c(\d+) => balancer_board_mobile_categories_view(1)',
	'/f(\d+) => balancer_board_mobile_forums_view(1)',
	'/f(\d+)\.(\d+) => balancer_board_mobile_forums_view(1,2)',
	'/t(\d+) => balancer_board_mobile_topics_view(1)',
	'/t(\d+)\.(\d+) => balancer_board_mobile_topics_view(1,2)',
	'/p(\d+) => balancer_board_mobile_posts_view(1)',
));
