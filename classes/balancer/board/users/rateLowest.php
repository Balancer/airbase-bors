<?php

class balancer_board_users_rateLowest extends base_page
{
	function title() { return ec('Худшие сообщения'); }
	function config_class() { return 'balancer_board_admin_config'; }
	function can_read() { templates_noindex(); return ($me=bors()->user()) ? $me->is_coordinator() : false; }

	function template() { return 'forum/_header.html'; }

	function local_data()
	{
		return array(
			'lowest' => objects_array('bors_votes_thumb', array(
				'group' => 'target_class_name,target_object_id',
				'order' => 'SUM(score)',
				'limit' => 30,
				)),
		);
	}
}
