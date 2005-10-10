<?
    list($usec, $sec) = explode(" ",microtime());
    $GLOBALS['cms']['start_microtime'] = ((float)$usec + (float)$sec);

    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
    ini_set('log_errors', 'On');
    require_once('config.php');

	require_once("funcs/lcml.php");
	require_once("funcs/DataBaseHTS.php");
	require_once("funcs/users.php");
	
	$us = new User();
	
//	echo "<xmp>";
	echo lcml('[b][http://xxxx|xxx][/b]');
//	echo "</xmp>";
?>

