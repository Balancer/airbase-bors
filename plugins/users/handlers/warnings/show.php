<?
	hts_data_prehandler("()(\d+)/warnings/", array(
			'body'		=> 'plugins_users_warn_show_body',
			'title'		=> 'plugins_users_warn_show_title',
			'nav_name'	=> 'plugins_users_warn_show_nav_name',
			'template'	=> "{$_SERVER['DOCUMENT_ROOT']}/cms/templates/forum/forum.html",
		));

	function plugins_users_warn_show_body($uri, $m)
	{
		require_once('funcs/modules/messages.php');

		$uid = $data['user_id']	= intval($m[2]);
		if(!$uid)
			return ec("Не задан ID пользователя.");

		$db = new DataBase('punbb');
		
		$data['ref'] = urldecode(@$_GET['ref']) or @$_SERVER['HTTP_REFERER'];
		
		$data['active_warnings'] = $db->get_array("SELECT * FROM warnings WHERE user_id = $uid AND time > ".(time()-86400*30)." ORDER BY time DESC");
		$data['passive_warnings'] = $db->get_array("SELECT * FROM warnings WHERE user_id = $uid AND time < ".(time()-86400*30)." ORDER BY time DESC");

        include_once("engines/smarty/assign.php");
        return template_assign_data("show.html", $data);
	}

	function plugins_users_warn_show_title($uri, $m)
	{
		require_once('funcs/modules/messages.php');

		$uid = $data['user_id']	= intval($m[2]);
		if(!$uid)
			return ec("Не задан ID пользователя.");

		$us = new User($uid);

		return ec("Штрафы пользователя ") . $us->data('name');
	}

	function plugins_users_warn_show_nav_name($uri, $m)
	{
		require_once('funcs/modules/messages.php');

		$uid = $data['user_id']	= intval($m[2]);
		if(!$uid)
			return ec("Не задан ID пользователя.");

		$us = new User($uid);

		return $us->data('name');
	}
?>
