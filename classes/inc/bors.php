<?php

require_once('names.php');
require_once('classes/objects/Bors.php');
require_once('inc/messages.php');

function object_load($class_id, $object_id=NULL, $page=1)
{
	if(preg_match('!^\d+$!', $class_id))
		$class_name = class_id_to_name($class_id);
	else
		$class_name = $class_id;
	
//	echo "Load {$class_id}({$object_id})<br />\n";
	
	if(!$class_name)
		return;
	
	return class_load($class_name, $object_id, $page);
}

function defval($data, $name, $default)
{
	if(empty($data[$name]))
		return $default;
	
	return $data[$name];
}
