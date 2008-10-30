<?
	function access_check($uri, $us = NULL)
	{
		exit(__FILE__.':'.__LINE__.' Access denied');
	
		$us = bors()->user();

		if($us->id() < 2)
		{
			$GLOBALS['page_data']['title'] = tr('Error');
			$GLOBALS['page_data']['source'] = tr('You are not logged in');

			show_page($uri);
			return false;
		}

		if(!access_allowed($uri))
		{
			$GLOBALS['page_data']['title'] = tr('Error');
			$GLOBALS['page_data']['source'] = tr('You have not permission to this action');

			show_page($uri);
			return false;
		}
		
		return true;
	}

    function check_action_access($level=3, $redir=false)
	{
		debug_trace();
		exit(__FILE__.':'.__LINE__.' Access denied');
		
		$us = bors()->user();

		if($us->level() < $level)
		{
			if($redir)
			{
				go($redir);
			}
			else
			{
				$GLOBALS['page_data']['source'] = ec("Доступ запрещён. Требуется $level при наличии ".$us->level());
				show_page($GLOBALS['main_uri']);
			}
			
			return false;
		}

		return true;
    }
