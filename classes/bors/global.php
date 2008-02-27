<?php

// Глобальный класс для общих данных

class bors_global extends base_empty
{
	private $user = NULL;
	function user()
	{
		if($this->user === NULL)
		{
			global $me;
			if(empty($me) || !is_object($me))
				$me = &new User();

			$id = $me->get('id');

			if(!$id || $id == 1)
				return $this->user = false;
//			echo "Current user id = $id<br />";

			$this->user = object_load(config('user_class'), $id);
		}
		
		return $this->user;
	}
}
