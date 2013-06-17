<?php

class balancer_board_raw_post extends bors_object_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return 'AB_FORUMS'; }
	function table_name() { return 'posts'; }

	function table_fields()
	{
		return array(
			'id',
			'poster',
			'poster_id' => array('class' => 'ab_forums_user', 'have_null' => true),
			'avatar_id',
			'poster_ip',
			'poster_ua',
			'poster_email',
			'title',
			'source',
			'markup_class_name',
			'hide_smilies',
			'posted',
			'edited',
			'edited_by',
			'topic_id',
			'add_type',
			'original_id',
			'answer_to_post_id',
			'answer_to_user_id',
			'order',
			'is_deleted',
			'is_hidden',
			'is_moderatorial',
			'is_incorrect',
			'is_moderated',
			'is_spam',
			'last_moderator_id',
			'field4',
			'field_int',
			'field_str',
		);
	}
}
