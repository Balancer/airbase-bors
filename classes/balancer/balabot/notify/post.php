<?php

class balancer_balabot_notify_post extends bors_object
{
	function do_work($data)
	{
		$post_id = popval($data, 'post_id');
		$text = popval($data, 'text');
		$post = bors_load('balancer_board_post', $post_id);
		if(!$post)
			return debug_hidden_log('worker_error', 'Not found post '.$post_id);

		$topic = $post->topic();
		if(!$topic)
			return debug_hidden_log('worker_error', 'Not found topic for post '.$post->debug_title());

		$post_owner = $post->owner();

		$users_subscriptions = bors_find_all('balancer_board_users_subscription', array('topic_id' => $topic->id()));
		foreach($users_subscriptions as $us)
		{
			$user = $us->user();
			if(!$user)
				continue;

			if($user->xmpp_notify_enabled() && $user->id() != $post_owner->id())
				$user->notify_text($text);
		}
	}
}
