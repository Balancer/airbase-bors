<?
	$GLOBALS['now'] = time();

	if(!defined("BORS_INCLUDE"))
		define("BORS_INCLUDE", $_SERVER['DOCUMENT_ROOT']."/cms/");

	include_once(BORS_INCLUDE.'config/default.php');

	if(file_exists(BORS_INCLUDE.'config/local.php'))
		include_once(BORS_INCLUDE.'config/local.php');

	if(file_exists(@BORS_INCLUDE_LOCAL.'config.php'))
		include_once(@BORS_INCLUDE_LOCAL.'config.php');

    require_once("debug.php");
    require_once("localization/main.php");

	global $hts;
	include_once("funcs/DataBaseHTS.php");
	$hts = &new DataBaseHTS();

