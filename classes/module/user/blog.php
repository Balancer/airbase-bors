<?php

class module_user_blog extends base_page
{
	private $user;
	
	function main_db_storage(){ return 'punbb'; }

	function local_template_data_set()
	{
		return array(
			'blog_records' => objects_array('forum_blog', array(
				'owner_id' => $this->args('owner_id'),
				'limit' => $this->args('limit', 5),
				'order' => '-blogged_time',
			)),
			
			'skip_avatar_block' => $this->args('skip_avatar_block', false),
		);
	}
}