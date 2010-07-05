<?php

class balancer_board_users_rateLowest extends base_page
{
	function title() { return ec('Худшие сообщения'); }
	function config_class() { return 'balancer_board_admin_config'; }
	function can_read() { template_noindex(); return ($me=bors()->user()) ? $me->is_coordinator() : false; }

	function template() { return 'forum/_header.html'; }

	function local_data()
	{
		$lowest = objects_array('bors_votes_thumb', array(
				'group' => 'target_class_name,target_object_id',
				'order' => 'SUM(score)',
				'limit' => 30,
		));

		$lowest_week = objects_array('bors_votes_thumb', array(
				'group' => 'target_class_name,target_object_id',
				'order' => 'SUM(score)',
				'limit' => 50,
				'create_time>' => time()-86400*14,
		));

		bors_objects_targets_preload($lowest);
		bors_objects_targets_preload($lowest_week);
		return compact('lowest', 'lowest_week');
	}
}
