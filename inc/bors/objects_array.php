<?php

require_once('classes/inc/mysql.php');

function objects_array($class, $args = array())
{
	if(is_numeric($class))
		$class = class_id_to_name($class);

	$where = mysql_args_compile($args);
		
	$init = new $class(NULL);

	$cargs = array();

	if(!empty($args['object_id']))
		$cargs['object_id'] = $args['object_id'];
	
	return $init->storage()->load($init, $where, false, $cargs);
}

function objects_count($class, $args = array())
{
	if(is_numeric($class))
		$class = class_id_to_name($class);
	
	if(is_object($class))
		$init = $class;
	else	
		$init = new $class(NULL);

	$where = mysql_args_compile($args);

	$cargs = array();

	if(!empty($args['object_id']))
		$cargs['object_id'] = $args['object_id'];

	return $init->storage()->load($init, $where, true, $cargs);
}
