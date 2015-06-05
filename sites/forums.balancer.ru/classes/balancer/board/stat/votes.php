<?php

class balancer_board_stat_votes extends balancer_board_page
{
	var $title = 'Статистика по оценкам сообщенй';
	var $nav_name = 'Оценки';

	function cache_static() { return rand(3600, 7200); }

	function body_data()
	{
		$best_all = bors_find_all('bors_votes_thumb', array(
			'group' => 'target_class_name,target_object_id',
			'having' => 'SUM(score) > 0',
			'order' => 'SUM(score) DESC',
			'limit' => 20,
		));

		$best_of_year = bors_find_all('bors_votes_thumb', array(
			'create_time>' => time()-86400*31,
			'group' => 'target_class_name,target_object_id',
			'having' => 'SUM(score) > 0',
			'order' => 'SUM(score) DESC',
			'limit' => 20,
		));

		$worst_all = bors_find_all('bors_votes_thumb', array(
			'group' => 'target_class_name,target_object_id',
			'having' => 'SUM(score) < 0',
			'order' => 'SUM(score)',
			'limit' => 20,
		));

		$worst_of_year = bors_find_all('bors_votes_thumb', array(
			'create_time>' => time()-86400*31,
			'group' => 'target_class_name,target_object_id',
			'having' => 'SUM(score) < 0',
			'order' => 'SUM(score)',
			'limit' => 20,
		));

		bors_objects_targets_preload($best);
		bors_objects_targets_preload($best_of_year);
		bors_objects_targets_preload($worst);
		bors_objects_targets_preload($worst_of_year);

		return array_merge(parent::body_data(), compact(
			'best_all',
			'best_of_year',
			'worst_all',
			'worst_of_year'
		));
	}
}
