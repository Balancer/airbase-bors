<?php

class balancer_board_modules_dated extends bors_module
{
	function body_data()
	{
		return array('items' => $this->arg('items'));
	}
}
