<?php

class balancer_board_access_owned extends balancer_board_access_personal
{
	function can_action()
	{
		$owner = $this->id()->get('owner');
		if(!$owner)
			bors_throw(ec('Отсутствует метод owner()'));

		return $owner->id() == bors()->user_id();
	}

	function can_edit() { return $this->is_balancer(); }
}
