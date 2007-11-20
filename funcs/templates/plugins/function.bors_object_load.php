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

	$obj = class_load($class, $id);

	if(!empty($var))
    	$smarty->assign($var, $obj);

	if(!empty($show))
		return $obj->$show();
			
	return $obj->body();
}
