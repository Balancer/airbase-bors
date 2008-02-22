<?php

// Глобальный класс для общих данных

class bors_global extends base_empty
{
	private $user = NULL;
	function user()
	{
		if($this->user == NULL)
			$this->user = object_load(config('user_class'), -1);
		return $this->user;
	}
}
