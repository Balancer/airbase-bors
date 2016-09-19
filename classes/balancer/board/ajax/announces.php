<?php

class balancer_board_ajax_announces extends bors_page
{
	var $auto_map = true;

	function content() { return $this->body(); }

	function body_data()
	{
		$w = intval(defval($_GET, 'w', 1000));

		$n = max(3, floor($w/308));

		return array_merge(parent::body_data(), [
			'width' => $w,
			'num' => $n,
			'announces' => balancer_board_announce::find()
//				->order('-create_time')
				->order('SQRT(UNIX_TIMESTAMP()-UNIX_TIMESTAMP(`create_ts`))*RAND()')
//				->order('RAND()')
				->all($n*2),
		]);
	}
}
