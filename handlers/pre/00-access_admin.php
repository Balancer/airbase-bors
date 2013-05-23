<?php
    register_handler('!/admin/!', 'handler_check_admin_access');
	if(empty($GLOBALS['cms']['cms_path_enable']))
	    register_handler('!/cms/!', 'handler_check_admin_access');
    register_handler('!/inc/!', 'handler_check_admin_access');

    function handler_check_admin_access($uri, $m=array())
	{
		if(!bors()->user())
			return bors_message(__FILE__.ec(': Вы не авторизованы'));

		if(bors()->user()->level() < 3)
			return bors_message(__FILE__.ec(": Извините, у вас недостаточный уровень доступа для просмотра этой страницы. Ваш уровень " . bors()->user()->level()));

		return $uri;
    }
