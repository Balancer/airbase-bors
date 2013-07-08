<?php
    $map = array(

		'/\d{4}/\d{1,2}/\d{1,2}/forum\-(\d+)(,(\d+))?\-\-.+ => forum_forum(1,3)',
		'/\d{4}/\d{1,2}/\d{1,2}/forum\-(\d+)/? => forum_forum(1,3)',
		'(/)forum/(\d+)/ => redirect:forum_forum(2)',

		'(/forum/)tools/move_post_tree/(\d+)/? => forum_tools_post_moveTree(2)',
		'(/forum/)tools/topic/(\d+)/reload/? => forum_tools_topic_reload(2)',
		'(/forum/tools/topic/)(\d+)/ => forum_tools_topic(2)',
		'(/forum/tools/post/)(\d+)/ => forum_tools_post(2)',

//		'/js/users/touch.js\?(.+) => user_js_touch(1)',
//		'/js/forum/topvisits.js => forum_js_topvisits',

		'/\d{4}/\d{1,2}/\d{1,2}/category\-(\d+)\-\-.+ => forum_category(1)',
		'/users?/(\d+)/personal\.js => forum_js_personal(1)',
		'(/)forum/ => forum_main',
		'/users?/(\d+)/use-topics\.html => users_UseTopics(1)',

		'/users?/(\d+)/? => user_main(1)',
		'(/users?/(\d+)/)test/ => user_test(2)',

		'/users?/(\d+)/blog/ => user_blog(1)',
		'/users?/(\d+)/blog/index.html => redirect:user_blog(1)',
		'/users?/(\d+)/blog/(\d+).html => user_blog(1,2)',
		'/users?/(\d+)/blog\.html => redirect:user_blog(1)',
		'/users?/(\d+)/blog(\-(\d+))\.html => redirect:user_blog(1,3)',
		'/users?/(\d+)/blog/index\-(\d+)\.html => redirect:user_blog(1,2)',

		'/users?/(\d+)/blog/rss.xml => user_blog_rss(1)',

		'/users?/(\d+)/posts/ => user_posts(1)',
		'/users?/(\d+)/posts/index.html => user_posts(1)',
		'/users?/(\d+)/posts/index\-(\d+)\.html => user_posts(1,2)',
		'/users?/(\d+)/posts/(\d+)\.html => user_posts(1,2)',

		'/users?/(\d+)/rep\.gif => user_image_reputation(1)',

		'/users?/(\d+)/reputation/ => user_reputation(1)',
		'/users?/(\d+)/reputation/(.+) => user_reputation(1,2)',
		'/users?/(\d+)/reputation\.html => user_reputation(1)',
		'/users?/(\d+)/reputation\.html?(.*) => user_reputation(1,2)',

#		'/users?/(\d+)/reputation.* => forum_main',

		'(/)users/? => users_main',
		'(/users/)toprep/? => users_toprep',

		'/users?/(\d+)/warnings\.js => user_js_warnings(1)',
		'/users?/(\d+)/warnings\.gif => forum_images_warnings(1)',
		'(/)users/warning/(\d+)/ => users_warning(2)',
);
