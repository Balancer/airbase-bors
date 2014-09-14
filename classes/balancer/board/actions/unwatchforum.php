<?php

class balancer_board_actions_unwatchforum extends bors_page
{
	var $auto_map = true;

	function pre_show()
	{
		$forum_id = $this->id();

		$v = bors_find_first('balancer_board_forums_visit', array(
			'user_id' => bors()->user_id(),
			'forum_id' => $forum_id,
		));

		if($v)
			$v->set_is_disabled(true);
		else
			bors_new('balancer_board_forums_visit', array(
				'user_id' => bors()->user_id(),
				'forum_id' => $forum_id,
				'is_disabled' => true,
			));

		return go_ref();
	}
}
