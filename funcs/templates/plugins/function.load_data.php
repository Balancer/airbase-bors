<?php
    require_once("config.php");
    require_once('funcs/DataBaseHTS.php');

    function smarty_function_load_data($params, &$smarty)
    {
//		print_r($params);
	
        $hts = new DataBaseHTS;
		
		$add = "";
		
		$norm = $hts->normalize_uri($params['page']);
		
        if(empty($params['page']))
            $params['page'] = $GLOBALS['main_uri'];

        $ldp = $params['page'];
        $src = $hts->get_data($hts->normalize_uri($ldp), $params['key']);
		$add .= $hts->normalize_uri($ldp);

        if(!$src)
            $src = $hts->get_data($hts->normalize_uri($GLOBALS['main_uri']."$ldp/"), $params['key']);

        return /*"=$add|{$params['key']}=".*/ $src;
    }
?>
