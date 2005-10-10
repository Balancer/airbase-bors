<?
    register_action_handler('login', 'handler_action_login');

    function handler_action_login($uri, $action)
	{
		$us = new User;

		$GLOBALS['page_data']['title'] = "Login page";
		$GLOBALS['page_data']['source'] = '[php]if(access_warn($new_page ? $ref : $uri, $hts)) echo "<tr><td>Логин: <input name=\"login\"></td><td>Пароль: <input name=\"password\" type=\"password\"></td></tr>";[/php]';

		show_page($uri);
		go($uri);
		return true;
	}
?>
