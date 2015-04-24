<?php
//TODO: под снос, если не нужна статика
class forum_js_topvisits extends base_js
{
	function body_data()
	{
		$top = objects_array('balancer_board_topic', array(
				'num_views>=' => 10,
				'last_visit - first_visit > 600',
				'order' => '(86400*num_views)/(last_visit-first_visit) DESC',
				'forum_id NOT IN' => array(37),
				'limit' => 20,
		));

		return array('top' => $top);
	}

	function cache_static() { return config('static_forum') ? rand(3600,7200) : 0; }
//	function url_engine() { return 'url_calling'; }
}
