<?php

function class_name_id($object)
{
	$class_name = get_class($object);

	if(strlen($class_name) > 64)
	{
		echo 0/0;
		exit("Too long class name: '$class_name'");
	}

	$db = &new DataBase(config('main_bors_db'));
	$class_name_id = $db->get("SELECT id FROM bors_class_names WHERE name = '".addslashes($class_name)."'");

	if($class_name_id)
		return $class_name_id;
			
	$db->insert('bors_class_names', array('name' => $class_name));
	return $db->last_id();
}

function class_name_by_id($class_name_id)
{
	$db = &new DataBase(config('main_bors_db'));
	return $db->get("SELECT name FROM bors_class_names WHERE id = ".intval($class_name_id)."");
}
