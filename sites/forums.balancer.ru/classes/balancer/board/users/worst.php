<?php

class balancer_board_users_worst extends balancer_board_paginated
{
	var $main_class = 'bors_votes_thumb';
	var $group = 'target_class_name,target_object_id';
	var $order = 'SUM(score)';

	function where()
	{
		 return ['target_user_id' => $this->id()];
	}
}
