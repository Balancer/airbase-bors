<?php

function bors_check_access($object, $access_list, $user=NULL)
{
	if(!$user)
		$user = bors()->user();

//	debug_exit($access_list);

	foreach($access_list as $section => $need_level)
		if($user->check_access($section, $need_level, false))
			return true;
	
//	bors_message(ec("Недостаточный уровень доступа"));
	
	return false;
}
