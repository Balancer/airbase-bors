<?php

class airbase_forum_forum extends base_page_db
{
	function table_name() { return 'forums'; }
	function db_name() { return config('punbb.database', 'AB_FORUMS'); }

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
				$current_forum = $forums ? $forums[$parent] : object_load('airbase_forum_forum', $parent);
		} while($parent);

		$cat = $cats ? $cats[$current_forum->category_id()] : $current_forum->category();

		return join(' « ', $result).' « '.$cat->full_name();
	}

	private $_can_read = array();
	function can_read($public = false)
	{
		$user = $public ? NULL : bors()->user();
		$gid = $user ? $user->group_id() : 3;
		if(!$gid || $public)
			$gid = 3;

		if(isset($this->_can_read[$gid]))
			return $this->_can_read[$gid];

		if($access = airbase_forum_access::load_fg($this->id(), $gid))
			return $this->_can_read[$gid] = $access->can_read() ? 1 : 0;

		return $this->_can_read[$gid] = object_load('forum_group', $gid)->can_read() ? 1 : 0;
	}

	static function enabled_ids_list()
	{
		$forums = objects_array('airbase_forum_forum', array(
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
		$forums = objects_array('airbase_forum_forum', array(
			'redirect_url IS NULL',
		));
		
		$result = array();
		foreach($forums as $f)
			if(!$f->can_read())
				$result[] = $f->id();
		
		return $result;
	}
}
