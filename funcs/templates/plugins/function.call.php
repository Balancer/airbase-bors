<?php

    function smarty_function_call($params, &$smarty)
    {
        if(empty($params['func']))
            return "call func error - empty name";

        $fn = "sfc_".$params['func'];
        if(!function_exists($fn))
            return "call $fn error - function not exists";

        return $fn($params);
    }

    function sfc_debug_page_stat()
    {
        return debug_page_stat();
    }

    function sfc_module($name)
    {
		if(!empty($params['param']))
			$name = $params['param'];
		else
		{
			$name = $params['name'];
			foreach($params as $key=>$val)
				$GLOBALS['module_data'][$key] = $val;
		}
		
		ob_start();
        include_once("modules/$name");
//		echo "$name";
    	$res = ob_get_contents();
	    ob_end_clean();
		return $res;
    }

    function sfc_forum($params)
    {
		if(!empty($params['param']))
		{
			$id = $params['param'];
	        include("show/forum-comments.phtml");
		}
    }

?>