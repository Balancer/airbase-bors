<?php

class balancer_balabot_notify_post extends bors_object
{
	function do_work($data)
	{
		$post_id = popval($data, 'post_id');
		$text = popval($data, 'text');
		$notifyed_user = popval($data, 'notifyed_user');

		if(is_object($notifyed_user))
			$notifyed_user_id = $notifyed_user->id();
		else
			$notifyed_user_id = false;

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

			$user_id = $user->id();
			if($user_id == $notifyed_user_id || $user_id == $post_owner->id())
				continue;

			if($user->xmpp_notify_enabled())
				$user->notify_text($text);
		}
	}
}
