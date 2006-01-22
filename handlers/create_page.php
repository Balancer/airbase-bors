<?
    require_once("actions/create-page.php");

    register_action_handler('create-page', 'handler_create_page');

    function handler_create_page($uri, $action)
	{
	
		require_once("funcs/check/access.php");

		if(!check_action_access(3))
			return false;

		action_create_page($uri);
		echo("!!!");

		// Показываем созданную страницу
		show_page($uri);

		return true;
	}
?>
