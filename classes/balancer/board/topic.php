<?php

class balancer_board_topic extends forum_topic
{
	function extends_class() { return 'forum_topic'; }

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
		$dbh = new driver_mysql(config('punbb.database', 'punbb'));
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

	static function create($forum, $title, $message, $user, $keywords_string = NULL, $as_blog = true, $data = array())
	{
		echo "Pass new topic to {$forum->debug_title()}\n";

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

		$post = balancer_board_post::create($topic, $message, $user, $keywords_string, $as_blog, $data);

		$topic->set_first_post_id($post->id(), true);
		$topic->set_last_post_id($post->id(), true);

		return $topic;
	}

	function topic_updated($post)
	{
		$user = $post->owner();
		$text = "{$user->title()} пишет:\n"
			.trim($post->source())
			."\n\n// #{$post->id()} {$post->url_for_igo()} в теме «{$post->topic()->title()}»";

		bors()->do_task('balancer_balabot_notify_post', array('post_id' => $post->id(), 'text' => $text));
	}
}
