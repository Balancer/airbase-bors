<?php

class access_airbase extends access_base
{
	function is_balancer() { return bors()->user() && bors()->user()->id() == 10000; }

	function can_action() { return $this->is_balancer(); }
}
