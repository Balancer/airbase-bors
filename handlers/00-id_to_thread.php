<?
    register_uri_handler("!^(http://[^/]+.*/{$GLOBALS['cms']['conferences_path']}/)(\d+/?)$!", 'handler_id_to_thread');
    register_uri_handler("!^(http://[^/]+.*/{$GLOBALS['cms']['conferences_path']}/guest/)(\d+/?)$!", 'handler_id_to_thread');

    function handler_id_to_thread($uri, $m=array())
	{
		return "{$m[1]}thread{$m[2]}";
    }
?>
