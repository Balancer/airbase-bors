<?php

function lt_ab($args)
{
	$user_name = defval($args, 'user');
	if($user_name)
	{
		require_once('inc/images.php');
		$user = bors_find_first('balancer_board_user', array('title' => $user_name));
		if(!$user)
			$user = bors_find_first('balancer_board_user', array('username' => $user_name));
		if(!$user)
			$user = bors_find_first('balancer_board_user', array('user_nick' => $user_name));

		if(!$user)
			return "$user_name";

		$text = defval($args, 'text', $user_name);
		return "<a href=\"{$user->url()}\">".bors_icon('user.png')."</a>&nbsp;<a href=\"{$user->url()}blog/\">{$text}</a>";
	}

	$uid = intval(defval($args, 'uid'));
	if($uid)
	{
		require_once('inc/images.php');
		$user = bors_load('balancer_board_user', $uid);
		if($user)
		{
			$text = defval($args, 'text', $user->title());
			return "<a href=\"{$user->url()}\">".bors_icon('user.png')."</a>&nbsp;<a href=\"{$user->url()}blog/\">{$text}</a>";
		}
	}

	return "[ab $orig]";
}
