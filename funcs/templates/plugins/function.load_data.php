<?php
    require_once("config.php");
    require_once('funcs/DataBaseHTS.php');

    function smarty_function_load_data($params, &$smarty)
    {
        $hts = new DataBaseHTS;
		
		$norm = $hts->normalize_uri($params['page']);
		
        if(empty($params['page']))
            $params['page'] = $GLOBALS['page'];
        $ldp = $params['page'];
        $src = $hts->get_data($ldp, $params['key']);
        if(!$src)
            $src = $hts->get_data($GLOBALS['page']."$ldp/", $params['key']);
        return $src;
    }
?>
