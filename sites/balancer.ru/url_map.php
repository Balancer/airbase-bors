<?php

bors_vhost_routes('balancer.ru', array(
   	'/ => balancer_main',

	'/cache/avatars/jpgto/(.+)\.jpg => balancer_cache_avatars_jpgto(1)',

	'/_tools/external/sites/preview => balancer_tools_external_sites_preview',
	'/(_cg/_st)/\w/\w/(\S+-\d+x\d+)\.png => balancer_tools_external_sites_preview(2,1)',
	'/(_cg/_st)/\w+/\w/[^/]+/(\S+-\d+x\d+)\.png => balancer_tools_external_sites_preview(2,1)',
	'/(_cg/_st)/\w/\w/(\S+)\.png => balancer_tools_external_sites_preview(2,1)',

	'/forum/topics/(\d+)/reports/users-graph\.png => balancer_board_topic_usersGraphPng(1)',
	'/forum/topics/(\d+)/reports/users-graph\.svg => balancer_board_topic_usersGraphSVG(1)',
	'/forum/topics/(\d+)/reports/users-ograph\.svg => balancer_board_topic_usersGraphSVG(1,ordered=1)',

	'/forums/attaches/(\d+)/? => balancer_board_attaches_view(1)',

	'(/forum/(\d+)/)news/ => airbase_forum_news(2)',
	'(/forum/(\d+)/)news/(\d+)\.html => airbase_forum_news(2,3)',

	'/lor/topics/(\d+)/reports/users-graph\.svg => lor_board_topic_usersGraphSVG(1)',
	'/lor/topics/(\d+)/reports/users-ograph\.svg => lor_board_topic_usersGraphSVG(1,ordered=1)',

	'/wc/\?(.+) => balancer_wc(1)',

	'/pages/ro/? => airbase_pages_ro',

	'/rpc/json/load/(\w+)/(\w+) => bal_rpc_json_load(2,target_class=1)',
	'/rpc/json/load/(\w+)/(\w+)/(.+) => bal_rpc_json_load(2,target_class=1,fields=3)',

	'/rpc/json/find/(\w+) => bal_rpc_json_find(1)',

	'/rpc/json/tanzpol => bal_rpc_json_tanzpol',

	'/users/(\d+)/posts/chart/ => users_posts_chart(1)',
	'/users?/(\d+)/? => user_main(1)',

	'/users/reputations/? => balancer_users_reputations',
	'(/users/)reputations/(\d+)\.html => balancer_users_reputations(NULL,2)',
	'/users/reputation/last-ograph\.svg => balancer_board_users_reputationGraphSVG',

	'/memo/theo/mmorpg/ => balancer_page_dropbox',

	'(/blog)/(\d{4})/? => balancer_blog_year(2)',

	'/_cg/(\d{4}-\d{1,2})/(\w+)\.(\w+) => b2f_cache_generated(2,ext=3,year=1)',

	'.* => balancer_page_hts',
//	'.* => bal_pages_hts(url)',
));
