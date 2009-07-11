<?php

class forum_group extends base_page_db
{
	function main_db() { return 'punbb'; }
	function main_table() { return 'groups'; }
	
	function main_table_fields()
	{
		return array(
			'id' => 'g_id',
			'title' => 'g_title',
			'can_read' => 'g_read_board',
			'can_post' => 'g_post_replies',
			'can_new' => 'g_post_topics',
			'user_title' => 'g_user_title',
			'can_move' => 'can_move',
		);
	}

function can_read() { return @$this->data['can_read']; }
function set_can_read($v, $dbup) { return $this->set('can_read', $v, $dbup); }
function can_post() { return @$this->data['can_post']; }
function set_can_post($v, $dbup) { return $this->set('can_post', $v, $dbup); }
function can_new() { return @$this->data['can_new']; }
function set_can_new($v, $dbup) { return $this->set('can_new', $v, $dbup); }
function user_title() { return @$this->data['user_title']; }
function set_user_title($v, $dbup) { return $this->set('user_title', $v, $dbup); }
function can_move() { return @$this->data['can_move']; }
function set_can_move($v, $dbup) { return $this->set('can_move', $v, $dbup); }
	
	function body() { return ec("Группа '{$this->title()}' (№{$this->id()})"); }
	function is_coordinator() { return $this->can_move(); }
}
