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
				'create_time>' => time()-86400*90,
				'group' => 'target_class_id,target_object_id',
				'order' => 'SUM(score)',
				'limit' => 30,
		));

		$lowest_week = objects_array('bors_votes_thumb', array(
				'create_time>' => time()-86400*14,
				'group' => 'target_class_id,target_object_id',
				'order' => 'SUM(score)',
				'limit' => 50,
		));

		$lowest_last = objects_array('bors_votes_thumb', array(
				'create_time>' => time()-86400*14,
				'group' => 'target_class_id,target_object_id',
				'order' => '-create_time',
				'having' => 'SUM(score) <= -4',
		));

		bors_objects_targets_preload($lowest);
		bors_objects_targets_preload($lowest_week);
		bors_objects_targets_preload($lowest_last);

//		$lowest_last = array_filter($lowest_week, function($x) { return $x->object()->warning_id() <= 0;});
		usort($lowest_last, function($x, $y) { return $y->object()->create_time() - $x->object()->create_time(); });
		$lowest_last = array_slice($lowest_last, 0, 20);

		return compact('lowest', 'lowest_week', 'lowest_last');
	}
}
