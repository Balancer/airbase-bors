<?
    register_action('login', 'handler_action_login');

    function handler_action_login($uri, $action)
	{
		$us = new User;

		$GLOBALS['page_data']['title'] = "Login page";
		$GLOBALS['page_data']['source'] = '<form action="?do-login" method="post"><table><tr><td>Login: <input name="login"></td><td>Password: <input name="password" type="password"></td></tr><tr><td colSpan="2"><input type="submit" value="Login"></td></tr></table></form>';

		require_once('obsolete/smarty.php');
		show_page($uri);
		return true;
	}
