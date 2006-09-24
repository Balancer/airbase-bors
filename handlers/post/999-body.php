<?
	include_once("funcs/lcml.php");

	hts_data_posthandler_add("!.*!", 'body', create_function('$uri, $m', '$hts = new DataBaseHTS();
		return (@$GLOBALS["cms"]["action"]) ? NULL : lcml($hts->get_data($uri,"source"), array(
			"page" => $uri,
			"cr_type" => $hts->get_data($uri, "cr_type"),
			"html" => true,
		));'));
?>
