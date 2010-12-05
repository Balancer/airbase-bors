<?php

class balancer_board_modules_avatar extends bors_module
{
	function body_data()
	{
		$size = $this->args('size', 100);
		$user = $this->args('user');
		$height = $user->avatar_height($size);
		$width  = $user->avatar_width($size);
		$margin_w = max(0, floor(($size - $user->avatar_width())/2));

		return array_merge(parent::body_data(), compact('user', 'size', 'height', 'width', 'margin_w'));
	}
}
