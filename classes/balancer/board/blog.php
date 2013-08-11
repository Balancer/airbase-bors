<?php

class balancer_board_blog extends forum_blog
{
	function extends_class_name() { return 'forum_blog'; }

	static function create($post, $keywords_string, $data = array())
	{
		$topic = $post->topic();
		$user  = $post->owner();

		$data = array_merge($data, array(
			'id'        => $post->id(),
			'owner_id'  => $user->id(),
			'topic_id'  => $topic->id(),
			'forum_id'  => $topic->forum_id(),
			'is_public' => $topic->is_public(),
		));

		$blog = object_new_instance('balancer_board_blog', $data);

		if($keywords_string)
			$blog->set_keywords_string($keywords_string, true);

		common_keyword_bind::add($blog);

		$text = "{$user->title()} пишет:\n"
			.trim($post->source())
			."\n\n// #{$post->id()} {$post->url_for_igo()} в теме {$post->topic()->title()}";

		balancer_board_user::friend_action_notify($user->id(), $text);

		return $blog;
	}

	function auto_targets()
	{
		return array_merge(parent::auto_targets(), array(
			'blog_source' => 'blog_source_class(blog_source_id)',
		));
	}

	function feed_entry()
	{
		if($fe = $this->blog_source())
			return $fe;

		$fe = bors_find_first('bors_external_feeds_entry', array(
			'target_class_name IN' => array('balancer_board_post', 'forum_post'),
			'target_object_id' => $this->id(),
			'order' => 'id',
		));

		if($fe)
		{
			$this->set_blog_source_class($fe->class_name());
			$this->set_blog_source_id($fe->id());
			return $fe;
		}

		$fe = bors_find_first('bors_external_feeds_entry', array(
			'target_object_id' => 0,
			'create_time' => $this->blogged_time(),
		));

		if(!$fe)
		{
			$fe = bors_find_first('bors_external_feeds_entry', array(
				'create_time' => $this->blogged_time(),
			));
		}

		if($fe)
		{
			$post = $this->post();

			if($fe->text() == $post->source())
			{
				$this->set_blog_source_class($fe->class_name());
				$this->set_blog_source_id($fe->id());
				$this->store();

				$fe_post = $fe->target();

				if(!$fe_post)
				{
					$fe->set_target_class_name($post->class_name());
					$fe->set_target_object_id($post->id());
				}
			}

			return $fe;
		}

		return NULL;
	}

	function recalculate($post = NULL, $topic = NULL)
	{
		if(empty($post))
			$post = $this->post();

		if(empty($topic))
			$topic = $this->topic();

		if(empty($topic))
		{
			debug_hidden_log('lost_topic', "Lost topic {$this->topic_id()} for post {$this}");
			return;
		}

		$this->set_owner_id($post->owner_id(), true);
		$this->set_topic_id($topic->id(), true);
		$this->set_forum_id($topic->forum_id(), true);
		$this->set_is_public($topic->is_public(), true);

//		if(config('is_developer')) { var_dump($this->feed_entry()); exit(); }
		if($feed_entry = $this->feed_entry())
		{
			$feed_entry->recalculate();
			$keywords = array_filter(array_unique(array_merge($this->keywords(), $feed_entry->keywords())));
			$this->set_keywords($keywords, true);
		}

		common_keyword_bind::add($this);
	}

	function html() { return $this->post()->html(); }
	function source() { return $this->post()->source(); }
}
