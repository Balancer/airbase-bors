<?php

class balancer_board_post extends forum_post
{
	function extends_class() { return 'forum_post'; }

	function is_public_access() { return $this->topic() && $this->topic()->forum_id() && $this->topic()->forum()->is_public_access(); }
}
