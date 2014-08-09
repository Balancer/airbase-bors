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
			'title',
			'request_class_name',
			'request_id',
			'target_user_id',
			'target_class_name',
			'target_id',
			'request_data',
			'need_score',
			'have_score',
			'create_time' => array('name' => 'UNIX_TIMESTAMP(`create_ts`)'),
			'modify_time' => array('name' => 'UNIX_TIMESTAMP(`modify_ts`)'),
			'owner_id',
			'last_editor_id',
			'last_editor_ip',
			'last_editor_ua',
		);
	}

	// balancer_board_rpg_request::factory('airbase_rpg_request_warning')
	//		->user($user)
	//		->score_mul(3)
	//		->add();

	static function factory($request_class_name)
	{
		return new balancer_board_rpg_helper($request_class_name);
	}
}
