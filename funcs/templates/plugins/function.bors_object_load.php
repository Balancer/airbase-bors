<?php

function smarty_function_bors_object_load($params, &$smarty)
{
   	extract($params);

    if(empty($class)) 
	{
   	    $smarty->trigger_error("bors_object_load: missing 'class' parameter");
       	return;
    }
		
   	if(!in_array('id', array_keys($params)))
		$id = NULL;

//	echo "object_load($class, $id, $page))";
	$obj = object_load($class, $id, @$page ? $page : 1);

	if(!empty($var))
	{
    	$smarty->assign($var, $obj);
		return;
	}
	
	if(!empty($show))
		return $obj->$show();
			
//	return $obj->body();
}
