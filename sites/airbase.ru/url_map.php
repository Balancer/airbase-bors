<?php

// echo bors_debug::trace();

$topics_view_class = config('topics.view_class');

bors_vhost_routes('airbase.ru', [
	'/login/ => wrk_mauth_login',
	'/alpha/rus/n/nk/144/ => airbase_db_page(url)',
	'/ => airbase_main',

	'(/top/)(\d+)/ => aviatop_members_view(2)',

	'(/top/)logos/(\d+)\.png => airbase_top_logo(2)',
	'(/top/)\?img=(\d+) => airbase_top_logo(2)',
	'(/top/)(\d+)\.png => airbase_top_logo(2)',
	'(/top/)(\d+)/logo\.png => airbase_top_logo(2)',
	"(/forums/)index\.php\?showtopic=(\d+) => redirect:{$topics_view_class}(2)",
	"(/forums/)index\.php\?act=ST&f=\d+&t=(\d+) => redirect:{$topics_view_class}(2)",
	'(/)cgi\-bin/forum/ultimatebb\.cgi\?ubb=get_topic&(f=\d+&t=\d+) => forum_topic_ubb(2)',
	'/forum/Forum(\d+/HTML/\d+)\.html => forum_topic_ubb(1)',

	'(/forums/)index\.php\?showtopic=(\d+)&st=(\d+)&? => forum_topic_ipbst(2,3)',

	'(/)forums/? => base_page_redirect(NULL,go=http://forums.airbase.ru)',
	'(/)cgi\-bin/forum/ultimatebb\.cgi\?ubb=forum&f=(\d+) => redirect:forum_forum(2)',
	'(/)cgi\-bin/forum/ultimatebb\.cgi\?f=(\d+)&ubb=forum => redirect:forum_forum(2)',

	'(/)forum/? => base_page_redirect(NULL,go=http://forums.airbase.ru)',

	'(/)forum/(\d+/\d+)/? => forum_topic_ubb(2)',
	'(/)forum/(\d+/\d+)/index\.htm => forum_topic_ubb(2)',
	'(/)forum/(\d+/\d+)/(\d+)\.htm => forum_topic_ubb(2,3)',
	'(/)forum/(\d+/\d+)/(\d+)/?$ => forum_topic_ubb(2,3)',

	'(/news/\d{4}/\d{1,2}/\d{1,2}/)(\d+)\.html => airbase_news_page(2)',

	'(.*/)index\.htm => common_redirect(1)',
	'(.*/)index\-t\.htm => common_redirect(1)',

	'.* => airbase_pages_markdown(url)',

//	'.*viewtopic\.php\?id=(\d+)&p=(\d+).* => balancer_board_topic(1,2)',
//	'.*viewtopic\.php\?id=(\d+).* => balancer_board_topic(1)',
//	'(.*) => airbase_page(1,host=0)',

//	'(.*) => airbase_pages_db(1)',

	'/([^/]+)/ => airbase_keywords_old(1)',
	'/([^/]+) => airbase_keywords_old(1)',

//	'.*/\w+\.phtml => airbase_pages_hts(url)',
//	'.* => airbase_pages_hts(url)',
]);

/*
if(config('is_developer'))
{
	// Понять, почему не показыватся http://www.airbase.ru/top/logos/1.png
	bors_vhost_routes('airbase.ru', [
		'.* => airbase_pages_markdown(url)',
	]);
}
*/

// set_bors_project('airbase');

// bors_auto_class('!(.*/)files/?!', 'auto_files', 'bors_auto_files')
