<?php

class balancer_board_posts_pure extends balancer_board_object_db
{
	function table_name() { return 'posts'; }

	function table_fields()
	{
		return array(
			'id',
			'title_raw' => 'title',
			'topic_id',
			'topic_page' => 'page',
			'create_time'	=> 'posted',
			'edited',
			'edited_by',
			'owner_id' => 'poster_id',
			'avatar_raw_id' => 'avatar_id',
			'poster_ip',
			'poster_email',
			'poster_ua',
			'author_name' => 'poster',
			'answer_to_id' => 'answer_to_post_id',
			'answer_to_user_id',
			'post_source' => 'source',
			'hide_smilies',
			'have_attach',
			'have_cross',
			'have_answers',
			'score',
			'is_moderatorial',
			'is_deleted',
			'is_hidden',
			'is_spam',
			'is_incorrect',
			'last_moderator_id',
			'sort_order' => '`order`',
			'markup_class_name',
		);
	}

// Заняты: 	field1 => title
//			field2 => score

// Свободны:field3 string
//			field4 => is_spam int(11)

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), array(
			'owner' => 'airbase_user(owner_id)',
//			'answer_to' => 'airbase_user(answer_to_id)',
		));
	}

	function url_in_container()
	{
		$pid = $this->id();

		$tid = $this->topic_id();

		if(!$tid)
			return "топик [topic_id={$this->topic_id()}, post_id={$this->id()}] не найден";

		$topic = bors_load('balancer_board_topic', $tid);

		if(!$topic)
			return "топик [topic_id={$this->topic_id()}, post_id={$this->id()}] не найден";

		if(!$topic->is_repaged())
		{
			$topic->repaging_posts();
			$post = bors_load($this->class_name(), $this->id());
		}
		else
			$post = $this;

		return $topic->url_ex($post->topic_page())."#p".$post->id();
	}
}
