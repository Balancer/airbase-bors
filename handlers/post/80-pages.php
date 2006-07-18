<?
    require_once('funcs/DataBaseHTS.php');
//    require_once('Smarty/Smarty.class.php');

	if(empty($GLOBALS['cms']['only_load']))
		register_uri_handler('!^http://([^/]+)(.*)$!', 'handler_pages');

	function handler_pages($uri, $m=array())
	{
//		echo "<tt>try show page '$uri'</tt>";
	
	    $hts  = new DataBaseHTS;
	    require_once('funcs/templates/smarty.php');
		if($hts->get_data($uri, 'source') || $hts->get_data($uri, 'body'))
		{
			show_page($uri);
			return true;
		}

		return false;

		$hts_file = preg_replace("!^{$GLOBALS['cms_host_url']}!", "", $uri);
		$hts_file = $GLOBALS['doc_root'].$hts_file."index.hts";

//		echo $hts;
		
		if(!file_exists($hts_file))
			return false;
		
		require_once("funcs/obsolete/data.php");
		$data = hts_get($hts_file);
		hts_store($uri, $data);
//		echo $GLOBALS['body'];

		if($hts->get_data($uri, 'source') || $hts->get_data($uri, 'body'))
		{
			show_page($uri);
			return true;
		}

		return false;
	}
/*
RewriteRule ^(.*)\?(.*?)$ cms/smarty/smarty.php?args=$2
RewriteRule ^(.*)/$ cms/smarty/smarty.php
RewriteRule ^$ cms/smarty/smarty.php
*/
?>
