<?php

function bors_check_access($object, $access_list, $user=NULL)
{
	if(!$user)
		$user = object_load('aviaport_user', -1);

//	debug_exit($access_list);

	foreach($access_list as $section => $need_level)
		if(!$user->check_access($section, $need_level, true))
			return false;
	
	return true;
}
