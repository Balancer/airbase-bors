<?
    register_uri_handler('!/admin/!', 'handler_check_admin_access');
    register_uri_handler('!/cms/!', 'handler_check_admin_access');

    function handler_check_admin_access($uri, $m=array())
	{
		$us = new User();

		if($us->data('level',1) < 9)
		{
			$GLOBALS['page_data']['source'] = "Access Denied. Need level 9, have level " . $us->data('level',1);
			show_page($uri);
			return true;
		}

		return $uri;
    }
?>
