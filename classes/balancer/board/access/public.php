<?php

class balancer_board_access_public extends bors_access
{
	function is_balancer() { return bors()->user() && bors()->user()->id() == 10000; }

	function can_action() { return $this->is_balancer(); }
	function can_edit() { return $this->is_balancer(); }
	function can_delete() { return $this->is_balancer(); }
}
