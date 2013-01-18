<?php

require_once('inc/strings.php');

class airbase_forum_category extends bors_object_db
{
	function db_name() { return config('punbb.database', 'AB_FORUMS'); }
	function table_name() { return 'categories'; }
	function table_fields()
	{
		return array(
			'id',
			'title' => 'cat_name',
			'parent_category_id' => 'parent',
			'order' => 'disp_position',
			'category_base' => 'base_uri',
		);
	}

	function url() { return $this->category_base_full(); }

	function nav_name() { return truncate($this->title(), 20); }

	function category_base_full()
	{
		$cat = $this;
		while(!$cat->category_base() && $this->parent_category_id())
			$cat = object_load('airbase_forum_category', $this->parent_category_id());

		return $cat->category_base();
	}

	function parents()
	{
		if($this->parent_category_id())
			return array("airbase_forum_category://". $this->parent_category_id());

		return array("http://www.balancer.ru/forum/");
	}

	function direct_subcats_ids()
	{
		// Получаем одни cat_id для дочерних категорий первого уровня
		return $this->select_array('id', array('parent' => $this->id()));
	}

	function direct_subcats()
	{
		$subcats = array();
		foreach($this->direct_subcats_ids() as $cat_id)
			$subcats[] = object_load('airbase_forum_category', $cat_id);
		return $subcats;
	}
		
	function all_subcats(&$processed = array())
	{
		$cats = array();
			
		foreach($this->direct_subforums_ids() as $forum_id)
		{
			if(in_array($forum_id, $processed))
				continue;

			$processed[] = $forum_id;
			$subforum = $cats[] = object_load('airbase_forum_category', $forum_id);
			$cats = array_merge($cats, $subforum->all_subforums($processed));
		}
			
		return $cats;
	}

	function direct_subforums_ids()
	{
		// Получаем одни forum_id для дочерних форумов первого уровня
		$db = new DataBase('AB_FORUMS');
		$result = $this->db()->select_array('forums', 'id', array('cat_id' => $this->id()));
		$db->close();
		return $result;
	}

	function direct_subforums()
	{
		$subforums = array();
		foreach($this->direct_subforums_ids() as $forum_id)
			$subforums[] = class_load('airbase_forum_forum', $forum_id);
		return $subforums;
	}

	function full_name($cats = NULL)
	{
		$result = array();
		$current_cat = $this;
		do
		{
			$result[] = $current_cat->nav_name();
			if($parent = $current_cat->parent_category_id())
				$current_cat = $cats ? $cats[$parent] : object_load('airbase_forum_category', $parent);
		} while($parent);

		return join(' « ', $result);
	}
}
