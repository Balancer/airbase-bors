<?php

class livestreet_topic extends bors_object_db
{
	function db_name() { return 'LIVESTREET'; }
	function table_name() { return 'ls_topic'; }

	function table_fields()
	{
		return array(
			'id' => 'topic_id',
			'blog_id',
			'owner_id' => 'user_id',
			'topic_type',
			'title' => 'topic_title',
			'topic_tags' => array('title' => 'tags separated by a comma'),
			'create_time' => 'UNIX_TIMESTAMP(topic_date_add)',
			'modify_time' => 'UNIX_TIMESTAMP(topic_date_edit)',
			'topic_user_ip',
			'topic_publish',
			'topic_publish_draft',
			'topic_publish_index',
			'topic_rating',
			'topic_count_vote',
			'topic_count_vote_up',
			'topic_count_vote_down',
			'topic_count_vote_abstain',
			'topic_count_read',
			'num_replies' => 'topic_count_comment',
			'topic_count_favourite',
			'topic_cut_text',
			'topic_forbid_comment',
			'topic_text_hash',
		);
	}

	function url_ex($page) { return "http://ls.balancer.ru/blog/{$this->id()}.html"; }

	function author_name() { return object_property($this->owner(), 'title'); }

	function forum() { return NULL; }

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), array(
			'owner' => 'balancer_board_user(owner_id)',
		));
	}

	function body()
	{
		$content = bors_load('livestreet_topics_content', $this->id());
		return $content ? $content->data['body'] : NULL;
	}

	function __dev()
	{
		var_dump(bors_load(__CLASS__, 95));
	}

	function topic() { return $this; }
	function is_deleted() { return false; }
}
