<?php

class balancer_board_mobile_forum extends balancer_board_forum
{
	function extends_class() { return 'forum_forum'; }

	function url() { return '/f'.$this->id(); }

//	function parents() { echo 'x'.$this->category()->url(); return array($this->category()->url()); }
//	function parents() { return array('/'); }

	function parents()
	{
		if($this->parent_forum_id())
			return array($this->parent_forum()->url());

		if($this->category())
			return array($this->category()->url());

		return array('/');
	}

	private $__category = 0;
	function category()
	{
		if($this->__category !== 0)
			return $this->__category;

		$f = $this;
		while($f->category_id() == 0)
			$f = object_load(config('punbb.forum_class', 'balancer_board_mobile_forum'), $f->parent_forum_id());

		return $this->__category = object_load('balancer_board_mobile_category', $f->category_id());
	}

	function auto_objects()
	{
		return array(
			'parent_forum' => 'balancer_board_mobile_forum(parent_forum_id)',
		);
	}

	function local_data()
	{
		return array(
			'topics' => objects_array('balancer_board_mobile_topic', array(
				'forum_id' => $this->id(),
				'page' => $this->page(),
				'per_page' => $this->items_per_page(),
				'order' => '-modify_time',
			)),
		);
	}

	function items_per_page() { return 10; }
}
