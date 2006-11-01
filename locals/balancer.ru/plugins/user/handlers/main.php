<?
	hts_data_prehandler("()(\d+)/blog/", array(
			'body'		=> 'plugins_user_blog_body',
			'title'		=> 'plugins_user_blog_title',
			'nav_name'	=> 'plugins_user_blog_nav_name',
			'template'	=> "{$_SERVER['DOCUMENT_ROOT']}/cms/templates/forum/forum.html",
		));

	function plugins_user_blog_body($uri, $m)
	{
		require_once('funcs/modules/messages.php');

		$uid = $data['user_id']	= intval($m[2]);
		if(!$uid)
			return ec("Не задан ID пользователя.");

        return lcml("[module show/lenta/personal user_id=\"$uid\" limit=\"50\"]");
	}

	function plugins_user_blog_title($uri, $m)
	{
		require_once('funcs/modules/messages.php');

		$uid = $data['user_id']	= intval($m[2]);
		if(!$uid)
			return ec("Не задан ID пользователя.");

		$us = new User($uid);

		return ec("Блог ") . $us->data('name');
	}

	function plugins_user_blog_nav_name($uri, $m)
	{
		require_once('funcs/modules/messages.php');

		$uid = $data['user_id']	= intval($m[2]);
		if(!$uid)
			return ec("Не задан ID пользователя.");

		$us = new User($uid);

		return $us->data('name');
	}
