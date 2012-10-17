<?php

class forum_category extends bors_page_db
{
	function new_class_name() { return 'balancer_board_category'; }

	function db_name() { return config('punbb.database', 'punbb'); }
	function table_name() { return 'categories'; }
	function table_fields()
	{
		return array(
			'id',
			'title' => 'cat_name',
			'parent_category_id' => 'parent',
			'order' => 'disp_position',
			'sort_order' => 'disp_position',
			'category_base' => 'base_uri',
			'bors_append',
			'template',
			'category_template' => 'template',
		);
	}

function parent_category_id() { return @$this->data['parent_category_id']; }
function set_parent_category_id($v, $dbup) { return $this->set('parent_category_id', $v, $dbup); }
function order() { return @$this->data['order']; }
function set_order($v, $dbup) { return $this->set('order', $v, $dbup); }
function category_base() { return @$this->data['category_base']; }
function set_category_base($v, $dbup) { return $this->set('category_base', $v, $dbup); }
function bors_append() { return @$this->data['bors_append']; }
function set_bors_append($v, $dbup) { return $this->set('bors_append', $v, $dbup); }
function template() { return @$this->data['template']; }
function set_template($v, $dbup) { return $this->set('template', $v, $dbup); }

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
				$cat = class_load('forum_category', $this->parent_category_id());

			return $cat->category_base();
		}

		function parents()
		{
//			echo "Get parents for cat ".$this->id();
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
				$subcats[] = class_load('forum_category', $cat_id);
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
				$subforum = $cats[] = class_load('forum_category', $forum_id);
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
				$subforums[] = class_load('forum_forum', $forum_id);
			return $subforums;
		}

/*		function all_subforums(&$cats_processed = array(), &$forums_processed = array())
		{
			$forums = array();
			
			foreach(array_merge($this->id(), $this->direct_subcats_ids()) as $cat_id)
			{
				if(in_array($cat_id, $cats_processed))
					continue;
				
				$cats_processed[] = $cat_id;
				$subcat = class_load('forumCategory', $cat_id);
				
				foreach($subcat->direct_subforums_ids() as $forum_id)
				{
					if(in_array($forum_id, $forums_processed))
						continue;

					$forumms_processed[] = $forum_id;
					$subforum = $forums[] = class_load('forum', $forum_id);
					$forums = array_merge($forums, $subforum->all_subforums(&$cats_processed, &$forums_processed));
				}
			}
					
			return $forums;
		}
*/

}
