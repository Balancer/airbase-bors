<?
	hts_data_prehandler("()(\d+)/$", array(
			'body'		=> 'plugins_user_main_body',
			'title'		=> 'plugins_user_main_title',
			'nav_name'	=> 'plugins_user_main_nav_name',
			'template'	=> "{$_SERVER['DOCUMENT_ROOT']}/cms/templates/forum/forum.html",
		));

	function plugins_user_main_body($uri, $m)
	{
		require_once('funcs/modules/messages.php');

		$uid = $data['user_id']	= intval($m[2]);
		if(!$uid)
			return ec("Не задан ID пользователя.");

		$us = &new User($uid);
		$data['name'] = $us->get('name');
		$data['uid'] = $uid;

        include_once("funcs/templates/assign.php");
        return template_assign_data("main.html", $data);
	}

	function plugins_user_main_title($uri, $m)
	{
		require_once('funcs/modules/messages.php');

		$uid = $data['user_id']	= intval($m[2]);
		if(!$uid)
			return ec("Не задан ID пользователя.");

		$us = &new User($uid);

		return ec("Личная страница ") . $us->data('name');
	}

	function plugins_user_main_nav_name($uri, $m)
	{
		require_once('funcs/modules/messages.php');

		$uid = $data['user_id']	= intval($m[2]);
		if(!$uid)
			return ec("Не задан ID пользователя.");

		$us = &new User($uid);

		return $us->data('name');
	}
