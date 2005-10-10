<?
    require_once('funcs/templates/smarty.php');

	register_uri_handler('!^(http://[^/]+/conferences/)$!', 'handler_conferences');

	function handler_conferences($uri, $m=array())
	{
		$GLOBALS['cms']['handler_content'] = <<< __EOT__
[module conferences/conferences]
__EOT__;

		$GLOBALS['cms']['action'] = 'virtual';

		show_page($uri);

		return true;
	}
?>
