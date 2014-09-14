<?php

class balancer_board_users_relations_quartal extends balancer_board_object_db
{
	function table_name() { return 'user_relations_quartal'; }

	function table_fields()
	{
		return array(
			'id',
			'from_user_id',
			'to_user_id',
			'from_user_name',
			'to_user_name',
			'votes_plus',
			'votes_minus',
			'reputations_plus',
			'reputations_minus',
			'score',
		);
	}

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), array(
			'from_user' => 'balancer_board_user(from_user_id)',
			'to_user' => 'balancer_board_user(to_user_id)',
		));
	}
}
