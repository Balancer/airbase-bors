<?
	hts_data_prehandler("()(\d+)/reputation/(.*)", array(
			'body'		=> 'plugins_users_reputation_show_body',
			'title'		=> 'plugins_users_reputation_show_title',
			'nav_name'	=> 'plugins_users_reputation_show_nav_name',
			'template'	=> "{$_SERVER['DOCUMENT_ROOT']}/cms/templates/forum/forum.html",
		));

	function plugins_users_reputation_show_body($uri, $m)
	{
		require_once('funcs/modules/messages.php');

		$uid = $data['user_id']	= intval($m[2]);
		if(!$uid)
			return ec("Не задан ID пользователя.");

		$db = &new DataBase('USERS');
		
		$data['ref'] = @$m[3] or @$_SERVER['HTTP_REFERER'];
		
		$data['list'] = $db->get_array("SELECT * FROM reputation_votes WHERE user_id = $uid ORDER BY time DESC");

		$dbf = &new DataBase('punbb');
		$data['reputation_abs_value'] = sprintf("%.2f", $dbf->get("SELECT reputation FROM users WHERE id = $uid"));
		
		$data['plus']  = $db->get("SELECT COUNT(*) FROM reputation_votes WHERE user_id = $uid AND score > 0");
		$data['minus'] = $db->get("SELECT COUNT(*) FROM reputation_votes WHERE user_id = $uid AND score < 0");

        include_once("funcs/templates/assign.php");
        return template_assign_data("show.html", $data);
	}

	function plugins_users_reputation_show_title($uri, $m)
	{
		require_once('funcs/modules/messages.php');

		$uid = $data['user_id']	= intval($m[2]);
		if(!$uid)
			return ec("Не задан ID пользователя.");

		$us = new User($uid);

		return ec("Репутация участника ") . $us->data('name');
	}

	function plugins_users_reputation_show_nav_name($uri, $m)
	{
		require_once('funcs/modules/messages.php');

		$uid = $data['user_id']	= intval($m[2]);
		if(!$uid)
			return ec("Не задан ID пользователя.");

		$us = new User($uid);

		return $us->data('name');
	}
