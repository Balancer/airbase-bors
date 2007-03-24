<?
	register_action('recompile', 'handler_recompile');
	
	function handler_recompile($uri, $action)
	{
		include_once("actions/recompile.php");
		recompile($GLOBALS['main_uri']);
		
		go($uri);
	}
