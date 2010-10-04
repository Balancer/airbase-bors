<?php

class balancer_board_mobile_forum extends balancer_board_forum
{
	function url($page = NULL) { return '/f'.$this->id().($page > 1 ? '.'.$page : NULL); }
	function extends_class() { return 'forum_forum'; }

	function parents()
	{
		if($this->forum()->parent_forum_id())
			return array($this->forum()->parent_forum()->url());

		if($this->forum()->category())
			return array($this->forum()->category()->url());

		return array('/');
	}

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
			'parent_forum' => 'balancer_board_mobile_forum(parent_forum_id)',
		);
	}
}
