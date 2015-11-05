<?php

class airbase_money_log extends bors_object_db
{
	function db_name() { return 'AB_FORUMS'; }
	function table_name() { return 'ab_money_log'; }

	function table_fields()
	{
		return array(
			'id',
			'user_id' => ['class' => 'balancer_board_user'],
			'amount',
			'result',
			'action',
			'comment',
			'source_id',
			'object_class_name',
			'object_id',
			'create_time' => ['name' => 'UNIX_TIMESTAMP(`create_ts`)'],
			'modify_time' => ['name' => 'UNIX_TIMESTAMP(`modify_ts`)'],
			'owner_id',
			'last_editor_id',
			'last_editor_ip',
			'last_editor_ua',
		);
	}

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), [
			'user'  => 'balancer_board_user(user_id)',
			'owner' => 'balancer_board_user(owner_id)',
		]);
	}

	function auto_targets()
	{
		return array_merge(parent::auto_targets(), [
			'target' => 'object_class_name(object_id)',
		]);
	}

	static function add($user, $amount, $action, $comment=NULL, $object=NULL, $source=NULL)
	{
		$result = $user->money() + $amount;

		$data = [
			'user_id' => $user->id(),
			'amount' => $amount,
			'result' => $result,
			'action' => $action,
			'comment' => $comment,
		];

		if($object)
		{
			$data['object_class_name'] = $object->class_name();
			$data['object_id'] = $object->id();
		}

		if($source)
			$data['source_id'] = $source->id();

		$log = bors_new('airbase_money_log', $data);
//		$user->set_money($result, true);
		$user->save();
	}
}
