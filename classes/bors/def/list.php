<?php

class_include('def_empty');

class def_list extends def_empty
{
	function id_to_name($id)
	{
		$list = $this->named_list();
		return $list[$id];
	}
}
