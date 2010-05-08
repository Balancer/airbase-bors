<?php

class balancer_user_votes extends base_page
{
	function title() { return ec('Оценки сообщений пользователя ').$this->user()->title(); }
	function config_class() { return 'balancer_board_config'; }

	function auto_objects()
	{
		return array(
			'user' => 'balancer_board_user(id)',
		);
	}

	function local_data()
	{
		$last = objects_array('bors_votes_thumb', array(
				'order' => '-create_time',
				'limit' => 30,
				'target_user_id' => $this->id(),
		));

		$best = objects_array('bors_votes_thumb', array(
				'group' => 'target_class_name,target_object_id',
				'order' => 'SUM(score) DESC',
				'having' => 'SUM(score)>0',
				'limit' => 20,
				'target_user_id' => $this->id(),
		));

		$worst = objects_array('bors_votes_thumb', array(
				'group' => 'target_class_name,target_object_id',
				'having' => 'SUM(score)<0',
				'order' => 'SUM(score)',
				'limit' => 10,
				'target_user_id' => $this->id(),
		));

		bors_objects_targets_preload($last);
		bors_objects_targets_preload($best);
		bors_objects_targets_preload($worst);

		return array(
			'last' => $last,
			'best' => $best,
			'worst' => $worst,
		);
	}

	function pre_show()
	{
		templates_noindex();

		if(bors()->client()->is_bot())
			return go('/');

		return false;
	}

	function cache_static() { return rand(600, 1200); }

	function parents() { return array($this->user()->url()); }
}
