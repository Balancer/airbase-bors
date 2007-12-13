<?php

require_once('classes/inc/mysql.php');

function objects_array($class, $args = array())
{
	if(is_numeric($class))
		$class = class_id_to_name($class);

	$where = mysql_where_compile(@$args['where']).' '.mysql_order_compile(@$args['order']);
	if(!empty($args['page']) && !empty($args['per_page']))
		$where .= ' '.mysql_limits_compile($args['page'], $args['per_page']);

	if(!empty($args['inner_join']))
		foreach($args['inner_join'] as $join)
			$where = "INNER JOIN {$join} {$where}";

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

	$where = mysql_where_compile(@$args['where']);

	if(!empty($args['inner_join']))
		foreach($args['inner_join'] as $join)
			$where = "INNER JOIN {$join} {$where}";
	
	$cargs = array();

	if(!empty($args['object_id']))
		$cargs['object_id'] = $args['object_id'];

	return $init->storage()->load($init, $where, true, $cargs);
}
