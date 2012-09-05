<?php

function lcml_usernames($text)
{
	if(!config('is_developer'))
		return $text;

	if(preg_match_all("!@([\wа-яА-ЯёЁ\.\-\(]+[\wa-яА-ЯёЁ\)])!u", $text, $match, PREG_SET_ORDER))
	{
//		var_dump($match);
		foreach($match as $m)
		{
//			var_dump($m);
			$name = $m[1];
			$user = bors_find_first('balancer_board_user', array('user_nick' => $name));

			if(!$user)
				$user = bors_find_first('balancer_board_user', array('realname' => $name));

			if(!$user)
				$user = bors_find_first('balancer_board_user', array('login' => $name));

			if($user)
			{
				require_once('inc/images.php');
				$text = str_replace('@'.$name, bors_icon('user.png').$user->titled_link(), $text);
			}
		}
	}

	return $text;
}
