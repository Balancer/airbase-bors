<?php

class forum_blog extends bors_page_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function can_be_empty() { return false; }

	function db_name() { return config('punbb.database'); }
	function table_name() { return 'blog'; }

	function table_fields()
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
			'is_deleted',
			'is_paste',
			'is_owned',
			'is_microblog',
			'blog_source_class',
			'blog_source_id',
		);
	}

	function new_instance()
	{
		$tab = $this->table_name();

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
		$blog = bors_load('user_blog', $this->owner_id());
		if($blog)
			$blog->cache_clean_self($this->id());
	}

	function cache_clean_self()
	{
		parent::cache_clean_self();
		bors()->changed_save();
//		bors_exit('tid='.$this->post()->topic_id());
		$post = bors_load('balancer_board_post', $this->id());
		//TODO: добавить поиск потерянных блоговых записей без топиков
		if($topic = $post->topic())
		{
			$this->set_topic_id($topic->id(), true);
			$this->set_forum_id($topic->forum_id(), true);
		}
		else
		{
			$this->set_topic_id(NULL, true);
		}

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

	function owner() { return bors_load('balancer_board_user', $this->owner_id()); }
	function title() { return $this->get('title_raw') ? $this->title_raw() : object_property($this->topic(), 'title'); }
	function set_title($title, $up=true) { return $this->set_title_raw($title, $up); }

//	function url() { return object_property($this->post(), 'url'); }
	function url_ex($page) { return object_property($this->post(), 'url'); }

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
	function author_name() { return object_property($this->post(), 'author_name'); }
}
