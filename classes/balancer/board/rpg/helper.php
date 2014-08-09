<?php

class balancer_board_rpg_helper extends bors_object
{
	// balancer_board_rpg_request::factory('airbase_rpg_request_warning')
	//		->set_user($user)
	//		->set_score_mul(3)
	//		->add();

	function set_user($user)
	{
		$this->set_attr('target_user_id',  $user->id());
		$this->set_attr('need_score', pow(3, $user->rpg_level()));
		return $this;
	}

	function set_target($target)
	{
		$this->set_attr('target_class_name',  $target->new_class_name());
		$this->set_attr('target_id',  $target->id());
		return $this;
	}

	function set_data($data)
	{
		$this->set_attr('request_data', $data);
		return $this;
	}

	function set_title($title)
	{
		$this->set_attr('title', $title);
		return $this;
	}

	function add($request_id = NULL)
	{
		return bors_new('balancer_board_rpg_request', array(
			'request_class_name' => $this->id(),
			'title' => $this->get('title'),
			'request_id' => $this->get('request_id', $request_id),
			'target_user_id' => $this->get('target_user_id'),
			'target_class_name' => $this->get('target_class_name'),
			'target_id' => $this->get('target_id'),
			'request_data' => $this->get('request_data') ? json_encode($this->get('request_data')) : NULL,
			'need_score' =>  $this->get('need_score'),
		));
	}
}
