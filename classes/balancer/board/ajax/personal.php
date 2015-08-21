<?php

class balancer_board_ajax_personal extends bors_module
{
	function body_data()
	{
		return array_merge(parent::body_data(), array(
			'owner' => bors()->user(),
			'page' => $this->args('object')->page(),
		));
	}
}
