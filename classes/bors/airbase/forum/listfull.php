<?php

class airbase_forum_listfull extends base_list
{
	function named_list()
	{
		$forums = objects_array('airbase_forum_forum', array('order' => 'cat_id, disp_position', 'by_id' => true));
		$cat_ids = array();
		foreach($forums as $id => $f)
			if(!in_array($cat_id = $f->category_id(), $cat_ids))
				$cat_ids[] = $cat_id;

		$cats = objects_array('airbase_forum_category', array('order' => 'disp_position', 'by_id' => true));
		
		$result = array(0 => ' ');
		foreach($forums as $id => $f)
			$result[$id] = $f->full_name($forums, $cats);
		
		asort($result);

		$result[0] = ec('Не указан');

		return $result;
	}
}
