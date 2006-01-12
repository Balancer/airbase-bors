<?
    register_action_handler('logout', 'handler_action_logout');

    function handler_action_logout($uri, $action)
	{
		$us = new User;
		$us->do_logout();

//		$GLOBALS['page_data']['title'] = ec("Выход");
//		$GLOBALS['page_data']['source'] = ec('Вы успешно вышли из системы');
//		show_page($uri);

		go("$uri?");
		return true;
	}
?>
