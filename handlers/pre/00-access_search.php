<?
    register_uri_handler('!/search/!', 'handler_check_search_access');

    function handler_check_search_access($uri, $m=array())
	{
		if(user_data('level') < 2)
		{
			$GLOBALS['page_data']['source'] = ec("Извините, у вас недостаточный уровень доступа для просмотра этой страницы. Ваш уровень " . user_data('level'));
			show_page($uri);
			return true;
		}

		return $uri;
    }
?>
