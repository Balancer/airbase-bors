<?php

class airbase_user_admin_access extends access_base
{
	function can_action()
	{
		$me = bors()->user();
		if(!in_array($me->group_id(), array(1,2,5,21)))
			return false;
			
		return true;
	}
}
