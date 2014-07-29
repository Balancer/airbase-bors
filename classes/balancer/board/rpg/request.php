<?php

class balancer_board_rpg_request extends balancer_board_object_db
{
	var $class_title = 'RPG запрос';
	var $class_title_rp = 'RPG запроса';

	function table_name() { return 'rpg_requests'; }

	function table_fields()
	{
		return array(
			'id',
			'request_class_name',
			'request_id',
			'request_data',
			'need_score',
			'have_score',
			'create_ts' => array('name' => 'UNIX_TIMESTAMP(`create_ts`)'),
			'modify_ts' => array('name' => 'UNIX_TIMESTAMP(`modify_ts`)'),
			'owner_id',
			'last_editor_id',
			'last_editor_ip',
			'last_editor_ua',
		);
	}
}
