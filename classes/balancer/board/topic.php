<?php

class balancer_board_topic extends forum_topic
{
	function extends_class_name() { return 'forum_topic'; }

	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return config('punbb.database', 'AB_FORUMS'); }
	function table_name() { return 'topics'; }

	function table_fields()
	{
		return array(
			'id',
			'forum_id_raw' => 'forum_id',
			'title'	=> 'subject',
			'description',
			'create_time'	=> 'posted',
			'last_post_create_time'=> 'last_post',
			'modify_time',
			'is_public',
			'owner_id'=> 'poster_id',
			'last_poster_name' => 'last_poster',
			'author_name' => 'poster',
			'num_replies',
			'is_repaged',
			'visits' => 'num_views',
			'first_post_id' => 'first_pid',
			'last_post_id' => 'last_post_id',
			'first_visit_time' => 'first_visit',
			'last_visit_time' => 'last_visit',
			'last_edit_time' => 'last_edit',
			'sticky',
			'moved_to',
			'joined_to_topic_id', // id темы, к которой была присоединена данная.
			'closed',
			'keywords_string_db' => 'keywords_string',
			'bot_note',
		);
	}

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), array(
			'folder' => 'balancer_board_forum(forum_id)',
		));
	}

	static function _forum_ids($domain)
	{
		static $fcache = array();
		if(empty($fcache[$domain]))
		{
			$cat_ids = array_keys(objects_array('balancer_board_category', array("base_uri LIKE 'http://".addslashes($domain)."/%'", 'by_id' => true)));
			if(!$cat_ids)
				return $fcache[$domain] = array(-1);
			$sub_cat_ids = array_keys(objects_array('balancer_board_category', array('parent IN' => $cat_ids, 'by_id' => true)));
			$cat_ids = array_merge($cat_ids, $sub_cat_ids);

			$fcache[$domain]  = array_keys(objects_array('balancer_board_forum', array(
				'cat_id IN' => $cat_ids, 
				'is_public' => 1,
				'by_id' => true,
			)));
		}

		if(empty($fcache[$domain]))
			return array(-1);

		return $fcache[$domain];
	}

	static function sitemap_index($domain, $page, $per_page)
	{
		return array_reverse(objects_array('balancer_board_topic', array(
			'forum_id IN' => self::_forum_ids($domain),
			'page' => $page,
			'per_page' => $per_page,
			'order' => 'modify_time',
		)));
	}

	static function sitemap_last_modify_time($domain, $page, $per_page)
	{
		$dbh = new driver_mysql(config('punbb.database', 'AB_FORUMS'));
		$dates = $dbh->select_array('topics', 'last_post', array(
			'forum_id IN' => self::_forum_ids($domain),
			'page' => $page,
			'per_page' => $per_page,
			'order' => 'last_post',
		));

		return $dates[count($dates)-1];
	}

	static function sitemap_total($domain)
	{
		return objects_count('balancer_board_topic', array(
			'forum_id IN' => self::_forum_ids($domain),
		));
	}

	static function create($forum, $title, $message, $user = NULL, $keywords_string = NULL, $as_blog = true, $data = array())
	{
//		echo "Pass new topic to {$forum->debug_title()}\n";

		$is_public = defval($data, 'is_public', true);

		$data = array_merge(array(
			'forum_id' => $forum->id(),
			'title'	=> $title,
			'last_post_create_time' => time(),
			'is_public' => $is_public,
			'owner_id' => $user->id(),
			'author_name' => $user->title(),
			'last_poster_name' => $user->title(),
			'num_replies' => 0,
			'is_repaged' => false,
			'visits' => 0,
			'keywords_string' => $keywords_string,
		), $data);

		$topic = object_new_instance(__CLASS__, $data);

		if(is_object($message)) // Если это объект — то постинг
			$post = $message;
		else // Если не объект — то текст первого постинга
			$post = balancer_board_post::create($topic, $message, $user, $keywords_string, $as_blog, $data);

		$topic->set_first_post_id($post->id(), true);
		$topic->set_last_post_id($post->id(), true);

		return $topic;
	}

	function topic_updated($post, $notifyed_user = NULL)
	{
		$user = $post->owner();
		$text = "{$post->topic()->title()}:\n"
			.trim(html_entity_decode(make_quote($user->title(), htmlspecialchars($post->source()), false), ENT_COMPAT, 'UTF-8'))
			."\n\n// #{$post->id()} {$post->url_for_igo()} «{$post->topic()->title()}», подписка";

		bors()->do_task('balancer_balabot_notify_post', array(
			'post_id' => $post->id(),
			'text' => $text,
			'notifyed_user' => $notifyed_user,
		));
	}

	function titled_link_new()
	{
		return $this->titled_link_ex(array('page' => 'new'));
	}

	function pre_show()
	{
		balancer_board_posts_view::container_init();
//		jquery_cloudZoom::load();
		jquery_fancybox::appear_all();
		return parent::pre_show();
	}

	function move_to_forum($forum_id)
	{
		// Перекинуть потом подготовку из moderate.php:320
		// А тут пока только перенос связанных forum_id
		// В блогах:
		foreach(bors_find_all('balancer_board_blog', array('topic_id' => $this->id())) as $blog)
			$blog->set_forum_id($forum_id);
	}

	function sort_title()
	{
		return bors_lower(preg_replace('/[^\wа-яА-ЯёЁ]/u', '', $this->title()));
	}

	function forum_title() { return $this->forum()->title(); }
	function title_with_forum() { return $this->title().' ['.$this->forum()->title().']'; }

	function list_fields_format() { return '%title% [%forum_title%]'; }

	function last_post_snip()
	{
		$lp = $this->last_post();
		return object_property($lp, 'author_name');
	}

	function last_post_ctime() { return bors_time::factory($this->last_post_create_time()); }

	function image()
	{
		if($this->__havefc())
			return $this->__lastc();

		$obj = bors_find_first('balancer_board_posts_object', array(
			'inner_join' => array(
				'balancer_board_post ON balancer_board_post.id = balancer_board_posts_object.post_id',
				'`BORS`.`bors_images` i ON balancer_board_posts_object.target_object_id = i.id',
			),
			'topic_id' => $this->id(),
			'`i`.extension<>"gif"',
			'target_class_id' => 202, // airbase_image
			'order' => 'post_id',
		));

		if($obj)
			$obj = $obj->target();
		else
			$obj = false;

		return $obj;
	}

	function image_thumbnail_64()
	{
		if(!($i = $this->image()))
			return NULL;

		return "<a href=\"{$this->url()}\">{$i->thumbnail('100x64(up,crop)')->html_code()}</a>";
	}
}
