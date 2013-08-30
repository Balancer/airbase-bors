<?php

class livestreet_blog extends bors_object_db
{
	function db_name() { return 'LIVESTREET'; }
	function table_name() { return 'ls_blog'; }

	function table_fields()
	{
		return array(
			'id' => 'blog_id',
			'owner_id' => 'user_owner_id',
			'title' => 'blog_title',
			'description' => 'blog_description' => array('type' => 'bbcode'),
			'blog_type',
			'create_time' => 'UNIX_TIMESTAMP(`blog_date_add`)',
			'modify_time' => 'UNIX_TIMESTAMP(`blog_date_edit`)',
			'blog_rating',
			'blog_count_vote',
			'blog_count_user',
			'blog_count_topic',
			'blog_limit_rating_topic',
			'blog_url',
			'blog_avatar',
			'airbase_forum_id',
		);
	}

	function url($page=NULL) { return "http://ls.balancer.ru/blog/{$this->blog_url()}/"; }
}
