<?php

class forum_access_move extends access_base
{
	function can_edit()
	{
		$me = bors()->user();
	
		if($this->id()->post()->owner()->id() == $me->id())
			return true;

		return $me->group()->can_move();
	}

	function can_read() { return $this->can_edit(); }
}
