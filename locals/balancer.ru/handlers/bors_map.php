<?
    $map = array(
		"/\d{4}/\d{1,2}/\d{1,2}/topic\-(\d+)(,(\d+))?\-\-.+\.html" => 'forum/borsForumTopic(1,3)',
		"/\d{4}/\d{1,2}/\d{1,2}/forum\-(\d+)(,(\d+))?\-\-.+\.html" => 'forum/borsForum(1,3)',
		"/\d{4}/\d{1,2}/\d{1,2}/forum\-(\d+)/?" => 'forum/borsForum(1,3)',
		"/\d{4}/\d{1,2}/\d{1,2}/category\-(\d+)\-\-.+\.html" => 'forum/borsForumCategory(1)',
		"/\d{4}/\d{1,2}/\d{1,2}/post\-(\d+)\.html" => 'forum/borsForumPost(1)',
		"/user/(\d+)/personal\.js" => 'forum/userPersonalJS(1)',
		"/forum\-new/" => 'forum/borsForumMain',
		"/user/(\d+)/warnings\.gif" => 'forum/images/borsForumImageWarnings(1)',
		"/user/(\d+)/use-topics\.html" => 'users/borsUsersUseTopics(1)',
	);
