<?php
class module_forum_sitenews extends base_page
{
	function body_engine() { return 'body_php'; }

    function local_template_data_set()
    {
		$limit = intval(max(1,min($this->args('limit', 10),100)));

		return array(
			'news' => objects_array('forum_topic', array(
				'forum_id' => 2, 
				'order' => '-posted', 
				'limit' => $limit)),
		);
    }
}
