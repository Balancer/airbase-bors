<?
    register_uri_handler('!^(http://[^/]+.*/)~page(\d+)/?$!', 'handler_by_pages');

    function handler_by_pages($uri, $m=array())
	{
//		echo "Page = $m[2]<br>";
		$GLOBALS['cms']['page_number'] = max(1, intval($m[2]));
		return $m[1];
    }
?>
