<?php

class airbase_forum_listfull extends base_list
{
	var $_list;

	function named_list()
	{
		if(!is_null($this->_list))
			return $this->_list;

		$ch = new Cache;
		if($ch->get('airbase_forum_listfull', 'list'))
			return $this->_list = $ch->last();

		$forums = objects_array('balancer_board_forum', array(
			'order' => 'cat_id, disp_position',
			'by_id' => true,
			'redirect_url IS NULL',
		));
		$cat_ids = array();
		foreach($forums as $id => $f)
			if(!in_array($cat_id = $f->category_id(), $cat_ids))
				$cat_ids[] = $cat_id;

		$cats = objects_array('airbase_forum_category', array('order' => 'disp_position', 'by_id' => true));

		$result = array(0 => ' ');
		foreach($forums as $id => $f)
			if($f->can_read())
				$result[$id] = $f->full_name($forums, $cats);

		asort($result);

		$result[0] = ec('Не указан');

		return $this->_list = $ch->set($result, rand(3600, 7200));
	}
}
