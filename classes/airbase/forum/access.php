<?php

class airbase_forum_access extends balancer_board_object_db
{
	function table_name() { return 'forum_perms'; }
	function table_fields()
	{
		return array(
			'id' => 'forum_id+group_id',
			'group_id',
			'forum_id',
			'can_read' => 'read_forum',
			'can_post' => 'post_replies',
			'can_new' => 'post_topics',
		);
	}

	static function load_fg($forum_id, $group_id)
	{
		return bors_find_first('airbase_forum_access', array('group_id' => $group_id, 'forum_id' => $forum_id));
	}

	function can_read() { return @$this->data['can_read']; }
	function set_can_read($v, $dbup) { return $this->set('can_read', $v, $dbup); }
	function can_post() { return @$this->data['can_post']; }
	function set_can_post($v, $dbup) { return $this->set('can_post', $v, $dbup); }
	function can_new() { return @$this->data['can_new']; }
	function set_can_new($v, $dbup) { return $this->set('can_new', $v, $dbup); }
}
