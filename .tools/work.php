#!/usr/bin/php
<?php
    $_SERVER['DOCUMENT_ROOT'] = '/var/www/bal.aviaport.ru/htdocs';

    require_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config.php");
    require_once("obsolete/DataBaseHTS.php");

    $hts = new DataBaseHTS();

//  $hts->delete_by_mask('%/thread%');
//  $hts->delete_by_mask('%/post%');
//  $hts->delete_by_mask('%/news%');
?>
