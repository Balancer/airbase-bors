#!/usr/local/bin/php
<?
	$_SERVER['DOCUMENT_ROOT'] = '/home/kra61882/public_html';
	
	require_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config.php");
	require_once("funcs/DataBaseHTS.php");

	$hts = new DataBaseHTS();

//	$hts->rename_host('http://www.aviaport.ru/forum-new', 'http://bal.aviaport.ru/conferences');
	$hts->rename_host('http://1001kran.runews/', 'http://1001kran.ru/news/');
?>
