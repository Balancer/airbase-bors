<?
    require_once("actions/create-page.php");

    register_action('create-page', 'handler_create_page');

    function handler_create_page($uri, $action)
	{
//		exit("Try create $_uri");
		require_once("funcs/check/access.php");

		if(!check_action_access(10))
			return false;

		action_create_page($uri);

		// Показываем созданную страницу
		show_page($uri);

		return true;
	}
?>
