<?
    register_uri_handler('!/search/!', 'handler_check_search_access');

    function handler_check_search_access($uri, $m=array())
	{
		if(preg_match("!/job/vacancy!", $uri))
			return false;
	
		include_once("funcs/modules/messages.php");
		
		if(user_data('level') < 2)
			return error_message(ec("Извините, у вас недостаточный уровень доступа для просмотра этой страницы. Ваш уровень " . intval(user_data('level'))));

		return $uri;
    }
?>
