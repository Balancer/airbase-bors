<?php

class balancer_board_access_admin extends bors_access
{
	function can_read() { return $this->can_action(); }
	function can_edit() { return $this->can_action(); }
	function can_action() { return object_property(bors()->user(), 'is_admin', false); }
}
