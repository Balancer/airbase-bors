<?php

class balancer_board_posts_process extends balancer_board_pages_simple
{
	function title()
	{
		return 'Обработка сообщения в теме «'.$this->post()->topic()->title().'»';
	}

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), array(
			'post' => 'balancer_board_post(id)',
		));
	}
}
