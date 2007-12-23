<?php
	$forums = '(/|/support/|/tech/forum/|/community/|/society/|/socionics/forum/)';

    $map = array(

		'/\d{4}/\d{1,2}/\d{1,2}/forum\-(\d+)(,(\d+))?\-\-.+ => forum_forum(1,3)',
		'/\d{4}/\d{1,2}/\d{1,2}/forum\-(\d+)/? => forum_forum(1,3)',
		'(/)forum/(\d+)/ => redirect:forum_forum(2)',

		'(/forums/)tools/move_post_tree/(\d+)/? => forum_tools_post_moveTree(2)',
		
		'/\d{4}/\d{1,2}/\d{1,2}/category\-(\d+)\-\-.+ => forum_category(1)',
		'/user/(\d+)/personal\.js => forum_js_personal(1)',
		'/user/(\d+)/warnings\.js => user_js_warnings(1)',
		'/forum\-new/ => forum_main',
		'/user/(\d+)/warnings\.gif => forum_images_warnings(1)',
		'/user/(\d+)/use-topics\.html => users_UseTopics(1)',

		'/user/(\d+)/? => user_main(1)',
		'(/user/(\d+)/)test/ => user_test(2)',

		'/user/(\d+)/blog/ => user_blog(1)',
		'/user/(\d+)/blog/index.html => redirect:user_blog(1)',
		'/user/(\d+)/blog/(\d+).html => user_blog(1,2)',
		'/user/(\d+)/blog\.html => redirect:user_blog(1)',
		'/user/(\d+)/blog(\-(\d+))\.html => redirect:user_blog(1,3)',
		'/user/(\d+)/blog/index\-(\d+)\.html => redirect:user_blog(1,2)',

		'/user/(\d+)/blog/rss.xml => user_blog_rss(1)',

		'/user/(\d+)/posts/ => user_posts(1)',
		'/user/(\d+)/posts/index.html => user_posts(1)',
		'/user/(\d+)/posts/index\-(\d+)\.html => user_posts(1,2)',
		'/user/(\d+)/posts/(\d+)\.html => user_posts(1,2)',

		'/user/(\d+)/rep\.gif => user_image_reputation(1)',

		'/user/(\d+)/reputation/ => user_reputation(1)',
		'/user/(\d+)/reputation/(.+) => user_reputation(1,2)',
		'/user/(\d+)/reputation\.html => user_reputation(1)',
		'/user/(\d+)/reputation\.html(.+) => user_reputation(1,2)',

#		'/user/(\d+)/reputation.* => forum_main',

		'(/)users/? => users_main',
		'(/users/)toprep/? => users_toprep',

		'(/forum/)topic/\d+/(\d+),new/ => forum_topic(2)',

		'/\d{4}/\d{1,2}/\d{1,2}/post\-(\d+)\.html => forum_post(1)',
		"{$forums}forum/punbb/viewtopic\.php\?pid=(\d+) => forum_post(2)",
		
		'(/|/support/|/tech/forum/|/community/|/society/|/socionics/forum/)\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+)(,(\d+))?\-\-.+\.html => forum_topic(2,4)',
		'(/|/support/|/tech/forum/|/community/|/society/|/socionics/forum/)\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+)(,(\d+))?\-\-.+ => redirect:forum_topic(2,4)',
		'(/|/support/|/tech/forum/|/community/|/society/|/socionics/forum/)\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+),(\d+).* => redirect:forum_topic(2,3)',
		'(/|/support/|/tech/forum/|/community/|/society/|/socionics/forum/)\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+).* => redirect:forum_topic(2)',
		'(/|/support/|/tech/forum/|/community/|/society/|/socionics/forum/)\d{4}/\d{1,2}/\d{1,2}/printable\-(\d+)\-\-.+ => forum_printable(2)',

		'/forum/topic/\d+/(\d+)/ => redirect:forum_topic(1)',
		'/forum/topic/\d+/(\d+),(\d+)/ => redirect:forum_topic(1,2)',

		'(/)users/warning/(\d+)/ => users_warning(2)',

		'(/)(test|crazy)/ => base_page_hts',
	);
