<?php
    require_once("config.php");
    require_once('funcs/DataBaseHTS.php');

    function smarty_function_page_title($params, &$smarty)
    {
        $hts = new DataBaseHTS;
        return $hts->get_data($GLOBALS['main_uri'], 'title');
    }
?>