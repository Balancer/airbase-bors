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

	function feed_entry()
	{
		return bors_find_first('bors_external_feeds_entry', array(
			'target_class_name' => 'balancer_board_post',
			'target_object_id' => $this->id(),
		));
	}

	function recalculate($post = NULL, $topic = NULL)
	{
		if(empty($post))
			$post = $this->post();

		if(empty($topic))
			$topic = $this->topic();

		$this->set_owner_id($post->owner_id(), true);
		$this->set_topic_id($topic->id(), true);
		$this->set_forum_id($topic->forum_id(), true);
		$this->set_is_public($topic->is_public(), true);

		if($feed_entry = $this->feed_entry())
		{
			$feed_entry->recalculate();
			$keywords = array_filter(array_unique(array_merge($this->keywords(), $feed_entry->keywords())));
			$this->set_keywords($keywords, true);
		}

		common_keyword_bind::add($this);
	}
}
