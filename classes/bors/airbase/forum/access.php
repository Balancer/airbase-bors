<?php

class airbase_forum_access extends base_object_db
{
	private $forum_id = '';
	private $group_id = '';

	function main_db() { return config('punbb.database', 'punbb'); }
	function main_table() { return 'forum_perms'; }

	function __construct($id)
	{
		list($this->forum_id, $this->group_id) = explode(':', $id);
//		echo $this->forum_id.','. $this->group_id;
		parent::__construct($id);
	}

	function main_table_fields()
	{
		return array(
			'id' => "group_id=".intval($this->group_id)." AND forum_id=".intval($this->forum_id),
			'can_read' => 'read_forum',
			'can_post' => 'post_replies',
			'can_new' => 'post_topics',
		);
	}

function can_read() { return @$this->data['can_read']; }
function set_can_read($v, $dbup) { return $this->set('can_read', $v, $dbup); }
function can_post() { return @$this->data['can_post']; }
function set_can_post($v, $dbup) { return $this->set('can_post', $v, $dbup); }
function can_new() { return @$this->data['can_new']; }
function set_can_new($v, $dbup) { return $this->set('can_new', $v, $dbup); }
}
