<?php

class airbase_access_balancer extends bors_admin_access
{
	function can_action($action, $data)
	{
		$me = bors()->user();
		if(!$me || !in_array($me->id(), array(10000)))
			return false;

		return true;
	}
}
