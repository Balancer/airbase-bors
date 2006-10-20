<?
    register_action('do-login', 'handler_action_do_login');

    function handler_action_do_login($uri, $action)
	{
		$us = new User();
			
		include_once("funcs/modules/messages.php");


		if($err = $us->do_login(@$_POST['login'], @$_POST['password'], false))
			return error_message($err, false);

		include_once("funcs/logs.php");
		log_action("user-login", $uri);

		return message(ec("Вы успешно вошли в систему"), "$uri?", "", 2);
	}
?>
