<?php

function url_truncate($url, $max_length)
{
	if(strlen($url) <= $max_length)
		return $url;

	$limit = $max_length - 3; // Учитываем /.../ в середине.
	$chunks = explode('/', $url);
	$count = count($chunks);
	$added = array();
	$left = array();
	$left_length = 0;
	$right = array();
	$right_length = 0;
	$right_pos = $count;

	for($i=0; $i<$right_pos; $i++)
	{
		if(empty($added[$i]))
		{
			$x = $chunks[$i];
			$sx = strlen($x);
	
			if($left_length + $sx + 1 + $right_length > $limit)
				break;
				
			$left[] = $x;
			$left_length += 1+$sx;
			$added[$i] = true;
		}
		
		if($i<2)
			continue;
		
		$j = --$right_pos;
		if(empty($added[$j]))
		{
			$x = $chunks[$j];
			$sx = strlen($x);
	
			if($right_length + $sx + 1 + $left_length > $limit)
				break;
				
			array_unshift($right,  $x);
			$right_length += 1+$sx;
			$added[$j] = true;
		}

	}
	
	return join('/', $left).'/.../'.join('/',$right);
}
