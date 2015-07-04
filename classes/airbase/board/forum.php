<?php

class airbase_board_forum extends bors_page_db
{
	function table_name() { return 'forums'; }
	function db_name() { return config('punbb.database'); }

	function table_fields()
	{
		return array(
			'id',
			'title' => 'forum_name',
			'parent_forum_id' => 'parent',
			'category_id' => 'cat_id',
		);
	}

	function nav_name() { return truncate($this->title(), 30); }

	function full_name($forums = NULL, $cats = NULL)
	{
		$result = array();
		$current_forum = $this;
		do {
			$result[] = $current_forum->nav_name();
			if($parent = $current_forum->parent_forum_id())
				$current_forum = $forums ? $forums[$parent] : object_load('airbase_board_forum', $parent);
		} while($parent);

		$cat = $cats ? $cats[$current_forum->category_id()] : $current_forum->category();
		
		return join(' « ', $result).' « '.$cat->full_name();
	}

	function can_read()
	{
		$user = bors()->user();
		$gid = $user ? $user->group_id() : 3;
		if(!$gid)
			$gid = 3;

		$can_read = class_load('airbase_board_access', "{$this->id()}:$gid")->can_read();

		if($can_read === NULL)
			$can_read = class_load('airbase_board_group', $gid)->can_read();

		return $can_read;
	}

	static function enabled_ids_list()
	{
		$forums = bors_find_all('airbase_board_forum', array(
			'redirect_url IS NULL',
		));
		
		$result = array();
		foreach($forums as $f)
			if($f->can_read())
				$result[] = $f->id();
		
		return $result;
	}

	static function disabled_ids_list()
	{
		$forums = bors_find_all('airbase_board_forum', array(
			'redirect_url IS NULL',
		));
		
		$result = array();
		foreach($forums as $f)
			if(!$f->can_read())
				$result[] = $f->id();
		
		return $result;
	}

	private $__category = false;
	function category()
	{
		if($this->__category !== false)
			return $this->__category;
	
		$f = $this;
		while($f->category_id() == 0)
			$f = object_load('airbase_board_forum', $f->parent_forum_id());

		return $this->__category = object_load('airbase_board_category', $f->category_id());
	}

	function url() { return $this->category()->category_base_full().'viewforum.php?id='.$this->id(); }
}
