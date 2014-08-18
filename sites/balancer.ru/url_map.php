<?php

bors_vhost_routes('balancer.ru', array(
	'/cache/avatars/jpgto/(.+)\.jpg => balancer_cache_avatars_jpgto(1)',

	'/_tools/external/sites/preview => balancer_tools_external_sites_preview',
	'/(_cg/_st)/\w/\w/(\S+-\d+x\d+)\.png => balancer_tools_external_sites_preview(2,1)',
	'/(_cg/_st)/\w+/\w/[^/]+/(\S+-\d+x\d+)\.png => balancer_tools_external_sites_preview(2,1)',
	'/(_cg/_st)/\w/\w/(\S+)\.png => balancer_tools_external_sites_preview(2,1)',

	'/forum/topics/(\d+)/reports/users-graph\.png => balancer_board_topic_usersGraphPng(1)',
	'/forum/topics/(\d+)/reports/users-graph\.svg => balancer_board_topic_usersGraphSVG(1)',
	'/forum/topics/(\d+)/reports/users-ograph\.svg => balancer_board_topic_usersGraphSVG(1,ordered=1)',

	'/forums/attaches/(\d+)/? => balancer_board_attaches_view(1)',

	'/lor/topics/(\d+)/reports/users-graph\.svg => lor_board_topic_usersGraphSVG(1)',
	'/lor/topics/(\d+)/reports/users-ograph\.svg => lor_board_topic_usersGraphSVG(1,ordered=1)',

	'/wc/\?(.+) => balancer_wc(1)',

	'/pages/ro/? => airbase_pages_ro',

	'/users?/(\d+)/? => user_main(1)',

	'/users/reputations/? => balancer_users_reputations',
	'(/users/)reputations/(\d+)\.html => balancer_users_reputations(NULL,2)',
	'/users/reputation/last-ograph\.svg => balancer_board_users_reputationGraphSVG',

	'/memo/theo/mmorpg/ => balancer_page_dropbox',

	'(/blog)/(\d{4})/? => balancer_blog_year(2)',

	'.* => bal_pages_hts(url)',
));
