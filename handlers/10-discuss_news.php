<?
    register_uri_handler("!^(http://[^/]+/){$GLOBALS['cms']['conferences_path']}/discuss_news\.php$!", 'handler_discuss_news');

    function handler_discuss_news($uri, $m=array())
	{
		$news_id = @$_GET['NewsID'];

		$thread = $hts->get_data("/30/27510.html", $uri, 'child', NULL, false, false, 'value', "value LIKE 'http://{$GLOBALS['cms']['conferences_host']}/news%' AND id");

		return true;
    }
?>
