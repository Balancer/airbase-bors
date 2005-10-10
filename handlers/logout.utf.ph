<?
    register_action_handler('logout', 'handler_action_logout');

    function handler_action_logout($uri, $action)
	{
		$us = new User;
		$us->do_logout();

		$GLOBALS['page_data']['title'] = "Выход";
		$GLOBALS['page_data']['source'] = 'Вы успешно вышли из системы';

		show_page($uri);
		go("/{$GLOBALS['cms']['conferences_path']}/",false,1);
		return true;
	}
?>
