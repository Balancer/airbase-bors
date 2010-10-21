<?
    require_once('obsolete/DataBaseHTS.php');
//    require_once('Smarty/Smarty.class.php');

//	if(empty($GLOBALS['cms']['only_load']))
	register_handler('!^http://([^/]+)(.*)$!', 'handler_pages');

	function handler_pages($uri, $m=array())
	{
//		echo "<tt>try show page '$uri'</tt><br/>";
	
	    $hts  = new DataBaseHTS($uri);

		if($hts->get('source') || $hts->get('body'))
		{
			require_once('obsolete/smarty.php');
			show_page($uri);
			return true;
		}

		return false;

		$hts_file = preg_replace("!^{$GLOBALS['cms_host_url']}!", "", $uri);
		$hts_file = $GLOBALS['doc_root'].$hts_file."index.hts";

		if(!file_exists($hts_file))
			return false;
		
		require_once("funcs/obsolete/data.php");
		$data = hts_get($hts_file);
		hts_store($uri, $data);
//		echo $GLOBALS['body'];

		if($hts->get('source') || $hts->get('body'))
		{
			show_page($uri, array('children_count' => $hts->get_children_array_ex_size($uri, array('range' => -1))));
			return true;
		}

		return false;
	}
/*
RewriteRule ^(.*)\?(.*?)$ cms/smarty/smarty.php?args=$2
RewriteRule ^(.*)/$ cms/smarty/smarty.php
RewriteRule ^$ cms/smarty/smarty.php
*/
