<?php
    $map = array(

		'(/)blog/ => balancer_blog',
		'(/)blog/index.html => redirect:balancer_blog',
		'(/)blog/(\d+).html => balancer_blog(NULL,2)',

		'/\d{4}/\d{1,2}/\d{1,2}/forum\-(\d+)(,(\d+))?\-\-.+ => forum_forum(1,3)',
		'/\d{4}/\d{1,2}/\d{1,2}/forum\-(\d+)/? => forum_forum(1,3)',
		'(/)forum/(\d+)/ => redirect:forum_forum(2)',

		'(/forum/)tools/move_post_tree/(\d+)/? => forum_tools_post_moveTree(2)',
		'(/forum/)tools/topic/(\d+)/reload/? => forum_tools_topic_reload(2)',
		'(/forum/tools/topic/)(\d+)/ => forum_tools_topic(2)',
		'(/forum/tools/post/)(\d+)/ => forum_tools_post(2)',

		'/js/users/touch.js\?(.+) => user_js_touch(1)',
		'/js/users/reputation,(\d+)\.js => user_js_reputation(1,2)',
		'/js/forum/topvisits\.js => forum_js_topvisits',
		
		'/\d{4}/\d{1,2}/\d{1,2}/category\-(\d+)\-\-.+ => forum_category(1)',
		'/user/(\d+)/personal\.js => forum_js_personal(1)',
		'(/)forum/ => forum_main',
		'(/forum/(\d+)/)news/ => airbase_forum_news(2)',
		'(/forum/(\d+)/)news/(\d+)\.html => airbase_forum_news(2,3)',
		'(/user/(\d+)/)use\-topics\.html => airbase_user_topics(2)',
		'(/user/(\d+)/)use\-topics,(\d+)\.html => airbase_user_topics(2,3)',

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
		'/user/(\d+)/reputation\.html => user_reputation(1)',
		'/user/(\d+)/reputation,(\d+)\.html => user_reputation(1,2)',
		'/user/(\d+)/reputation,(\d+)\.html\?(.*) => user_reputation(1,page=2,ref=3)',
		'/user/(\d+)/reputation\.html\?(.*) => user_reputation(1,ref=2)',
		'/user/(\d+)/reputation/(.+) => user_reputation(1,ref=2)',

		'(/user/(\d+)/)aliases\.html => airbase_user_aliases(2)',
		
#		'/user/(\d+)/reputation.* => forum_main',

		'(/)users/? => users_main',
		'(/users/)toprep/? => users_toprep',
		'(/users/)warnings/ => users_topwarnings',

//		'/user/(\d+)/warnings\.js => user_js_warnings(1)',
		'/user/(\d+)/warnings\.gif => forum_images_warnings(1)',
		'(/user/(\d+)/)warnings/ => airbase_user_warnings(2)',
		'(/users/(\d+)/)warnings/ => airbase_user_warnings(2)',
//		'(/user/(\d+)/)warnings\.html/?object=.* => airbase_user_warnings(2)',
		'(/user/(\d+)/)warnings\.html => airbase_user_warnings(2)',
		'(/user/(\d+)/)warnings,(\d+)\.html => airbase_user_warnings(2,3)',

		'(/)(test|crazy)/ => base_page_hts',

		'(/admin/users/(\d+)/)warnings.html\?object=(.+) => airbase_user_admin_warnings(2,object=3)',
		'(/admin/users/(\d+)/)warnings.html => airbase_user_admin_warnings(2)',
		'(/admin/forum/post/(\d+)/)as-new-topic/ => airbase_forum_admin_post_asnewtopic(2)',
//	'.* => base_page_hts',
);
