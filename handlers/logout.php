<?
    register_action_handler('logout', 'handler_action_logout');

    function handler_action_logout($uri, $action)
	{
		include_once("funcs/logs.php");
		log_action("user-logout", $uri);

		$us = new User;
		$us->do_logout();

		include_once("funcs/modules/messages.php");

//		return message(ec("Вы успешно вышли из системы"), "$uri?");
		go("/");
		return true;
	}
?>
