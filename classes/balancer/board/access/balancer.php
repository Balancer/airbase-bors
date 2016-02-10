<?php

class balancer_board_access_balancer extends bors_access
{
	function is_balancer() { return bors()->user() && bors()->user()->id() == 10000; }

	function can_read() { return $this->is_balancer(); }
	function can_action($action, $data) { return $this->is_balancer(); }
	function can_edit() { return $this->is_balancer(); }
	function can_delete() { return $this->is_balancer(); }
}
