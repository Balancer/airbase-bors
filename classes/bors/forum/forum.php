<?php

class forum_forum extends base_page_db
{
	function main_table() { return 'forums'; }
	function main_db() { return 'punbb'; }

	function main_table_fields()
	{
		return array(
			'id',
			'title' => 'forum_name',
			'description' => 'forum_desc',
			'parent_forum_id' => 'parent',
			'tree_position',
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
function set_parent_forum_id($v, $dbup)
{
	if($dbup && $this->parent_forum_id())
		$this->tree_position();

	return $this->set('parent_forum_id', $v, $dbup);
}

function tree_position($can_up = true)
{
	if(empty($this->data['tree_position']) && ($pid = $this->parent_forum_id()) && $pid != $this->id())
	{
		$parent = $this->parent_forum();
		$ppos = $parent->tree_position(false) . $pid . '>';
		$this->set('tree_position', $ppos, $can_up);
	}

	return @$this->data['tree_position'];
}
function set_tree_position($v, $dbup) { return $this->set('tree_position', $v, $dbup); }

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

	function init()
	{
		if(!$this->id())
			debug_exit('Try to init empty forum');
			
		return parent::init();
	}

	function uri_name() { return 'forum'; }

	private $__category = 0;
	function category()
	{
		if($this->__category !== 0)
			return $this->__category;
	
		$f = $this;
		while($f->category_id() == 0)
			$f = object_load('forum_forum', $f->parent_forum_id());

		return $this->__category = object_load('forum_category', $f->category_id());
	}

function parent_forum() { return $this->load_attr('parent_forum', $this->parent_forum_id() ? object_load('forum_forum', $this->parent_forum_id()) : NULL); }

function parents()
{
	if($this->parent_forum_id())
		return array("forum_forum://" . $this->parent_forum_id());
		
	if($this->category())
		return array("airbase_board_category://" . $this->category_id());
		
	return array("http://balancer.ru/forum/");
}

	function body()
	{
//			$this->cache_clean_self();
		
		if(!$this->can_read())
			return ec("Извините, доступ к этому ресурсу закрыт для Вас");

		include_once("engines/smarty/assign.php");

		$data = array();

		$topics_per_page = 50;
		$start_from = ($this->page() - 1) * $topics_per_page;
			
		$db = new driver_mysql('punbb');
		$data['topics'] = $db->get_array("SELECT id FROM topics WHERE forum_id IN (".join(",", $this->all_readable_subforum_ids()).") ORDER BY last_post DESC LIMIT $start_from, $topics_per_page");
		$db->close(); $db = NULL;

//			foreach($topics as $tid)
//				$data['topics'][] = class_load('forum/borsForumTopic', $tid);

		$data['this'] = $this;

		return template_assign_data("templates/ForumBody.html", $data);
	}

	//TODO: тут гости у нас строго 3-я группа!
	function is_public_access()
	{
		$access = object_load('airbase_forum_access', "{$this->id()}:3", array('no_load_cache' => true));
		if($access)
			return $access->can_read();

		return object_load('forum_group', 3)->can_read();
	}

	function can_read()
	{
		$user = bors()->user();
		$gid = $user ? $user->group_id() : 3;
		if(!$gid)
			$gid = 3;

		$access = object_load('airbase_forum_access', "{$this->id()}:{$gid}");
		if($access)
			return $access->can_read();

		return object_load('forum_group', $gid)->can_read();
	}

	function cache_children()
	{ 
		$children_caches = array();
		if($this->parent_forum_id())
			$children_caches[] = class_load('forum_forum', $this->parent_forum_id());

		if($this->category_id())
			$children_caches[] = class_load('forum_category', $this->category_id());
			
		return $children_caches;
	}
		
/*		function subforums_html()
		{
			include_once('other/punbb-modified-forum/include/subforums.php');
			global $pun_user;
			$pun_user['g_id'] = 1;
		}
*/	
	function direct_subforums_ids()
	{
		// Получаем одни forum_id для дочерних форумов первого уровня
		$db = new driver_mysql('punbb');
		$result =  $db->get_array("SELECT id FROM forums WHERE parent = {$this->id()}");
		$db->close(); 
		$db = NULL;
		return $result;
	}

	function direct_subforums()
	{
		$subforums = array();
		foreach($this->direct_subforums_ids() as $forum_id)
			$subforums[] = class_load('forum_forum', $forum_id);
		return $subforums;
	}
		
	function all_subforums(&$processed = array())
	{
		$forums = array();
			
		foreach($this->direct_subforums_ids() as $forum_id)
		{
			if(in_array($forum_id, $processed))
				continue;

			$processed[] = $forum_id;
			$subforum = $forums[] = class_load('forum_forum', $forum_id);
			$forums = array_merge($forums, $subforum->all_subforums(&$processed));
		}
			
		return $forums;
	}

	static function all_forums_preload($update_pos = false)
	{
		static $preloaded = false;
		
		if($preloaded)
			return;

		$preloaded = true;
		$all = objects_array('forum_forum', array('order' => 'sort_order'));
		if($update_pos)
			foreach($all as $f)
				$f->tree_position();
	}

	function all_readable_subforum_ids(&$processed = array())
	{
		if($ids = $this->attr('all_readable_subforum_ids'))
			return $ids;

		$this->all_forums_preload();

		if(debug_is_balancer())
		{
			$dbh = new driver_mysql($this->main_db());
			$subforum_ids = $dbh->select_array('forums', 'id', array("tree_position LIKE '{$this->tree_position()}{$this->id()}>%'"));
			$dbh->close();
			$subforum_ids = array();
			return $this->set_attr('all_readable_subforum_ids', $subforum_ids);
		}
	
		$forums = array($this->id());
			
		foreach($this->direct_subforums_ids() as $forum_id)
		{
			if(in_array($forum_id, $processed))
				continue;

			$processed[] = $forum_id;
			$subforum = object_load('forum_forum', $forum_id);
			$forums = array_merge($forums, $subforum->all_readable_subforum_ids(&$processed));
		}
			
		return $this->set_attr('all_readable_subforum_ids', $forums);
	}

	private $all_public_subforums_ids = false;
	function all_public_subforum_ids(&$processed = array(), $root = true)
	{
		if($root && $this->all_public_subforums_ids !== false)
			return $this->all_public_subforums_ids;

		$forums = array($this->id());

		foreach($this->direct_subforums_ids() as $forum_id)
		{
			if(in_array($forum_id, $processed))
				continue;

			$processed[] = $forum_id;
			$subforum = object_load('forum_forum', $forum_id);
			if($subforum && $subforum->is_public_access())
				$forums = array_merge($forums, $subforum->all_readable_subforum_ids(&$processed, false));
		}

		if($root)
			$this->all_public_subforums_ids = $forums;

		return $forums;
	}

	function cache_static() { return $this->is_public_access() ? 600 : 0; }

	function topic_update()
	{
		require_once('/var/www/balancer.ru/htdocs/cms/other/punbb-modified-forum/include/functions.php');
	}

	function url() { return $this->category()->category_base_full().'viewforum.php?id='.$this->id(); }
	function cache_static_can_be_dropped() { return false; }

	function cache_groups_parent() { return parent::cache_groups_parent().
		" airbase-board-forum-{$this->id()}"; }
}
