<?php

class balancer_board_forum extends base_page_db
{
	function main_table_storage() { return 'forums'; }
	function main_db_storage() { return 'punbb'; }

	function main_table_fields()
	{
		return array(
			'id',
			'title' => 'forum_name',
			'description' => 'forum_desc',
			'parent_forum_id' => 'parent',
			'category_id' => 'cat_id',
			'parent_forum_id' => 'parent',
			'sort_order' => 'disp_position',
			'keywords_string' => 'keywords',
			'redirect_url',
			'moderators',
			'num_topics',
			'num_posts',
			'last_post_time' => 'last_post',
			'last_post_id',
			'last_poster',
			'sort_by',
			'original_id',
			'skip_common',
		);
	}

function parent_forum_id() { return @$this->data['parent_forum_id']; }
function set_parent_forum_id($v, $dbup) { return $this->set('parent_forum_id', $v, $dbup); }
function category_id() { return @$this->data['category_id']; }
function set_category_id($v, $dbup) { return $this->set('category_id', $v, $dbup); }
function sort_order() { return @$this->data['sort_order']; }
function set_sort_order($v, $dbup) { return $this->set('sort_order', $v, $dbup); }
function keywords_string() { return @$this->data['keywords_string']; }
function set_keywords_string($v, $dbup) { return $this->set('keywords_string', $v, $dbup); }
function redirect_url() { return @$this->data['redirect_url']; }
function set_redirect_url($v, $dbup) { return $this->set('redirect_url', $v, $dbup); }
function moderators() { return @$this->data['moderators']; }
function set_moderators($v, $dbup) { return $this->set('moderators', $v, $dbup); }
function num_topics() { return @$this->data['num_topics']; }
function set_num_topics($v, $dbup) { return $this->set('num_topics', $v, $dbup); }
function num_posts() { return @$this->data['num_posts']; }
function set_num_posts($v, $dbup) { return $this->set('num_posts', $v, $dbup); }
function last_post_time() { return @$this->data['last_post_time']; }
function set_last_post_time($v, $dbup) { return $this->set('last_post_time', $v, $dbup); }
function last_post_id() { return @$this->data['last_post_id']; }
function set_last_post_id($v, $dbup) { return $this->set('last_post_id', $v, $dbup); }
function last_poster() { return @$this->data['last_poster']; }
function set_last_poster($v, $dbup) { return $this->set('last_poster', $v, $dbup); }
function sort_by() { return @$this->data['sort_by']; }
function set_sort_by($v, $dbup) { return $this->set('sort_by', $v, $dbup); }
function original_id() { return @$this->data['original_id']; }
function set_original_id($v, $dbup) { return $this->set('original_id', $v, $dbup); }
function skip_common() { return @$this->data['skip_common']; }
function set_skip_common($v, $dbup) { return $this->set('skip_common', $v, $dbup); }

	function nav_name() { return truncate($this->title(), 30); }

	function full_name($forums = NULL, $cats = NULL)
	{
		$result = array();
		$current_forum = $this;
		do {
			$result[] = $current_forum->nav_name();
			if($parent = $current_forum->parent_forum_id())
				$current_forum = $forums ? $forums[$parent] : object_load('balancer_board_forum', $parent);
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

		$can_read = class_load('balancer_board_access', "{$this->id()}:$gid")->can_read();

		if($can_read === NULL)
			$can_read = class_load('balancer_board_group', $gid)->can_read();

		return $can_read;
	}

	static function enabled_ids_list()
	{
		$forums = objects_array('balancer_board_forum', array(
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
		$forums = objects_array('balancer_board_forum', array(
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
			$f = object_load('balancer_board_forum', $f->parent_forum_id());

		return $this->__category = object_load('balancer_board_category', $f->category_id());
	}

	function url() { return $this->category()->category_base_full().'viewforum.php?id='.$this->id(); }

	private $_direct_subforums = false;
	function direct_subforums()
	{
		if($this->_direct_subforums === false)
		{
			$this->_direct_subforums = objects_array('balancer_board_forum', array(
				'parent_forum_id' => $this->id(),
				'order' => 'sort_order',
			));
/*			$res = array();
			foreach($this->all_boards_forums() as $fid => $f)
				if($f->parent_forum_id() == $this->id())
					$res[] = $f;

			$this->_direct_subforums = $res;
*/
		}

		return $this->_direct_subforums;
	}

	private $_all_subforums = false;
	function all_subforums(&$processed = array())
	{
		if(!$processed && $this->_direct_subforums !== false)
			return $this->_direct_subforums;

		$forums = array();

		foreach($this->direct_subforums() as $subforum)
		{
			if(in_array($subforum->id(), $processed))
				continue;

			$forums[] = $subforum;
			$processed[] = $subforum->id();
			$forums = array_merge($forums, $subforum->all_subforums(&$processed));
		}	

		if($processed)
			return $forums;
		else
			return $this->_direct_subforums = $forums;
	}

	private $_all_subforums_titled = false;
	function all_subforums_titled()
	{
		if($this->_all_subforums_titled === false)
			$this->_all_subforums_titled = bors_field_array_extract($this->all_subforums(), 'titled_url');

		return $this->_all_subforums_titled;
	}

	static function all_boards_forums()
	{
		static $_all_boards_forum = false;
		if($_all_boards_forum === false)
			$_all_boards_forum = objects_array('balancer_board_forum', array(
				'by_id' => true,
				'order' => 'sort_order',
			));

		return $_all_boards_forum;
	}
}
