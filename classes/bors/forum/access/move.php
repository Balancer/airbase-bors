<?php

class forum_access_move extends access_base
{
	function can_edit()
	{
		$me = bors()->user();
		if(!$me)
			return false;
	
		$post = $this->id()->post();
		if(is_object($post) && $post->owner() && $post->owner()->id() == $me->id())
			return true;

		return $me && $me->group()->can_move();
	}

	function can_read() { return $this->can_edit(); }
}
