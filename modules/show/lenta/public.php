<?
	debug_hidden_log('old-modules');
	return;
	include_once("public.inc.php");
	echo module_show_lenta_public(@$GLOBALS['module_data']['forums'], @$GLOBALS['module_data']['limit']);
