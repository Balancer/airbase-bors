<?php

// Общий для всех сайтов роутинг

$forums = '(/|/support/|/tech/forum/|/community/|/society/|/socionics/forum/|/forum/)';

$topic_view_class = config('topics.view_class');

bors_url_map([
	'/bal/login/?  => bal_login',	// Страница авторизации
	'/bal/logout/? => bal_logout',

	'/do/logout/ => bal_do_logout',
	'/do/login/ => bal_do_login',
	'/users/private/hactions/(\w+) => bors_user_hactions_dispatcher(1)',

	"{$forums}\d{4}/\d{1,2}/t(\d+),new.* => balancer_board_topics_go_new(2)",
	"{$forums}\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+),new.* => balancer_board_topics_go_new(2)",
	"{$forums}\d{4}/\d{1,2}/topic\-(\d+),new.* => balancer_board_topics_go_new(2)",
]);

// Leagcy, ссылки старого формата
bors_url_map(array(
	// http://forums.airbase.ru/index.php/topic,37641.msg701468/topicseen.html#msg701468
	// 	via http://www.balancer.ru/g/p701470
	'/index\.php/topic,(\d+)\.msg(\d+)/topicseen\.html => balancer_board_post(2)',
));

$map = array(
	'/g/(.+) => bors_system_go_redirect(1)',

	'(/admin/forum/posts/)move-tree => airbase_forum_admin_posts_movetree',
	'.*/ => airbase_page_hts_plain',
//	'.*/ => airbase_page_hts_plainu',
	'.* => airbase_pages_zim',
	'/_bors/ajax/post\-footer\-tools\?object=(.+) => balancer_board_posts_tools_footerAJAX(1)',
	'/_bors/ajax/post/info\?post=(.+) => balancer_board_post_ajax_info(1)',
	'/_bors/ajax/thumb\-(up|down)\?object=(.+) => balancer_ajax_thumb_vote(2,vote=1)',

	'/_bors/ajax/types/pttools\?id=(.+) => balancer_board_posts_tools_title(1)',

	'/_bors/j/local/forum/topvisits\.js => forum_js_topvisits',
	'/_bors/j/users/(\d+)/personal\.js => forum_js_personal(1)',
	'/_bors/local/search/result/ => bors_tools_search_result',

	'/ajax/forums/list/? => balancer_board_ajax_forums_list',

	'/\d{4}/\d{1,2}/\d{1,2}/post\-(\d+)\.html => balancer_board_post(1)',
	'.*/\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+)\-rss\.xml => forum_topic_rss(1)',
	'.*/\d{4}/\d{1,2}/topic\-(\d+)\-rss\.xml => forum_topic_rss(1)',
	'(/)forum(\d+)/ => redirect:forum_forum(2)',
	'(/forum/)latest/ => airbase_board_show_latest',

	"{$forums}post\?(.+) => balancer_board_post_edit(2)",

	"{$forums}\d{4}/\d{1,2}/t(\d+)/attaches/ => balancer_board_topic_attaches(2)",
	"{$forums}\d{4}/\d{1,2}/t(\d+)/attaches/(\d+)\.html => balancer_board_topic_attaches(2,3)",

	"{$forums}\d{4}/\d{1,2}/\d{1,2}/printable\-(\d+)\-\-.* => redirect:forum_printable(2)",
	"{$forums}\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+)(,(\d+))?\-.+\.html\?? => redirect:{$topic_view_class}(2,4)",
	"{$forums}\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+),(\d+).* => redirect:{$topic_view_class}(2,3)",
	"{$forums}\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+)(,(\d+))?\-\-.+ => redirect:{$topic_view_class}(2,4)",
	"{$forums}\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+).* => {$topic_view_class}(2)",

	"{$forums}\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+),(last).* => {$topic_view_class}(2,3)",
	"{$forums}\d{4}/\d{1,2}/topic\-(\d+),(last).* => {$topic_view_class}(2,3)",
	"{$forums}\d{4}/\d{1,2}/t(\d+),(last).* => {$topic_view_class}(2,3)",

	"{$forums}\d{4}/\d{1,2}/p(\d+)\.html\?? => balancer_board_posts_show(2)",

	"{$forums}\d{4}/\d{1,2}/printable\-(\d+)\-\-.* => forum_printable(2)",
	"{$forums}\d{4}/\d{1,2}/tpc(\d+)\-\-.* => balancer_board_topics_printableCurrent(2)",
	"{$forums}\d{4}/\d{1,2}/tpc(\d+),(\d+)\-\-.* => balancer_board_topics_printableCurrent(2,3)",
	"{$forums}\d{4}/\d{1,2}/t(\d+)\-\-.*\.pdf => balancer_board_topics_pdf(2)",
	"{$forums}\d{4}/\d{1,2}/tpdfhelper\-(\d+)\-\-.*\.html => balancer_board_topics_pdfHelper(2)",
	"{$forums}\d{4}/\d{1,2}/tpdfcover\-(\d+)\-\-.*\.html => balancer_board_topics_pdfCover(2)",
	"{$forums}\d{4}/\d{1,2}/t(\d+)(,(\d+))?\-\-.+\.html\?? => {$topic_view_class}(2,4)",
	"{$forums}\d{4}/\d{1,2}/t(\d+)(,(\d+))?\-.+\.html\?? => redirect:{$topic_view_class}(2,4)",

	"{$forums}\d{4}/\d{1,2}/t(\d+)/blog/? => balancer_board_topics_blog(2)",
	"{$forums}\d{4}/\d{1,2}/t(\d+)/blog/(\d+)\.html => balancer_board_topics_blog(2,3)",
	"{$forums}\d{4}/\d{1,2}/t(\d+)/images/? => balancer_board_topics_images(2)",
	"{$forums}\d{4}/\d{1,2}/t(\d+)/images/(\d+)\.html => balancer_board_topics_images(2,3)",
	"{$forums}\d{4}/\d{1,2}/t(\d+)/video/? => balancer_board_topics_video(2)",
	"{$forums}\d{4}/\d{1,2}/t(\d+)/video/(\d+)\.html => balancer_board_topics_video(2,3)",

	"{$forums}\d{4}/\d{1,2}/topic\-(\d+)(,(\d+))?\-\-.+\.html\?? => {$topic_view_class}(2,4)",
	"{$forums}\d{4}/\d{1,2}/topic\-(\d+).* => {$topic_view_class}(2)",
	"{$forums}forum/punbb/viewtopic\.php\?pid=(\d+) => forum_post(2)",
	"{$forums}viewtopic\.php\?id=(\d+) => {$topic_view_class}(2)",

	"/posts/ajax/tools/(\d+)/? => balancer_board_posts_ajax_tools(1)",

	"/forum/topic/\d+/(\d+),(\d+)/ => redirect:{$topic_view_class}(1,2)",
	"(/forum/)topic/\d+/(\d+),new/ => {$topic_view_class}(2)",

	"/forum/topic/\d+/(\d+)/ => redirect:{$topic_view_class}(1)",
	'(.+\.htm) => airbase_images_show(1)',
	'.*/index.php => forum_main',
	'/js/board/comments/(\d+)\.js => balancer_board_js_comments(1)',
	'/js/users/touch.js\?(.+?)&.+ => user_js_touch(1)',
	'/js/users/touch.js\?(.+) => user_js_touch(1)',

	'/js/postload\.js => balancer_board_js_postload',

//	'.* => page_fs_separate', - Временно отключено по причине ошибок определения загруженности страницы.

//	'/test/ => airbase_main',
	'/tools/search/ => bors_tools_search',
	'/tools/search/result/ => bors_tools_search_result',
	'/user/(\d+)/personal\.js => forum_js_personal(1)',
	'/user/(\d+)/setvars.js => forum_user_js_setvars(1)',
	'/_bal/js/private/feed\.js => bal_user_private_feed',
	'/_bal/ajax/body\?object=(.+) => bal_ajax_body(1)',
	'.*viewforum\.php\?id=(\d+).* => forum_forum(1)',
	".*viewtopic\.php\?id=(\d+).* => {$topic_view_class}(1)",
	".*viewtopic\.php\?id=(\d+)&p=(\d+).* => {$topic_view_class}(1,2)",
	'/\w{32}/cache(/.*/\d*x\d*/[^/]+\.(jpe?g|png|gif)) => bors_image_autothumb(1)',
	'/\w{32}/cache(/.*/\d*x\d*\([^)]+\)/[^/]+\.(jpe?g|png|gif)) => bors_image_autothumb(1)',
	'/cache(/sites/.*/\d*x\d*/[^/]+) => bors_image_autothumb(1)',
	'.*/\w+\.phtml => airbase_page_hts_plain',
	'.*/\w+\.phtml => airbase_page_hts_plainu',

//	'.* => airbase_page_hts(url)',
	'.* => airbase_pages_zim',
	'.* => airbase_files_webroot',
);

//if(!empty($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] != 'www.tanzpol.org')
//	$map[] = '.* => bal_pages_hts(url)';

$map[] = '.* => bors_page_fs_htsu(url)'; //TODO: снести нафиг после конвертации старых hts Авиабазы

