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

	'(.+\.htm) => airbase_images_show(1)',

	'/test/ => airbase_main',

	'/user/(\d+)/personal\.js => forum_js_personal(1)',
	'/_bors/j/users/(\d+)/personal\.js => forum_js_personal(1)',
	'/_bors/j/local/forum/topvisits\.js => forum_js_topvisits',

	'/user/(\d+)/setvars.js => forum_user_js_setvars(1)',
	'/js/users/touch.js\?(.+) => user_js_touch(1)',
	'/js/board/comments/(\d+)\.js => balancer_board_js_comments(1)',

	'(/admin/forum/posts/)move-tree => airbase_forum_admin_posts_movetree',

	'(/forum/)latest/ => airbase_board_show_latest',
);
