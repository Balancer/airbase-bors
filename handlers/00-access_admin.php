<?
    register_uri_handler('!/admin/!', 'handler_check_admin_access');
    register_uri_handler('!/cms/!', 'handler_check_admin_access');
    register_uri_handler('!/inc/!', 'handler_check_admin_access');

    function handler_check_admin_access($uri, $m=array())
	{
//		echo user_data('level',NULL,1);
		if(user_data('level',NULL,1) < 3)
		{
			$GLOBALS['page_data']['source'] = "Access Denied. Need level 9, have level " . user_data('level',NULL,1);
			show_page($uri);
			return true;
		}

		return $uri;
    }
?>
