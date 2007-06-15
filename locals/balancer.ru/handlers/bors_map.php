<?
    $map = array(
		"/\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+)(,(\d+))?\-\-.+\.html" => 'forum_topic(1,3)',
		"/\d{4}/\d{1,2}/\d{1,2}/forum\-(\d+)(,(\d+))?\-\-.+\.html" => 'forum_forum(1,3)',
		"/\d{4}/\d{1,2}/\d{1,2}/forum\-(\d+)/?" => 'forum_forum(1,3)',
		"/\d{4}/\d{1,2}/\d{1,2}/category\-(\d+)\-\-.+\.html" => 'forum_category(1)',
		"/\d{4}/\d{1,2}/\d{1,2}/post\-(\d+)\.html" => 'forum_post(1)',
		"/user/(\d+)/personal\.js" => 'forum_userPersonalJS(1)',
		"/forum\-new/" => 'forum_main',
		"/user/(\d+)/warnings\.gif" => 'forum_images_warnings(1)',
		"/user/(\d+)/use-topics\.html" => 'users_UseTopics(1)',
	);
