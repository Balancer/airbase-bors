<?php
    require_once("funcs/DataBaseHTS.php");

    function include_data($uri, $key)
    {
        $hts = new DataBaseHTS();
        return $hts->get_data($uri, $key);
   	}

	echo include_data($GLOBALS['module_data']['page'], $GLOBALS['module_data']['key']);
