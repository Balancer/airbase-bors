<?
	function handlers_exec()
	{
		$uri = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

		if(!empty($_GET))
		{
			$ret = do_action_handlers($uri, $uri, $GLOBALS['cms_actions']);
		
			if($ret === true)
				return;

			if($ret !== false)
				$uri = $ret;
		}
		
		return do_uri_handlers($uri, $uri, $GLOBALS['cms_patterns']);
	}
