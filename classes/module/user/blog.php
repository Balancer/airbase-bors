<?php

class module_user_blog extends base_page
{
	private $user;

	function main_db() { return 'punbb'; }

	function body_data()
	{
		return array(
			'blog_records' => objects_array('balancer_board_blog', array(
				'owner_id' => $this->args('owner_id'),
				'limit' => $this->args('limit', 5),
				'order' => '-blogged_time',
				'is_public' => 1,
			)),

			'skip_avatar_block' => $this->args('skip_avatar_block', false),
		);
	}
}
