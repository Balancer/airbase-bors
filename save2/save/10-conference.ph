<?
    require_once('funcs/templates/smarty.php');

	register_uri_handler('!^(http://[^/]+/conferences/)(\d*)/$!', 'handler_conference');

	function handler_conference($uri, $m=array())
	{
		$conf = intval(@$m[2]);

			$GLOBALS['cms']['handler_content'] = <<< __EOT__
[module conferences/conference conf="$conf"]
__EOT__;

		$GLOBALS['cms']['action'] = 'virtual';
        $hts = new DataBaseHTS();
		$hts->set_data($uri, 'parent', $m[1]);
		
		show_page($uri);

		return true;
	}
?>
