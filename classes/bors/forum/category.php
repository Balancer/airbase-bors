<?php

class forum_category extends bors_page_db
{
	function new_class_name() { return 'balancer_board_category'; }

	function db_name() { return config('punbb.database'); }
	function table_name() { return 'categories'; }
	function table_fields()
	{
		return array(
			'id',
			'title' => 'cat_name',
			'project',
			'parent_category_id' => 'parent',
			'sort_order' => 'disp_position',
			'category_base' => 'base_uri',
			'bors_append',
			'template',
			'category_template' => 'template',
		);
	}

function parent_category_id() { return @$this->data['parent_category_id']; }
function set_parent_category_id($v, $dbup=true) { return $this->set('parent_category_id', $v, $dbup); }
function order() { return @$this->data['order']; }
function set_order($v, $dbup=true) { return $this->set('order', $v, $dbup); }
function category_base() { return @$this->data['category_base']; }
function set_category_base($v, $dbup=true) { return $this->set('category_base', $v, $dbup); }
function bors_append() { return @$this->data['bors_append']; }
function set_bors_append($v, $dbup=true) { return $this->set('bors_append', $v, $dbup); }
function template() { return @$this->data['template']; }
function set_template($v, $dbup=true) { return $this->set('template', $v, $dbup); }

	function url()
	{
		$base = $this->category_base_full();
		if($this->parent_category_id())
			return secure_path($base.'/viewcat.php?id='.$this->id());
		else
			return $base;
	}

	function category_base_full()
	{
		$cat = $this;
		while(!$cat->category_base() && $this->parent_category_id())
			$cat = bors_load('balancer_board_category', $this->parent_category_id());

		$base = $cat->category_base();

		if($bs = config('airbase_mirror_map'))
		{
			if(empty($bs[$base]))
			{
				echo "Can't find $base";
				exit();
			}
			$base = $bs[$base];
		}

		return $base;
	}

	function parents()
	{
//		echo "Get parents for cat ".$this->id();
		if($this->parent_category_id())
			return array("forum_category://". $this->parent_category_id());

		return array("http://www.balancer.ru/forum/");
	}

	function direct_subcats_ids()
	{
		// Получаем одни cat_id для дочерних категорий первого уровня
		return $this->db()->get_array("SELECT id FROM categories WHERE parent = {$this->id()}");
	}

	function direct_subcats()
	{
		$subcats = array();
		foreach($this->direct_subcats_ids() as $cat_id)
			$subcats[] = bors_load('balancer_board_category', $cat_id);
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
			$subforum = $cats[] = bors_load('balancer_board_category', $forum_id);
			$cats = array_merge($cats, $subforum->all_subforums($processed));
		}

		return $cats;
	}

	function direct_subforums_ids()
	{
		// Получаем одни forum_id для дочерних форумов первого уровня
		return $this->db()->get_array("SELECT id FROM forums WHERE cat_id = {$this->id()}");
	}

	function direct_subforums()
	{
		$subforums = array();
		foreach($this->direct_subforums_ids() as $forum_id)
			$subforums[] = bors_load('balancer_board_forum', $forum_id);
		return $subforums;
	}
}
