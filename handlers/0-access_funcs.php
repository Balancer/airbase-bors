<?
    function check_action_access($level=3, $redir=false)
	{
		$us = new User();

		if($us->data('level') < $level)
		{
			if($redir)
			{
				go($redir);
			}
			else
			{
				$GLOBALS['page_data']['source'] = "Access Denied";
				show_page($uri);
			}
			
			return false;
		}

		return true;
    }
?>
