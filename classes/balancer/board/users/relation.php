<?php

class balancer_board_users_relation extends balancer_board_object_db
{
	function table_name() { return 'user_relations'; }

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
			'ignore',
		);
	}

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), array(
			'from_user' => 'balancer_board_user(from_user_id)',
			'to_user' => 'balancer_board_user(to_user_id)',
		));
	}

	static function set_ignore($from_uid, $to_uid)
	{

		if($from_uid < 2 || $to_uid < 2)
			return;

//		bors_debug::syslog('funs/ignore', "Ignore from $from_uid to $to_uid");

		$rel = self::find(['from_user_id' => $from_uid, 'to_user_id' => $to_uid])->first();

		if($rel && !$rel->get('is_empty'))
			return $rel->set('ignore', true);

		$rel = bors_new(__CLASS__, [
			'from_user_id' => $from_uid,
			'to_user_id' => $to_uid,
			'from_user_name' => airbase_user::load($from_uid)->title(),
			'to_user_name' => airbase_user::load($to_uid)->title(),
			'ignore' => true,
		]);

//		var_dump($rel); exit();
//		$rel->store();
		return $rel;
	}

	static function unset_ignore($from_uid, $to_uid)
	{
		if($from_uid < 2 || $to_uid < 2)
			return;

		$rel = self::find(['from_user_id' => $from_uid, 'to_user_id' => $to_uid])->first();

		if($rel)
			return $rel->set('ignore', false);

		return NULL;
	}
}
