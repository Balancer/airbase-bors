<?php

class balancer_board_modules_attaches extends bors_module
{
	function body_data()
	{
		return array_merge(parent::body_data(), array(
			'items' => $this->args('items'),
		))
	}
}
