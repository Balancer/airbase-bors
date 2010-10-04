<?php

class balancer_board_blogs_record extends base_page_db
{
	function main_db() { return 'punbb'; }
	function main_table() { return 'blog'; }

	function main_table_fields()
	{
		return array(
			'id' => 'post_id',
			'owner_id',
			'forum_id',
			'blogged_time',
		);
	}

	function auto_objects()
	{
		return array(
			'post' => 'balancer_board_post(id)',
		);
	}

	function url() { return $this->post()->url(); }
	function title() { return $this->post()->title(); }
	function body($limit = 512)
	{
		$body = strip_text($this->post()->body(), $limit);
		return $body;
	}
}