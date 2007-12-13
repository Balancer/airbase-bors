<?php

function mysql_where_compile($conditions_array)
{
//	if(isset($conditions_array['where']))
//		$conditions_array = $conditions_array['where'];

	if(empty($conditions_array))
		return '';
		
	$where = array();
	foreach($conditions_array as $field_cond => $value)
	{

		$value = str_replace('%ID%', '%MySqlStorageOID%', $value);
//		echo "$field_cond  $value -> $val2<br/>\n";

		if(!preg_match('! IN$!', $field_cond))
			$where[] = $field_cond . '\'' . addslashes($value) . '\'';
		else
			$where[] = $field_cond . '(' . $value . ')';
	}
	
	return 'WHERE '.join(' AND ', $where);
}

function mysql_order_compile($order_list)
{
//	if(isset($order_list['order']))
//		$order_list = $order_list['order'];
		
	if(empty($order_list))
		return '';
		
	$order = array();
	foreach(split(',', $order_list) as $o)
	{
		if(preg_match('!^\-(.+)$!', $o, $m))
			$order[] = $m[1].' DESC';
		else
			$order[] = $o;
	}

	return 'ORDER BY '.join(',', $order);
}

function mysql_limits_compile($page, $per_page)
{
	$start = (max($page,1)-1)*$per_page;
	
	return 'LIMIT '.$start.','.$per_page;
}
