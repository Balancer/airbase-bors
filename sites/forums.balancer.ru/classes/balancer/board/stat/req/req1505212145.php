<?php

class balancer_board_stat_req_req1505212145 extends bors_json
{
	var $auto_map = true;
	function cache_static() { return 86400; }

	function data()
	{
		$result = [];
		for($year=1999; $year<=date('Y'); $year++)
		{
			$posts = bors_find_all('balancer_board_post', [
				'create_time BETWEEN' => [strtotime("$year-01-01 00:00:00"), strtotime("$year-12-31 23:59:59")],
				'inner_join' => [
					'balancer_board_topic ON topic_id = balancer_board_topic.id',
					'balancer_board_forum ON forum_id = balancer_board_forum.id',
				],
				'cat_id IN' => [6,26,27],
				'MOD(`posts`.`id`, 100)=' => 0,
				'is_deleted' => false,
				'balancer_board_topic.is_public=' => true,
				'order' => '-create_time',
				'limit' => 15,
			]);

			$row = [];

			foreach($posts as $p)
			{
				$post_data = [
					'id' => $p->id(),
					'topic_id' => $p->topic_id(),
					'topic' => $p->topic()->title(),
					'forum_id' => $p->topic()->forum()->id(),
					'forum' => $p->topic()->forum()->title(),
					'created' => date('d.m.Y H:i:s', $p->create_time()),
					'user_id' => $p->owner_id(),
					'user' => $p->author_name(),
					'score_positive' => $p->score_positive(),
					'score_negative' => $p->score_negative(),
					'message' => $p->source(),
				];

				if($w = $p->warning())
					$post_data['warning'] = [
						'type_id' => $w->type_id(),
						'type' => $w->type()->title(),
						'message' => $w->source(),
					];

				$row[$p->id()] = $post_data;
			}

			$result[$year] = $row;
		}

		return $result;
	}
}