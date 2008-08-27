<?php

class forum_access_move extends access_base
{
	function can_edit()
	{
		$me = bors()->user();
	
		$post = $this->id()->post();
		if($post && $post->owner()->id() == $me->id())
			return true;

		return $me && $me->group()->can_move();
	}

	function can_read() { return $this->can_edit(); }
}
