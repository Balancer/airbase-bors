<?php

//set_loglevel(10,0);
// config_set('debug_trace_object_load', true);

class airbase_board_show_latest extends base_page
{
	function is_loaded() { return @$_COOKIE['user_id'] == 10000 && parent::is_loaded(); }
	function config_class() { return 'airbase_board_config'; }
	function body_engine() { return 'body_php'; }
	function can_cache() { return false; }

	function title() { return ec('Крайние обновившиеся темы'); }
	function nav_name() { return ec('крайние темы'); }

	function body_data()
	{
		$topics = objects_array('balancer_board_topic', array(
				'limit' => 50,
				'order' => '-modify_time',
		));

		$forum_ids = array();
		foreach($topics as $t)
			$forum_ids[$t->forum_id()] = $t->forum_id();

		objects_array('airbase_board_forum', array('id IN' => array_keys($forum_ids)));

		return array(
			'topics' => $topics,
		);
	}
}
