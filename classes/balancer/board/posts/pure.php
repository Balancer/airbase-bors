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
//			'post_body' => 'source_html',
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
}
