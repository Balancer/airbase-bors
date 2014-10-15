<?php

class balancer_board_mobile_forums_view extends balancer_board_mobile_page
{
//	function url($page = NULL) { return '/f'.$this->id().($page > 1 ? '.'.$page : NULL); }
	function title() { return $this->forum()->title(); }
	function parents()
	{
		if($this->forum()->parent_forum_id())
			return array($this->forum()->parent_forum()->url());

		if($this->forum()->category())
			return array($this->forum()->category()->url());

		return array('/');
	}

	function can_read() { return $this->forum()->can_read(); }

	function full_version_url() { return $this->full_forum()->url(); }

	function category()
	{
		if($this->__lastfc())
			return $this->__lastc;

		$f = $this->forum();
		while($f->category_id() == 0)
			$f = object_load(config('punbb.forum_class', 'balancer_board_mobile_forum'), $f->parent_forum_id());

		return $this->__setc(object_load('balancer_board_mobile_category', $f->category_id()));
	}

	function auto_objects()
	{
		return array(
			'forum' => 'balancer_board_mobile_forum(id)',
			'full_forum' => 'balancer_board_forum(id)',
			'parent_forum' => 'balancer_board_mobile_forum(parent_forum_id)',
		);
	}

	function body_data()
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
