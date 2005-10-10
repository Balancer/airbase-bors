<?
    require_once('funcs/DataBaseHTS.php');
    require_once('funcs/templates/smarty.php');

	register_uri_handler('!^(http://[^/]+/conferences/(\d+)/)(\d+/)$!', 'handler_forum_topics');

	function handler_forum_topics($uri, $m=array())
	{
	    $hts  = new DataBaseHTS;

		if($hts->get_data($uri, 'source'))
		{
			show_page($uri);
			return true;
		}

		$forum = intval(@$m[2]);
		$topic = intval(@$m[3]);

		$GLOBALS['cms']['handler_content'] = <<< __EOT__
[module conferences/showtopic topic="$topic" forum="$forum"]
__EOT__;

		$GLOBALS['cms']['action'] = 'virtual';
        $hts = new DataBaseHTS();
		$hts->set_data($uri, 'parent', $m[1]);
		
		show_page($uri);

		return true;
	}
?>
