<?php

class forum_group extends base_page_db
{
	function main_db_storage() { return 'punbb'; }
	function main_table_storage() { return 'groups'; }
	
	function field_title_storage() { return 'punbb.groups.g_title(g_id)'; }

	function body() { return ec("Группа '{$this->title()}' (№{$this->id()})"); }

	var $stb_can_read;
	function can_read() { return $this->stb_can_read; }
	function set_can_read($can_read, $db_update) { $this->set("can_read", $can_read, $db_update); }
	function field_can_read_storage() { return 'punbb.groups.g_read_board(g_id)'; }

	var $stb_can_post;
	function can_post() { return $this->stb_can_post; }
	function set_can_post($can_post, $db_update) { $this->set("can_post", $can_post, $db_update); }
	function field_can_post_storage() { return 'punbb.groups.g_post_replies(g_id)'; }

	var $stb_can_new;
	function can_new() { return $this->stb_can_new; }
	function set_can_new($can_new, $db_update) { $this->set("can_new", $can_new, $db_update); }
	function field_can_new_storage() { return 'punbb.groups.g_post_topics(g_id)'; }

	var $stb_user_title;
	function user_title() { return $this->stb_user_title; }
	function set_user_title($user_title, $db_update) { $this->set("user_title", $user_title, $db_update); }
	function field_user_title_storage() { $this->id(); return 'punbb.groups.g_user_title(g_id)'; }
		
	function can_be_empty() { return true; }

	var $stb_can_move;
	function can_move() { return $this->stb_can_move; }
	function set_can_move($can_move, $db_update) { $this->set("can_move", $can_move, $db_update); }
	function field_can_move_storage() { $this->id(); return 'can_move(g_id)'; }
}
