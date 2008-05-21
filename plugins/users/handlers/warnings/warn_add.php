<?

	hts_data_prehandler("()(\d+)/warn_add/", array(
			'body'		=> 'plugins_users_warn_add_body',
			'title'		=> ec("Выставить штраф"),
			'nav_name'	=> ec("штраф"),
			'template'	=> "forum/forum.html",
		));

	function plugins_users_warn_add_body($uri, $m)
	{
		$uid = $data['user_id']	= intval($m[2]);
		if(!$uid)
			return ec("Не задан ID пользователя.");

		$me = bors()->user();
		if(!in_array($me->group_id(), array(1,2,5,21)))
			return ec("У Вас недостаточно прав доступа");
			
		$us = object_load('forum_user', $uid);
//		if(in_array($us->data('group'), array(1,2)))
//			return ec("Эта группа пользователей защищена от штрафования");
		
		$db = new DataBase('punbb');
		$count = $db->get("SELECT COUNT(*) FROM warnings WHERE user_id = $uid AND moderator_id = ".intval($me->id())." AND time > ".(time()-86400));
		if($count>=3)
			return ec("Не больше 3 штрафов в день от одного модератора!");

		$count = $db->get("SELECT COUNT(*) FROM warnings WHERE user_id = $uid AND moderator_id = ".intval($me->id())." AND time > ".(time()-86400*WARNING_DAYS));
		if($count>=4)
			return ec("Не больше 4 штрафов от одного модератора!");
			
//		if(check_access($us));
	
//		include("inc/common.php");
		
		$data['ref'] = urldecode(@$_GET['ref']) or @$_SERVER['HTTP_REFERER'];
		
		$data['active_warnings'] = objects_array('airbase_user_warning', array('user_id=' => $uid, 'time>' => time()-86400*WARNING_DAYS, 'order' => '-time'));
		$data['passive_warnings'] = objects_array('airbase_user_warning', array('user_id=' => $uid, 'time<=' => time()-86400*WARNING_DAYS, 'order' => '-time'));

		$data['user_name'] = $us->title();

        include_once("engines/smarty/assign.php");
        return template_assign_data("warn_add.html", $data);
	}

    register_action('warn_add_do', 'plugins_users_warn_add_do');

    function plugins_users_warn_add_do($uri, $action)
	{
		$uid = intval($_POST['user_id']);
		if(!$uid)
			return bors_message(ec("Не задан ID пользователя."));

		class_load('forum_user', $uid)->cache_clean_self();

		$me = bors()->user();
		if(!in_array($me->group_id(), array(1,2,5,21)))
			return bors_message(ec("У Вас недостаточно прав доступа"));
			
		$db = new DataBase('punbb');
		$count = $db->get("SELECT COUNT(*) FROM warnings WHERE user_id = $uid AND moderator_id = ".intval($me->id())." AND time > ".(time()-86400));
		if($count>=3)
			return bors_message(ec("Не больше 3 штрафов в день от одного модератора!"));

		$count = $db->get("SELECT COUNT(*) FROM warnings WHERE user_id = $uid AND moderator_id = ".intval($me->id())." AND time > ".(time()-86400*WARNING_DAYS));
		if($count>=4)
			return bors_message(ec("Не больше 4 штрафов от одного модератора!"));

		$db = new DataBase('punbb');
		$db->insert('warnings', array(
			'user_id'		=> intval($_POST['user_id']),
			'time'			=> time(),
			'score'			=> 1,
			'moderator_id'	=> $me->id(),
			'moderator_name'=> $me->title(),
			'uri'			=> $_POST['uri'],
			'comment'		=> $_POST['comment'],
		));

		@unlink("/var/www/balancer.ru/htdocs/user/{$uid}/warnings.gif");

		include_once("funcs/navigation/go.php");
		if(!empty($_POST['ref']))
			go($_POST['ref']);
		
		return message(ec("Штраф выставлен"));
	}
