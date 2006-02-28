<?
    register_uri_handler('!/(\d{4}/\d{1,2}/\d{1,2})/!', 'handler_check_users_expire');

    function handler_check_users_expire($uri, $m=array())
	{
		include_once("funcs/check/user.php");
		if(!news_access($GLOBALS['main_uri']))
		{
			include_once("funcs/modules/messages.php");	
			$hts = new DataBaseHTS;
			return message($hts->get_data('/cms/templates/texts/access-expiried/','body'), false, $hts->get_data('/cms/templates/texts/access-expiried/','title'));
		}

		return false;
    }
?>
