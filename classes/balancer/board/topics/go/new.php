<?php

class balancer_board_topics_go_new extends bors_object
{
	var $auto_map = true;

	function pre_show()
	{
		$me = bors()->user();

		if($me)
			$me->utmx_update();

		// Если регистрант
		if($me && $me->id() >= 2)
		{
			$uid = $me->id();

			$visit = bors_find_first('balancer_board_topics_visit', [
				'user_id' => $uid,
				'target_object_id' => $this->id()
			]);

			if($visit)
				$last_visit = $visit->last_visit();
			else
				// Если отметки о чтении топика нет, то считаем за дату последнего посещения
				// дату модификации самой старой записи в таблице посещений.
				$last_visit = bors_find_first('balancer_board_topics_visit', ['last_visit>' => 0])->modify_time();

			// Первое нечитанное сообщение темы
			$first_new_post = bors_find_first('balancer_board_posts_pure', [
				'topic_id' => $this->id(),
				'posted>' => $last_visit,
				'order' => 'sort_order,id',
			]);

			// Если мы нашли первое нечитанное сообщение, то переходим к нему
			if($first_new_post)
				return go($first_new_post->url_in_container());

			// Если не нашли, то тупо переходим на последнее сообщение.
			$last_post = bors_find_first('balancer_board_posts_pure', [
				'topic_id' => $this->id(),
				'use_index' => 'by_tid_ordered',
				'order' => '-sort_order,-id',
			]);

			if($last_post)
				return go($last_post->url_in_container());
		}

		// Если гость или не нашли, куда перейти
		$topic = bors_load('balancer_board_topic', $this->id());

		return go($topic->url_ex(1));
	}
}
