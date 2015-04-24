<?php
class module_forum_sitenews extends bors_page
{
	function body_engine() { return 'body_php'; }

    function body_data()
    {
		$limit = intval(max(1,min($this->args('limit', 10),100)));

		return array(
			'news' => objects_array('balancer_board_topic', array(
				'forum_id' => 2, 
				'order' => '-posted', 
				'limit' => $limit)),
		);
    }
}
