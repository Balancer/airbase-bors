<?php

class bal_user_private_feed extends bal_page
{
	function template() { return 'forum/headless.html'; }
	function is_smart() { return true; }
	function body_data()
	{
		if($me = bors()->user())
		{
			$feed = bors_find_all('bal_event', array(
				'user_id' => $me->id(),
				'order' => '-create_time',
				'limit' => 25,
			));
		}
		else
		{
			$feed = NULL;
		}

		return array(
			'feed' => $feed,
		);
	}
}
