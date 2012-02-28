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
		$last = array();

		foreach(objects_array('bors_votes_thumb', array(
				'target_user_id' => $this->id(),
				'create_time>' => time() - 86400*30,
				'order' => '-create_time',
//				'group' => 'target_class_name,target_object_id',
				'limit' => 100,
		)) as $vote)
			if(empty($last[$idx = $vote->target_class_name().'-'.$vote->target_object_id()]))
				$last[$idx] = $vote;

		$last = array_splice(array_values($last), 0, 30);

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
			'pos_count' => bors_count('bors_votes_thumb', array('target_user_id' => $this->id(), 'score>' => 0)),
			'neg_count' => bors_count('bors_votes_thumb', array('target_user_id' => $this->id(), 'score<' => 0)),
		);
	}

	function pre_show()
	{
		template_noindex();

		if(bors()->client()->is_bot())
			return go('/');

		return false;
	}

	function cache_static() { return config('static_forum') ? rand(600, 1200) : 0; }

	function parents()
	{
		$p = array($this->user()->url());
		if($this->id() == bors()->user_id())
			$p[] = 'http://forums.balancer.ru/personal/';

		return $p;
	}
}
