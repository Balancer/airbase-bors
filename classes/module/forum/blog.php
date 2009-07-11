<?php

class module_forum_blog extends base_page
{
	function main_db_storage(){ return 'punbb'; }

	private $_data = array();
	function local_data()
	{
		$limit = $this->args('limit', 25);
//		$page  = $this->args('limit', 25);
	
		$page_id = $this->page().','.$limit;
	
		if(isset($this->_data[$page_id]))
			return $this->_data[$page_id];

		$this->_data[$page_id] = array(
				'blog_records' => objects_array('forum_blog', array(
					'order' => '-blogged_time',
					'page' => max(1,$this->page()),
					'per_page' => $limit,
					'forum_id NOT IN' => array(19, 37, 102, 138, 170),
				)),
				'no_show_answers' => true,
				'skip_message_footer' => true,
			);
		
		return $this->_data[$page_id];
	}
}
