<?php

bors_url_map(array(
	'/login/ => wrk_mauth_login',
));

$map = array(
	'/ => airbase_main',

	'(/top/)(\d+)/ => aviatop_member(2)',

	'(/top/)logos/(\d+)\.png => airbase_top_logo(2)',
	'(/top/)\?img=(\d+) => airbase_top_logo(2)',
	'(/top/)(\d+)\.png => airbase_top_logo(2)',
	'(/top/)(\d+)/logo\.png => airbase_top_logo(2)',
	'(/forums/)index\.php\?showtopic=(\d+) => redirect:forum_topic(2)',
	'(/forums/)index\.php\?act=ST&f=\d+&t=(\d+) => redirect:forum_topic(2)',
	'(/)cgi\-bin/forum/ultimatebb\.cgi\?ubb=get_topic&(f=\d+&t=\d+) => forum_topic_ubb(2)',
	'/forum/Forum(\d+/HTML/\d+)\.html => forum_topic_ubb(1)',

	'(/forums/)index\.php\?showtopic=(\d+)&st=(\d+)&? => forum_topic_ipbst(2,3)',

	'(/)forums/? => base_page_redirect(NULL,go=http://forums.airbase.ru)',
	'(/)cgi\-bin/forum/ultimatebb\.cgi\?ubb=forum&f=(\d+) => redirect:forum_forum(2)',
	'(/)cgi\-bin/forum/ultimatebb\.cgi\?f=(\d+)&ubb=forum => redirect:forum_forum(2)',

	'(/)forum/? => base_page_redirect(NULL,go=http://forums.airbase.ru)',

	'(/)forum/(\d+/\d+)/ => forum_topic_ubb(2)',
	'(/)forum/(\d+/\d+)/index\.htm => forum_topic_ubb(2)',
	'(/)forum/(\d+/\d+)/(\d+)\.htm => forum_topic_ubb(2,3)',

	'(.*/)index\.htm => common_redirect(1)',
	'(.*/)index\-t\.htm => common_redirect(1)',

//	'.*viewtopic\.php\?id=(\d+)&p=(\d+).* => forum_topic(1,2)',
//	'.*viewtopic\.php\?id=(\d+).* => forum_topic(1)',
//	'(.*) => base_page_hts(1,host=0)',
//	'(.*) => airbase_page(1,host=0)',

	'/([^/]+)/ => airbase_keywords_old(1)',
	'/([^/]+) => airbase_keywords_old(1)',
);
