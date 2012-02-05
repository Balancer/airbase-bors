<?php

class forum_blog extends base_page_db
{
//	function storage_engine() { return 'storage_db_mysql_smart'; }
	function storage_engine() { return 'bors_storage_mysql'; }
	function can_be_empty() { return false; }

	function main_db() { return config('punbb.database', 'punbb'); }
	function main_table() { return 'blog'; }

	function main_table_fields()
	{
		return array(
			'id' => 'post_id',
			'title_raw' => 'title',
			'keywords_string',
			'owner_id',
			'topic_id',
			'forum_id',
			'blogged_time',
			'is_public',
			'is_microblog',
			'blog_source_class',
			'blog_source_id',
		);
	}

	function new_instance()
	{
		$tab = $this->main_table();

		$this->db()->replace($tab, array('post_id' => $this->id(), 'blogged_time' => time()));
	}

	function delete()
	{
		$this->cache_clean();
		$this->db()->query('DELETE FROM blog WHERE post_id = ' . $this->id());

		delete_cached_object($this);
	}

	function cache_clean()
	{
//		debug_exit('clean'.$this->owner_id());
		$blog = object_load('user_blog', $this->owner_id());
		if($blog)
			$blog->cache_clean_self($this->id());
	}

	function cache_clean_self()
	{
		parent::cache_clean_self();
		bors()->changed_save();
//		bors_exit('tid='.$this->post()->topic_id());
		$this->set_topic_id(object_load('balancer_board_post', $this->id())->topic_id(), true);
		$this->set_forum_id(object_load('balancer_board_topic', $this->topic_id())->forum_id(), true);
//TODO: непонятно, откуда огромный трафик
//		if(!bors()->client()->is_bot())
//			common_keyword_bind::add($this);
	}

	function auto_objects()
	{
		return array(
			'post' => 'balancer_board_post(id)',
			'topic' => 'balancer_board_topic(topic_id)',
			'forum' => 'balancer_board_forum(forum_id)',
		);
	}

	function owner() { return object_load('balancer_board_user', $this->owner_id()); }
	function title() { return $this->get('title_raw') ? $this->title_raw() : object_property($this->topic(), 'title'); }
	function set_title($title, $up) { return $this->set_title_raw($title, $up); }

	function url() { return object_property($this->post(), 'url'); }

	function container() { return $this->topic(); }

	function keywords() { return array_map('trim', explode(',', $this->keywords_string())); }
	function set_keywords($keywords, $up)
	{
		sort($keywords, SORT_LOCALE_STRING);
		$this->set_keywords_string(join(', ', array_unique($keywords)), $up);
		if($up)
			common_keyword_bind::add($this);
		return $keywords;
	}

	function create_time() { return $this->post()->create_time(); }
	function modify_time() { return $this->post()->modify_time(); }

	// При уброке проверить http://forums.balancer.ru/tags/лингвистика/
	function num_replies() { return $this->post()->answers_count(); }
	// При уброке проверить http://forums.balancer.ru/tags/лингвистика/
	function author_name() { return $this->post()->author_name(); }
}
