<?

register_action('do-login', 'handler_action_do_login');

function handler_action_do_login($uri, $action)
	{
		$me = &new User();
			
		if($err = $me->do_login(@$_POST['login'], @$_POST['password'], false))
			return bors_message($err, false);

		include_once("funcs/logs.php");
		log_action("user-login", $uri);

		return bors_message(ec("Вы успешно вошли в систему"), array('redirect' => "$uri?", 'title' => "", 'timeout' => 2));
}
