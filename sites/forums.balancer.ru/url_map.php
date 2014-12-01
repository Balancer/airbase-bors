<?php

$topics_view_class = config('topics.view_class');

bors_url_map(array(
	'/best/ => balancer_board_posts_best',
	'/worst/? => balancer_board_posts_worst',
	'(/)worst/(\d+)\.html => balancer_board_posts_worst(NULL,2)',
	'(/)best/(\d+)\.html => balancer_board_posts_best(NULL,2)',
	'(/personal)(/?.*) => include(balancer_board_personal)',
	'/community/persons/ => balancer_board_forums_persons',
	'(/community/)persons/(\d+)\.html => balancer_board_forums_persons(NULL,2)',
	'/posts/popups/tools/?\?post=(\d+) => balancer_board_posts_popups_tools(1)',
//	'.* => balancer_board_pages_zim',
));

$map = array(
	'/ => balancer_board_main',
	'/index.html => balancer_board_main',
	'/index\-(\d+)\.html => balancer_board_main(1,1)',

	"/\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+)(,(\d+))?\-\-.+\.html => {$topics_view_class}(1,3)",
	'/\d{4}/\d{1,2}/\d{1,2}/forum\-(\d+)(,(\d+))?\-\-.+\.html => forum_forum(1,3)',
	'/\d{4}/\d{1,2}/\d{1,2}/forum\-(\d+)/? => forum_forum(1,3)',
	'/\d{4}/\d{1,2}/\d{1,2}/category\-(\d+)\-\-.+\.html => forum_category(1)',
	'/\d{4}/\d{1,2}/\d{1,2}/post\-(\d+)\.html => forum_post(1)',

	'/admin/ => balancer_board_admin_main',
	'/admin/users/ => balancer_board_users_admin',
	'/admin/users/untrusted-posts/ => balancer_board_users_untrustedPosts',
	'(/admin/users/)untrusted-posts/(\d+)\.html => balancer_board_users_untrustedPosts(NULL,2)',
	'/admin/users/lowest-rate/ => balancer_board_users_rateLowest',

	'/archive/ => balancer_board_archive_main',
	'(/archive/)(\d+)/ => balancer_board_archive_year(2)',
	'(/archive/)(\d+/\d+)/ => balancer_board_archive_month(2)',
	'(/archive/)(\d+/\d+/\d+)/ => balancer_board_archive_day(2)',

	'(/archive/posts/)(\d+/\d+/\d+)/ => balancer_board_archive_posts_day(2)',
	'(/archive/posts/)(\d+/\d+/\d+)/(\d+)\.html => balancer_board_archive_posts_day(2,3)',

	'/blogs/ => balancer_board_blogs_main',
	'(/)blogs/(\d+)\.html => balancer_board_blogs_main(NULL,2)',

	'/dashboard/? => balancer_board_dashboard',

	'/info/ => balancer_board_info_main',
	'/info/top\-forums\-by\-topics/ => balancer_board_info_topForumsByTopics',

	'(/)new\-topics/ => balancer_board_new_topics',
	'(/)new\-topics/(\d+)\.html => balancer_board_new_topics(NULL,2)',

	'/post => balancer_board_post_edit',

	'/stat/forums-activity\.png => balancer_board_stat_forumsActivity',
	'/stat/forums-activity-year\.png => balancer_board_stat_forumsActivityYear',

	'(/)tags/? => balancer_board_keywords_main',
	'(/tags.*/)\*/? => balancer_board_keywords_list',
	'(/tags/)(.+)/(\d+)\.html => balancer_board_keywords_tags(2,3)',
	'(/tags/)(.+)/? => balancer_board_keywords_tags(2)',

	'/topics/(\d+)/votes-map\.svg => balancer_board_topics_votesGraphSVG(1)',

	'/new\-topics/10years-ago/ => balancer_board_topics_10years',

	'/users?/(\d+)/personal\.js => forum_js_personal(1)',
	'/users?/(\d+)/warnings\.gif => forum_images_warnings(1)',
	'/users?/(\d+)/use-topics\.html => users_UseTopics(1)',
	'/users?/(\d+)/blog/ => user_blog(1)',
	'/users?/(\d+)/blog/index.html => user_blog(1)',
	'/users?/(\d+)/blog/(\d+).html => user_blog(1,2)',
	'/users?/(\d+)/blog\.html => user_blog(1)',
	'/users?/(\d+)/blog(\-(\d+))\.html => user_blog(1,3)',
	'/users?/(\d+)/reputation/ => user_reputation(1)',
	'/users?/(\d+)/reputation/(.+) => user_reputation(1,2)',
	'/users?/(\d+)/reputation\.html => user_reputation(1)',
	'/users?/(\d+)/reputation\.html(.+) => user_reputation(1,2)',

	'/users/(\d+)/votes/lastgraph\.svg => balancer_board_users_votes_lastgraph(1)',

	'(/users/)(\d+)/? => user_main(2)',

	'/\d{4}/\d{1,2}/\d{1,2}/printable\-(\d+)\-\-.+\.html => forum_printable(1)',

	'/warnings/? => balancer_board_warnings_main',
	'(/)warnings/(\d+)\.html => balancer_board_warnings_main(NULL,2)',
);
