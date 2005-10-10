<?
    require_once("actions/create-page.php");

    register_action_handler('create-page', 'handler_create_page');

    function handler_create_page($uri, $action)
	{
		if(!check_action_access(9))
			return false;

		action_create_page($uri);

		// Показываем созданную страницу
		show_page($uri);

		return true;
	}
?>
