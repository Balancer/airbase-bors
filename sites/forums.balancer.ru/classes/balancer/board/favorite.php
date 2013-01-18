<?php

class balancer_board_favorite extends base_object_db
{
	function main_db() { return 'AB_FORUMS'; }
	function main_table() { return 'favorites'; }
	function main_table_fields()
	{
		return array(
			'id',
			'owner_id',
			'target_class_name',
			'target_object_id',
			'create_time',
			'modify_time',
		);
	}

	function auto_targets()
	{
		return array_merge(parent::auto_targets(), array(
			'target' => 'target_class_name(target_object_id)',
		));
	}
}
