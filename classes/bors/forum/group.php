<?php

class forum_group extends base_page_db
{
	function main_db_storage() { return 'punbb'; }
	function main_table_storage() { return 'groups'; }
	
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
	
	function body() { return ec("Группа '{$this->title()}' (№{$this->id()})"); }
	function is_coordinator() { return $this->can_move(); }
//	function can_be_empty() { return true; }
}
