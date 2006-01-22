<?
    register_uri_handler('!/admin/!', 'handler_check_admin_access');
    register_uri_handler('!/cms/!', 'handler_check_admin_access');
    register_uri_handler('!/inc/!', 'handler_check_admin_access');

    function handler_check_admin_access($uri, $m=array())
	{
		if(user_data('level') < 3)
		{
			$GLOBALS['page_data']['source'] = ec("Извините, у вас недостаточный уровень доступа для просмотра этой страницы. Ваш уровень " . user_data('level',1));

			show_page($uri);
			return true;
		}

		return $uri;
    }
?>
