<?php

$forums = '(/|/support/|/tech/forum/|/community/|/society/|/socionics/forum/|/forum/)';

$map = array(
	'/\w{32}/cache(/.*/\d*x\d*/[^/]+\.(jpe?g|png|gif)) => bors_image_autothumb(1)',
	'/\w{32}/cache(/.*/\d*x\d*\([^)]+\)/[^/]+\.(jpe?g|png|gif)) => bors_image_autothumb(1)',

	'.*/\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+)\-rss\.xml => forum_topic_rss(1)',
	'.*/\d{4}/\d{1,2}/topic\-(\d+)\-rss\.xml => forum_topic_rss(1)',
//	'.* => page_fs_separate', - Временно отключено по причине ошибок определения загруженности страницы.
	'.*/ => airbase_page_hts_plain',
	'.*/ => airbase_page_hts_plainu',
	'.*/\w+\.phtml => airbase_page_hts_plain',
	'.*/\w+\.phtml => airbase_page_hts_plainu',

	'.*/\w+\.phtml => base_page_hts(url)',

	'/_bors/ajax/thumb\-(up|down)\?object=(.+) => balancer_ajax_thumb_vote(2,vote=1)',
	'/_bors/ajax/post\-footer\-tools\?object=(.+) => balancer_board_posts_tools_footerAJAX(1)',

	'.*viewtopic\.php\?id=(\d+)&p=(\d+).* => forum_topic(1,2)',
	'.*viewtopic\.php\?id=(\d+).* => forum_topic(1)',
	'.*viewforum\.php\?id=(\d+).* => forum_forum(1)',
	'.*/index.php => forum_main',

	'/_bors/local/search/result/ => bors_tools_search_result',
	'/tools/search/result/ => bors_tools_search_result',
	'/tools/search/ => bors_tools_search',

	'(/forum/)topic/\d+/(\d+),new/ => forum_topic(2)',
	'(/)forum(\d+)/ => redirect:forum_forum(2)',

	'/\d{4}/\d{1,2}/\d{1,2}/post\-(\d+)\.html => forum_post(1)',

	"{$forums}\d{4}/\d{1,2}/t(\d+)/attaches/ => balancer_board_topic_attaches(2)",
	"{$forums}\d{4}/\d{1,2}/t(\d+)/attaches/(\d+)\.html => balancer_board_topic_attaches(2,3)",

	"{$forums}forum/punbb/viewtopic\.php\?pid=(\d+) => forum_post(2)",
	"{$forums}\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+)(,(\d+))?\-.+\.html\?? => redirect:forum_topic(2,4)",
	"{$forums}\d{4}/\d{1,2}/topic\-(\d+)(,(\d+))?\-\-.+\.html\?? => forum_topic(2,4)",
	"{$forums}\d{4}/\d{1,2}/t(\d+)(,(\d+))?\-\-.+\.html\?? => forum_topic(2,4)",
	"{$forums}\d{4}/\d{1,2}/t(\d+)(,(\d+))?\-.+\.html\?? => redirect:forum_topic(2,4)",
	"{$forums}\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+)(,(\d+))?\-\-.+ => redirect:forum_topic(2,4)",
	"{$forums}\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+),(\d+).* => redirect:forum_topic(2,3)",
	"{$forums}\d{4}/\d{1,2}/printable\-(\d+)\-\-.* => forum_printable(2)",
	"{$forums}\d{4}/\d{1,2}/\d{1,2}/printable\-(\d+)\-\-.* => redirect:forum_printable(2)",

	"{$forums}\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+),(last|new).* => forum_topic(2,3)",
	"{$forums}\d{4}/\d{1,2}/topic\-(\d+),(last|new).* => forum_topic(2,3)",
	"{$forums}\d{4}/\d{1,2}/t(\d+),(last|new).* => forum_topic(2,3)",

	"{$forums}\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+).* => forum_topic(2)",
	"{$forums}\d{4}/\d{1,2}/topic\-(\d+).* => forum_topic(2)",

	'/forum/topic/\d+/(\d+)/ => redirect:forum_topic(1)',
	'/forum/topic/\d+/(\d+),(\d+)/ => redirect:forum_topic(1,2)',

	'/test/ => airbase_main',

	'/user/(\d+)/personal\.js => forum_js_personal(1)',
	'/_bors/j/users/(\d+)/personal\.js => forum_js_personal(1)',
//	'/_bors/j/local/forum/topvisits\.js => forum_js_topvisits',

	'/user/(\d+)/setvars.js => forum_user_js_setvars(1)',
	'/js/users/touch.js\?(.+) => user_js_touch(1)',
	'/js/board/comments/(\d+)\.js => balancer_board_js_comments(1)',

	'(/admin/forum/posts/)move-tree => airbase_forum_admin_posts_movetree',

	'(/forum/)latest/ => airbase_board_show_latest',

   	'/ => balancer_main',

	'/_bors/ajax/thumb\-(up|down)\?object=(.+) => balancer_ajax_thumb_vote(2,vote=1)',
	'/_bors/ajax/post\-footer\-tools\?object=(.+) => balancer_board_posts_tools_footerAJAX(1)',

	'(.*)\?cdrop  => bors_admin_tools_clean(1)',

		'(/admin/users/(\d+)/)warnings.html\?object=(.+) => airbase_user_admin_warnings(2,object=3)',
		'(/admin/users/(\d+)/)warnings.html => airbase_user_admin_warnings(2)',
		'(/admin/forum/post/(\d+)/)as-new-topic => airbase_forum_admin_post_asnewtopic(2)',
		'(/admin/forum/post/(\d+)/)move-tree => forum_tools_post_moveTree(2)',
		'(/admin/forum/post/(\d+)/)do\-(\S+)\.bas => forum_tools_post_do(2,3)',

		'(/)blog/ => balancer_blog',
		'(/)blog/index.html => redirect:balancer_blog',
		'(/)blog/(\d+).html => balancer_blog(NULL,2)',

		'(/forum/)tools/topic/(\d+)/reload/? => forum_tools_topic_reload(2)',
		'(/forum/tools/topic/)(\d+)/ => forum_tools_topic(2)',
		'(/forum/tools/post/)(\d+)/ => forum_tools_post(2)',
		'/forum/user\-(\d+\-posts\-in\-topic\-\d+)/ => balancer_board_posts_userInTopic(1)',
		'/forum/user\-(\d+\-posts\-in\-topic\-\d+)/(\d+)\.html => balancer_board_posts_userInTopic(1,2)',

		'/\d{4}/\d{1,2}/\d{1,2}/forum\-(\d+)(,(\d+))?\-\-.+ => forum_forum(1,3)',
		'/\d{4}/\d{1,2}/\d{1,2}/forum\-(\d+)/? => forum_forum(1,3)',
		'(/)forum/(\d+)/ => redirect:forum_forum(2)',

		'/forum/viewtopic\.php\?pid=(\d+) => redirect:forum_post(1)',

		'(/img/forums/)(\d+)/index.bas => balancer_images_index(2)',
		'(/img/forums/)(\d+)/? => balancer_images_index(2)',

		'/js/users/reputation,(\d+)\.js => user_js_reputation(1,2)',
//		'/js/forum/topvisits\.js => forum_js_topvisits',

		'/\d{4}/\d{1,2}/\d{1,2}/category\-(\d+)\-\-.+ => forum_category(1)',
		'(/)forum/ => forum_main',
		'(/forum/(\d+)/)news/ => airbase_forum_news(2)',
		'(/forum/(\d+)/)news/(\d+)\.html => airbase_forum_news(2,3)',
		'(/forum/(\d+)/)posts\-rss\.xml => airbase_board_forum_prss(2)',

		'/games/ogame/calculators/ishka/ => balancer_ogame_calc_ishka',

		'(/tools/backlinks/)\?object=(\d+) => bors_referer_backlinks(2)',
		'/tools/backlinks/ => bors_referer_main',
		'/tools/votes/ => bors_votes_last',

		'(/tools/search/)result/ => bors_tools_search_result',
		'(/tools/)search/ => bors_tools_search',

		'(/user/(\d+)/)use\-topics\.html => airbase_user_topics(2)',
		'(/user/(\d+)/)use\-topics,(\d+)\.html => airbase_user_topics(2,3)',

		'/user/(\d+)/? => user_main(1)',

		'/user/(\d+)/setvars.js => forum_user_js_setvars(1)',

		'/user/(\d+)/blog/ => user_blog(1)',
		'/user/(\d+)/blog/index.html => redirect:user_blog(1)',
		'/user/(\d+)/blog/(\d+).html => user_blog(1,2)',
		'/user/(\d+)/blog\.html => redirect:user_blog(1)',
		'/user/(\d+)/blog(\-(\d+))\.html => redirect:user_blog(1,3)',
		'/user/(\d+)/blog/index\-(\d+)\.html => redirect:user_blog(1,2)',

		'/user/(\d+)/blog/rss.xml => user_blog_rss(1)',

		'(/user/(\d+)/)posts/ => user_posts(2)',
		'(/user/(\d+)/posts/)(\d+)/ => user_posts_year(2,3)',
		'/user/(\d+)/posts/(\d+/\d+)/ => user_posts_month(1,2)',
		'/user/(\d+)/posts/(\d+/\d+/\d+|last|first)/ => user_posts_day(1,2)',

		'/user/(\d+)/rep\.gif => user_image_reputation(1)',

		'/user/(\d+)/reputation/ => user_reputation(1)',
		'/user/(\d+)/reputation\.html => user_reputation(1)',
		'/user/(\d+)/reputation,(\d+)\.html => user_reputation(1,2)',
		'/user/(\d+)/reputation,(\d+)\.html\?(.*) => user_reputation(1,page=2,ref=3)',
		'/user/(\d+)/reputation\.html\?(.*) => user_reputation(1,ref=2)',
		'/user/(\d+)/reputation/\?(.+) => user_reputation(1,ref=2)',

		'(/user/(\d+)/)aliases\.html => airbase_user_aliases(2)',

#		'/user/(\d+)/reputation.* => forum_main',

		'(/)users/? => users_main',
		'(/users/)toprep/? => users_toprep',
		'(/users/)warnings/ => users_topwarnings',

//		'/user/(\d+)/warnings\.js => user_js_warnings(1)',
		'/user/(\d+)/warnings\.gif => forum_images_warnings(1)',
		'(/user/(\d+)/)warnings/ => airbase_user_warnings(2)',
		'(/users/(\d+)/)warnings/ => airbase_user_warnings(2)',
		'(/user/(\d+)/)warnings\.html/?object=.* => airbase_user_warnings(2)',
		'(/user/(\d+)/)warnings\.html => airbase_user_warnings(2)',
		'(/user/(\d+)/)warnings,(\d+)\.html => airbase_user_warnings(2,3)',

		'/users/images/rep\-map\.svg => balancer_users_images_repMap',

		'(/users/(\d+)/)votes/ => balancer_user_votes(2)',

		'(/bors/examples/)top-reputation/ => examples_topReputation',

		'(/)(test|crazy)/ => base_page_hts',
		'(/test/bors/)xml/ => bors_test_xml',

	'/users/reputation/last-ograph\.svg => balancer_board_users_reputationGraphSVG',

	'/forum/topics/(\d+)/reports/users-graph\.png => balancer_board_topic_usersGraphPng(1)',
	'/forum/topics/(\d+)/reports/users-graph\.svg => balancer_board_topic_usersGraphSVG(1)',
	'/forum/topics/(\d+)/reports/users-ograph\.svg => balancer_board_topic_usersGraphSVG(1,ordered=1)',

	'/lor/topics/(\d+)/reports/users-graph\.svg => lor_board_topic_usersGraphSVG(1)',
	'/lor/topics/(\d+)/reports/users-ograph\.svg => lor_board_topic_usersGraphSVG(1,ordered=1)',

	'/external/picasaweb/js/album/(\w+/\w+)/? => balancer_external_picasaweb_jsAlbum(1)',
	'/external/picasaweb/bb/album/(\w+/\w+)/? => balancer_external_picasaweb_bbAlbum(1)',

//	'.* => base_page_hts',
);
