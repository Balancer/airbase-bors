#!/usr/bin/php
<?
	$_SERVER['DOCUMENT_ROOT'] = '/var/www/bal.aviaport.ru/htdocs';
	
	require_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config.php");
	require_once("funcs/DataBaseHTS.php");

	$hts = new DataBaseHTS();

	$hts->rename_host('http://www.aviaport.ru/forum-new', 'http://bal.aviaport.ru/conferences');
//	$hts->rename_host('/conference/', '/conferences/');
?>
