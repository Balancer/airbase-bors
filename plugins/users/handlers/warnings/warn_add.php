<?
	hts_data_prehandler("()(\d+)/warn_add/", array(
			'body'		=> 'plugins_users_warn_add_body',
			'title'		=> ec("Выставить штраф"),
			'nav_name'	=> ec("штраф"),
			'template'	=> "{$_SERVER['DOCUMENT_ROOT']}/cms/templates/forum/forum.html",
		));

	function plugins_users_warn_add_body($uri, $m)
	{
		require_once('funcs/modules/messages.php');

		$uid = $data['user_id']	= intval($m[2]);
		if(!$uid)
			return ec("Не задан ID пользователя.");

		$me = new User();
		if(!in_array($me->data('group'), array(1,2,5,21)))
			return ec("У Вас недостаточно прав доступа");
			
		$us = new User($uid);
//		if(in_array($us->data('group'), array(1,2)))
//			return ec("Эта группа пользователей защищена от штрафования");
		
		$db = new DataBase('punbb');
		$count = $db->get("SELECT COUNT(*) FROM warnings WHERE user_id = $uid AND moderator_id = ".intval($me->data('id'))." AND time > ".(time()-86400));
		if($count>=3)
			return ec("Не больше 3 штрафов в день от одного модератора!");
			
//		if(check_access($us));
	
//		include("inc/common.php");
		
		$data['ref'] = urldecode(@$_GET['ref']) or @$_SERVER['HTTP_REFERER'];
		
		$data['active_warnings'] = $db->get_array("SELECT * FROM warnings WHERE user_id = $uid AND time > ".(time()-86400*30)." ORDER BY time DESC");
		$data['passive_warnings'] = $db->get_array("SELECT * FROM warnings WHERE user_id = $uid AND time < ".(time()-86400*30)." ORDER BY time DESC");

		$data['user_name'] = $us->data('name');

        include_once("funcs/templates/assign.php");
        return template_assign_data("warn_add.html", $data);
	}

    register_action('warn_add_do', 'plugins_users_warn_add_do');

    function plugins_users_warn_add_do($uri, $action)
	{

		require_once('funcs/modules/messages.php');

		$uid = intval($_POST['user_id']);
		if(!$uid)
			return error_message(ec("Не задан ID пользователя."));

		class_load('forum_user', $uid)->cache_clean_self();

		$me = new User();
		if(!in_array($me->data('group'), array(1,2,5,21)))
			return error_message(ec("У Вас недостаточно прав доступа"));
			
		$db = new DataBase('punbb');
		$count = $db->get("SELECT COUNT(*) FROM warnings WHERE user_id = $uid AND moderator_id = ".intval($me->data('id'))." AND time > ".(time()-86400));
		if($count>=3)
			return error_message(ec("Не больше 3 штрафов в день от одного модератора!"));

		$db = new DataBase('punbb');
		$db->insert('warnings', array(
			'user_id'		=> intval($_POST['user_id']),
			'time'			=> time(),
			'score'			=> 1,
			'moderator_id'	=> $me->data('id'),
			'uri'			=> $_POST['uri'],
			'comment'		=> $_POST['comment'],
		));

		include_once("funcs/navigation/go.php");
		if(!empty($_POST['ref']))
			go($_POST['ref']);
		
		return message(ec("Штраф выставлен"));
	}
