<?php

class balancer_board_admin_posts_spam extends bors_paginated
{
	var $title_ec = 'Сообщения с возможным спамом';
	var $nav_name_ec = 'спам';
	var $is_auto_url_mapped_class = true;
	var $config_class = 'balancer_board_admin_config';
	var $access_type = 'move';
	function template() { return 'forum/_header.html'; }

	var $main_class = 'balancer_board_post';
	function where()
	{
		return array(
			'inner_join' => 'balancer_board_topic ON balancer_board_topic.id = topic_id',
			'is_spam' => 1,
			'is_moderated IS NULL',
			'forum_id<>' => 191,
		);
	}

	function selected_posts() { return array_filter(@explode(',', @$_COOKIE['selected_posts'])); }
	function is_filtered_spam() { return !empty($_GET) && @$_GET['type'] == 'spam'; }
	function is_filtered_ham()  { return !empty($_GET) && @$_GET['type'] == 'ham'; }

	function is_filtered()  { return !empty($_GET) && @$_GET['type']; }

	function local_data()
	{
		return array_merge(parent::local_data(), array(
			'show_spam' => true,
			'is_filtered_spam' => $this->is_filtered_spam(),
			'is_filtered_ham'  => $this->is_filtered_ham(),
			'items' => $this->is_filtered() ? objects_array('balancer_board_post', array(
				'id IN' => $this->selected_posts(),
			)) : parent::items(),
		));
	}

	function on_action_mark_do()
	{
		$type = @$_GET['type'];
		if($type != 'spam' && $type != 'ham')
			return bors_message(ec('Ошибка типа'));

		$is_spam = $type == 'spam';

		foreach(objects_array('balancer_board_post', array('id IN' => $this->selected_posts())) as $post)
		{
			if(!$is_spam && $post->is_spam())
				balancer_akismet::factory()->submit_ham($post);

			if($is_spam && is_null($post->is_moderated()))
			{
				balancer_board_ban::ban($post->owner(), $post->poster_ip(), false, $post);
				balancer_akismet::factory()->submit_spam($post);
			}

			$post->set_is_spam($is_spam, true);
			$topic = $post->topic();
			if($is_spam && $topic->first_post_id() == $post->id())
			{
				$topic->set_forum_id(191, true);
				$topic->recalculate();
			}
			else
				echo $topic->debug_titled_link().": ".$topic->num_replies()."<br/>";
		}

	   	SetCookie('selected_posts', NULL, 0, '/');

		return go('http://forums.balancer.ru/admin/posts/spam');
	}
}
