<?php

    function smarty_function_module($params, &$smarty)
    {
		$name = $params['name'].".php";
		foreach($params as $key=>$val)
			$GLOBALS['module_data'][$key] = $val;
		
		ob_start();
		include("modules/$name");
		$res = ob_get_contents();
		ob_end_clean();
		return $res;
    }
