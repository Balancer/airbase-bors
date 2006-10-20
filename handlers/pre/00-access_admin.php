<?
    register_handler('!/admin/!', 'handler_check_admin_access');
	if(empty($GLOBALS['cms']['cms_path_enable']))
	    register_handler('!/cms/!', 'handler_check_admin_access');
    register_handler('!/inc/!', 'handler_check_admin_access');

    function handler_check_admin_access($uri, $m=array())
	{
		require_once('funcs/modules/messages.php');

		if(user_data('level') < 3)
			return error_message(__FILE__.ec("Извините, у вас недостаточный уровень доступа для просмотра этой страницы. Ваш уровень " . intval(user_data('level'))));

		return $uri;
    }
?>
