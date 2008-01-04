<?php

require_once('names.php');
require_once('classes/objects/Bors.php');
require_once('inc/messages.php');
require_once('inc/bors/objects_array.php');

function object_load($class, $object_id=NULL, $page=NULL, $args=array())
{
	if(is_numeric($class))
		$class = class_id_to_name($class);
	
//	echo "Load {$class}({$object_id})<br />\n";
	
	if(!$class)
		return;
	
	return class_load($class, $object_id, $page, $args);
}

function defval($data, $name, $default=NULL)
{
	if(empty($data[$name]))
		return $default;
	
	return $data[$name];
}
