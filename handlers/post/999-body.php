<?
	include_once("funcs/lcml.php");

	hts_data_posthandler_add("!.*!", 'body', 'cms_default_body');
	function cms_default_body($uri, $m)
	{	
		$hts = &new DataBaseHTS();
		$ret = @$GLOBALS["cms"]["action"] ? NULL : lcml($hts->get_data($uri,"source"), array(
			"page" => $uri,
			"cr_type" => $hts->get_data($uri, "cr_type"),
			"html" => true,
			'uri' => $uri,
		));
		
		return $ret;
	}
?>
