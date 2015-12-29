<?php

class forum_access_move extends access_base
{
	function can_edit()
	{
		$me = bors()->user();
		if(!$me)
			return false;

		$post = $this->id()->get('post');
		if(is_object($post) && $post->owner() && $post->owner()->id() == $me->id() && !$me->is_destructive())
			return true;

		return $me && $me->can_move();
	}

	function can_read() { return $this->can_edit(); }
}
