<?
    register_uri_handler("!^(http://[^/]+/){$GLOBALS['cms']['conferences_path']}/users/?$!", 'handler_users');

    function handler_users($uri, $m=array())
	{
		go("http://{$GLOBALS['cms']['conferences_host']}/users/");
    }
?>
