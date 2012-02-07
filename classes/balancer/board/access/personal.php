<?php

class balancer_board_access_personal extends balancer_board_access_public
{
	function can_action() { return (bool) bors()->user(); }
	function can_edit() { return $this->is_balancer(); }
}
