<?php

class balancer_board_access_public extends bors_access
{
	function is_balancer() { return bors()->user() && bors()->user()->id() == 10000; }

	function can_action() { return $this->is_balancer(); }
}
