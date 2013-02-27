<?php

class forum_forum extends bors_object_db
{
	function table_name() { return 'forums'; }
	function db_name() { return config('punbb.database', 'AB_FORUMS'); }
	function storage_engine() { return 'bors_storage_mysql'; }

	function new_class_name() { return 'balancer_board_forum'; }

	function table_fields()
	{
		return array(
			'id',
			'title' => 'forum_name',
			'description' => 'forum_desc',
			'image_id',
			'parent_forum_id' => 'parent',
			'parent_id' => 'parent',
			'tree_map',
			'category_id' => 'cat_id',
			'is_public',
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
	if($dbup && ($v||$this->parent_id()))
		$this->set_tree_map(bors_lib_object::tree_map($this), true);

	return $this->set('parent_forum_id', $v, $dbup);
}

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
			$f = object_load(config('punbb.forum_class', 'forum_forum'), $f->parent_forum_id());

		return $this->__category = object_load('balancer_board_category', $f->category_id());
	}

	function parents()
	{
		if($this->parent_forum_id())
			return array("forum_forum://" . $this->parent_forum_id());

		if($this->category())
			return array("airbase_board_category://" . $this->category_id());

		return array("http://www.balancer.ru/forum/");
	}

	function pre_show()
	{
		if(!$this->can_read())
			return bors_message(ec("Извините, доступ к этому ресурсу закрыт для Вас."));

		return parent::pre_show();
	}
/*
	function body()
	{
		include_once("engines/smarty/assign.php");

		$data = array();

		$topics_per_page = $this->items_per_page();
		$start_from = ($this->page() - 1) * $topics_per_page;

		$db = new driver_mysql(config('punbb.database', 'AB_FORUMS'));
		$data['topics'] = $db->get_array("SELECT id FROM topics WHERE forum_id IN (".join(",", $this->all_readable_subforum_ids()).") ORDER BY last_post DESC LIMIT $start_from, $topics_per_page");
		$data['forum_topic_class'] = config('punbb.forum_topic_class', 'forum_topic');
		$db->close(); $db = NULL;

//			foreach($topics as $tid)
//				$data['topics'][] = bors_load('forum/borsForumTopic', $tid);

		$data['this'] = $this;

		return template_assign_data("templates/ForumBody.html", $data);
	}
*/
	function items_per_page() { return 50; }
	function total_items() { return $this->db()->select('topics', 'COUNT(*)', array("(forum_id IN (".join(",", $this->all_readable_subforum_ids())."))")) ; }

	//TODO: тут гости у нас строго 3-я группа!
	function is_public_access()
	{
		$ch = new bors_cache;
		if($ch->get('forum_permissions', "{$this->id()}:3"))
			return $ch->last();

		$access = airbase_forum_access::load_fg($this->id(), '3');
		if($access)
			return $this->set($access->can_read(), 600);

		return $this->set(object_load('forum_group', 3)->can_read(), 600);
	}

	function can_read()
	{
		$user = bors()->user();
		$gid = $user ? $user->group_id() : 3;

		if(!$gid)
			$gid = 3;

		$ch = new bors_cache;
		if($ch->get('forum_permissions', "{$this->id()}:{$gid}"))
			return $ch->last();

		$access = airbase_forum_access::load_fg($this->id(), $gid);
		if($access)
			return $ch->set($access->can_read(), 600);

		return $ch->set(object_load('forum_group', $gid)->can_read(), 600);
	}

	function can_read_by_group($group)
	{
		$gid = $group->id();

		$ch = new bors_cache;
		if($ch->get('forum_permissions', "{$this->id()}:{$gid}"))
			return $ch->last();

		$access = airbase_forum_access::load_fg($this->id(), $gid);
		if($access)
			return $ch->set($access->can_read(), 600);

		return $ch->set(object_load('forum_group', $gid)->can_read(), 600);
	}

	function cache_children()
	{
		$children_caches = array();
		if($this->parent_forum_id())
			$children_caches[] = bors_load(config('punbb.forum_class', 'forum_forum'), $this->parent_forum_id());

		if($this->category_id())
			$children_caches[] = bors_load('balancer_board_category', $this->category_id());

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
		$db = new driver_mysql(config('punbb.database', 'AB_FORUMS'));
		$result =  $db->get_array("SELECT id FROM forums WHERE parent = {$this->id()}");
		$db->close(); 
		$db = NULL;
		return $result;
	}

	function direct_subforums()
	{
		$subforums = array();
		foreach($this->direct_subforums_ids() as $forum_id)
			$subforums[] = bors_load(config('punbb.forum_class', 'forum_forum'), $forum_id);
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
			$subforum = $forums[] = bors_load(config('punbb.forum_class', 'forum_forum'), $forum_id);
			$forums = array_merge($forums, $subforum->all_subforums($processed));
		}

		return $forums;
	}

	static function all_forums_preload($update_pos = false)
	{
		static $preloaded = false;

		if($preloaded)
			return;

		$preloaded = true;
		$all = objects_array(config('punbb.forum_class', 'forum_forum'), array('order' => 'sort_order'));
		if($update_pos)
			foreach($all as $f)
				$f->set_tree_map(bors_lib_object::tree_map($f), true);
	}

	function all_readable_subforum_ids(&$processed = array())
	{
		if($ids = $this->attr('all_readable_subforum_ids'))
			return $ids;

		$this->all_forums_preload();

		$forums = array($this->id());

		foreach($this->direct_subforums_ids() as $forum_id)
		{
			if(in_array($forum_id, $processed))
				continue;

			$processed[] = $forum_id;
			$subforum = object_load(config('punbb.forum_class', 'forum_forum'), $forum_id);
			$forums = array_merge($forums, $subforum->all_readable_subforum_ids($processed));
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
			$subforum = object_load(config('punbb.forum_class', 'forum_forum'), $forum_id);
			if($subforum && $subforum->is_public_access())
				$forums = array_merge($forums, $subforum->all_readable_subforum_ids($processed, false));
		}

		if($root)
			$this->all_public_subforums_ids = $forums;

		return $forums;
	}

	function cache_static() { return $this->is_public_access() && config('static_forum') ? 600 : 0; }

	function topic_update()
	{
		require_once('/var/www/balancer.ru/htdocs/cms/other/punbb-modified-forum/include/functions.php');
	}

	function url() { return $this->category()->category_base_full().'viewforum.php?id='.$this->id(); }
//	function url() { return 'http://www.balancer.ru/forum/punbb/viewforum.php?id='.$this->id(); }
	function cache_static_can_be_dropped() { return false; }

	function cache_group_provides() { return parent::cache_group_provides() + array("balancer-board-forum-{$this->id()}"); }

	function auto_objects()
	{
		return array(
			'parent' => 'balancer_board_forum(parent_id)',
			'parent_forum' => 'balancer_board_forum(parent_forum_id)',
			'last_post' => 'balancer_board_post(last_post_id)',
			'image' => 'balancer_board_image(image_id)',
		);
	}

	function update_num_topics()
	{
		$this->set_num_topics(objects_count('balancer_board_topic', array('forum_id' => $this->id())), true);
		$this->set_num_posts(objects_count('balancer_board_posts_pure', array(
			'inner_join' => 'balancer_board_topic ON (balancer_board_topic.id = balancer_board_post.topic_id AND balancer_board_topic.forum_id='.$this->id().')'
		)), true);
	}

	function recalculate()
	{
		$this->update_num_topics();
	}
}
