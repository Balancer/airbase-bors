<?php

class balancer_board_tasks_recalculate extends bors_object
{
	function do_work($target)
	{
		$target->recalculate();
		return false;
	}
}
