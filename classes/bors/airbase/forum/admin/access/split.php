<?php

class airbase_forum_admin_access_split extends access_base
{
	function can_edit()
	{
		$me = bors()->user();
	
		return $me && $me->group()->can_move();
	}

	function can_read() { return $this->can_edit(); }
}
