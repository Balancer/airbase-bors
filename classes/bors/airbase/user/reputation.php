<?php

class airbase_user_reputation extends base_page_db
{
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
}
