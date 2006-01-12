<?
    function lt_module($params)
    { 
//    	echo "xxxxxxxxxxxx";

//        if(!check_lcml_access('usemodules',true))
//            return $txt;

        $ps = "";
		foreach($params as $key=>$value)
			$ps.="\$GLOBALS['module_data']['$key'] = '".addslashes($value)."'; ";

        return /*save_format*/("<?php $ps include_once(\"modules/{$params['url']}.php\"); ?>");
    }
?>