<?php

$forums = '(/|/support/|/tech/forum/|/community/|/society/|/socionics/forum/|/forum/)';

$map = array(
	'.*/\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+)\-rss\.xml => forum_topic_rss(1)',
	'.*/\d{4}/\d{1,2}/topic\-(\d+)\-rss\.xml => forum_topic_rss(1)',
//	'.* => base_page_hts',
//	'.* => page_fs_separate', - Временно отключено по причине ошибок определения загруженности страницы.
		
	'/do\-login/ => common_do_login',
	'/actions/do\-logout/ => common_do_logout',
		
	'.*viewtopic\.php\?id=(\d+)&p=(\d+).* => forum_topic(1,2)',
	'.*viewtopic\.php\?id=(\d+).* => forum_topic(1)',
	'.*viewforum\.php\?id=(\d+).* => forum_forum(1)',
	'.*/index.php => forum_main',

	'(/forum/)topic/\d+/(\d+),new/ => forum_topic(2)',

	'/\d{4}/\d{1,2}/\d{1,2}/post\-(\d+)\.html => forum_post(1)',
	"{$forums}forum/punbb/viewtopic\.php\?pid=(\d+) => forum_post(2)",
	"{$forums}\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+)(,(\d+))?\-\-.+\.html\?? => forum_topic(2,4)",
	"{$forums}\d{4}/\d{1,2}/topic\-(\d+)(,(\d+))?\-\-.+\.html\?? => forum_topic(2,4)",
	"{$forums}\d{4}/\d{1,2}/t(\d+)(,(\d+))?\-\-.+\.html\?? => forum_topic(2,4)",
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
	'/js/users/touch.js\?(.+) => user_js_touch(1)',
);
