<?php

class airbase_forum_listfulld extends base_list
{
	var $_list;

	function named_list()
	{
		if(!is_null($this->_list))
			return $this->_list;

		$forums_count = bors_count('balancer_board_forum', array());

		$forums = bors_find_all('balancer_board_forum', array(
			'order' => 'cat_id, disp_position',
			'by_id' => true,
			'redirect_url IS NULL',
		));
		$cat_ids = array();
		foreach($forums as $id => $f)
			if(!in_array($cat_id = $f->category_id(), $cat_ids))
				$cat_ids[] = $cat_id;

		$cats = bors_find_all('airbase_forum_category', array('order' => 'disp_position', 'by_id' => true));

		$result = array(0 => ' ');
		foreach($forums as $id => $f)
			if($f->can_read())
				$result[$id] = $f->full_name($forums, $cats);

		asort($result);

		$result[0] = ec('Не указан');

		return $result;
	}
}
