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
	include_once("obsolete/DataBaseHTS.php");
	$hts = &new DataBaseHTS();

function bors_init()
{
	require_once("funcs/templates/global.php");
	require_once("obsolete/users.php");
	require_once("funcs/navigation/go.php");
	require_once("funcs/lcml.php");
    require_once("include/classes/cache/CacheStaticFile.php");


	require_once('classes/inc/bors.php');
	require_once('engines/bors/object_show.php');
	require_once('engines/bors/vhosts_loader.php');
}
