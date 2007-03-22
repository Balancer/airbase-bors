<?
	include_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config/default.php");
	@include_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config/local.php");
	@include_once("{$_SERVER['DOCUMENT_ROOT']}/cms-local/config.php");
	@include_once(@BORS_LOCAL_PATH . "/config.php");
    require_once("debug.php");
    require_once("localization/main.php");

	global $hts;
	include_once("funcs/DataBaseHTS.php");
	$hts = &new DataBaseHTS();
