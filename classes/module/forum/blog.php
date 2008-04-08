<?php

class module_forum_blog extends base_page
{
	function main_db_storage(){ return 'punbb'; }

	private $data = array();
	function data_providers()
	{
		$limit = $this->args('limit', 25);
//		$page  = $this->args('limit', 25);
	
		$page_id = $this->page().','.$limit;
	
		if(isset($this->data[$page_id]))
			return $this->data[$page_id];

		$this->data[$page_id] = array(
				'blog_records' => objects_array('forum_blog', array(
					'order' => '-blogged_time',
					'page' => max(1,$this->page()),
					'per_page' => $limit,
				))
			);
		
		return $this->data[$page_id];
	}
}
