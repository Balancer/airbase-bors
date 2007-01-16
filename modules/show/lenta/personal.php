<?
	include_once("personal.inc.php");
	echo module_show_lenta_personal($GLOBALS['module_data']['user_id'], @$GLOBALS['module_data']['forums'], $GLOBALS['module_data']['limit']);
?>
