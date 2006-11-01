<?
    register_handler("(\d+)/avatarblock/", 'balancer_plugins_user_avatar_block_handler');

    function balancer_plugins_user_avatar_block_handler($uri, $m)
	{
		$db = new DataBase('punbb');
		
		$user_id = intval($m[1]);
		$pun_config['root_uri'] = "http://balancer.ru/forum/punbb";
		$pun_config['o_avatars_dir'] = "/var/www/balancer.ru/htdocs/forum/punbb/img/avatars/";
		$pun_config['o_avatars_uri'] = "http://balancer.ru/forum/punbb/img/avatars/";
		
		if(!$user_id)
			return error_message(ec("Неверный номер пользоваетеля"));
		
		$ud = $db->get("SELECT * FROM users WHERE id = $user_id");

		$user_avatar = '';
		
		$username = htmlspecialchars($ud['username']);
		$userlink = "<a href=\"{$pun_config['root_uri']}/profile.php?id={$ud['id']}\">".$username.'</a>';

		define('PUN_UNVERIFIED', 3);
		define('PUN_ADMIN', 1);
		define('PUN_MOD', 2);
		define('PUN_GUEST', 3);
		define('PUN_MEMBER', 4);
		include_once("/var/www/balancer.ru/htdocs/forum/punbb/include/functions.php");
		$user_title = get_title($ud);

		if($ud['use_avatar'] == '1')
		{
			foreach(split(' ','gif jpg png') as $ext)
				if($img_size = @getimagesize("{$pun_config['o_avatars_dir']}$user_id.$ext"))
				{
					$user_avatar = "<img src=\"{$pun_config['o_avatars_uri']}$user_id.$ext\" {$img_size[3]} alt=\"\" />";
					break;
				}
		}
		else
			$user_avatar = '';

		$user_warn_count	= intval($db->get("SELECT COUNT(*) FROM warnings WHERE user_id = $user_id AND time > ".(time()-30*86400)));
		$user_warn = "";
		if($user_warn_count)
		{
			$user_warn .= str_repeat("<img src=\"http://balancer.ru/img/web/cross.gif\" width=\"16\" height=\"16\" border=\"0\">", intval($user_warn_count/2));

			if($user_warn_count % 2)
				$user_warn .= "<img src=\"http://balancer.ru/img/web/cross-half.gif\" width=\"16\" height=\"16\" border=\"0\">";

			if(intval($user_warn_count/2+0.5) < 5)
				$user_warn .= str_repeat("<img src=\"http://balancer.ru/coppermine/images/flags/blank.gif\" width=\"16\" height=\"16\" border=\"0\">", 5-intval($user_warn_count/2+0.5));
	
			if($user_warn_count >= 10)
				$user_warn .= "<div style=\"font-size: 6pt; color: red;\">R/O до ".strftime("%y-%m-%d", 30*86400+$db->get("SELECT MIN(`time`) FROM warnings WHERE user_id = user_id AND time > ".(time()-30*86400)." LIMIT 10"))."</div>";
		}

		$data = array();
		foreach(split(' ', 'username user_avatar user_id userlink user_title user_warn') as $key)
			$data[$key] = $$key;

        include_once("funcs/templates/assign.php");
        echo template_assign_data("avatarblock.html", $data);
		return true;
	}
