<?php

function lt_lj($args)
{
	$user = defval($args, 'user');
	if($user)
	{
		$text = defval($args, 'text', $user);
		return "<a href=\"http://users.livejournal.com/$user/profile\" target=\"_blank\"><img src=\"http://l-stat.livejournal.com/img/userinfo.gif\" width=\"17\" height=\"17\" alt=\"[LJ]\" /></a>&nbsp;<a href=\"http://users.livejournal.com/{$user}/\" target=\"_blank\">$text</a>";
	}

	return "[lj $orig]";
}
