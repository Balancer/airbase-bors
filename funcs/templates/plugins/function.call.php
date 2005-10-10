<?php

    function smarty_function_call($params, &$smarty)
    {
        if(empty($params['func']))
            return "call func error - empty name";

        $fn = "sfc_".$params['func'];
        if(!function_exists($fn))
            return "call $fn error - function not exists";

        return $fn(empty($params['param'])?NULL:$params['param']);
    }

    function sfc_debug_page_stat()
    {
        return debug_page_stat();
    }

    function sfc_module($name)
    {
		ob_start();
        include_once("modules/$name");
//		echo "$name";
    	$res = ob_get_contents();
	    ob_end_clean();
		return $res;
    }

    function sfc_forum($id)
    {
        include("show/forum-comments.phtml");
    }

?>