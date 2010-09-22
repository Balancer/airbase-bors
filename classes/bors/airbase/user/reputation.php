<?php

class airbase_user_reputation extends base_page_db
{
	function class_title_vp() { return ec('запись репутации'); }

	function fields()
	{
		return array('USERS' => array('reputation_votes' => array(
			'id',
			'user_id',
			'voter_id',
			'owner_id' => 'voter_id',
			'create_time' => 'time',
			'comment',
			'refer' => 'uri',
			'score',
			'is_deleted',
		)));
	}

	function owner() { return object_load('bors_user', $this->owner_id()); }
	function target_user() { return object_load('bors_user', $this->user_id()); }
	function class_title() { return 'Запись в репутации'; }
	function title() { return ($this->score() > 0 ? '+1' : '-1').' от '.$this->owner()->title().' к '.$this->target_user()->title(); }
	function titled_url() { return $this->title(); }

	function cache_groups_parent() { return "user-{$this->user_id()}-reputation"; }

	function refer_link()
	{
		$ref = $this->refer();
		if(preg_match('/^\w+__\d+$/', $ref))
		{
			$object = object_load($ref);
			return object_property($object, 'titled_url');
		}

		if(preg_match('!post://(\d+)/?!', $ref, $m))
		{
			$object = object_load('balancer_board_post', $m[1]);
			return object_property($object, 'titled_url');
		}

		$object = object_load($ref);
		if(is_a($object, 'bors_system_go_internal'))
			$object = $object->target();

		if($object)
			return $object->titled_url();

		return $ref;
	}
}
