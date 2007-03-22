<?
	include_once(CMS_INCLUDE.'config/default.php');
	@include_once(CMS_INCLUDE.'config/local.php');
	@include_once(CMS_INCLUDE_LOCAL.'config.php');
	@include_once(@BORS_LOCAL_PATH . "/config.php");
    require_once("debug.php");
    require_once("localization/main.php");

	global $hts;
	include_once("funcs/DataBaseHTS.php");
	$hts = &new DataBaseHTS();
