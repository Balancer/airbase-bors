<?php

class balancer_board_topics_actions extends balancer_board_meta_main
{
	var $auto_map = true;

	var $main_class = 'balancer_board_action';
	var $title = 'Действия над темой';
	var $nav_name = 'действия';

	function where()
	{
		return array(
			'target_class_name IN' => array('balancer_board_topic', 'forum_topic'),
			'target_object_id' => $this->id(),
			'order' => '-create_time',
//			'group' => 'target_class_name, target_object_id, message',
		);
	}

	// bors_objects_preload($data['last_actions'], 'owner_id', 'balancer_board_user', 'owner');
}
