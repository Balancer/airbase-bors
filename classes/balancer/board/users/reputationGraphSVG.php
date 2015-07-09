<?php

define('REPUTATION_GRAPH_LIMIT', 500);

class balancer_board_users_reputationGraphSVG extends bors_image_svg
{
	private $edges_count = 0;

	function pre_parse()
	{
		if(bors()->client()->is_bot())
		{
			debug_hidden_log('002', 'bot trapped!');
			return go('http://www.balancer.ru/forum/');
		}

		return false;
	}

	function image()
	{
		$reps = bors_find_all('airbase_user_reputation', array('is_deleted' => false, 'order' => '-create_time', 'limit' => REPUTATION_GRAPH_LIMIT));

		$users = array();
		$edges = array();

		$max = 0;
		$maxu = 0;

		foreach($reps as $r)
		{
			$voter = $r->owner();
			$voter_id = $voter->id();
			$target = $r->target_user();
			if(!$target)
				continue;
			$target_id = $target->id();
			if(empty($users[$voter_id]))
				$users[$voter_id] = array(
					'name' => $voter->title(),
					'link' => $voter->url(),
					'count' => 1,
				);
			else
			{
				$cnt = ++$users[$voter_id]['count'];
				if($cnt > $maxu)
					$maxu = $cnt;
			}

			if(empty($users[$target_id]))
				$users[$target_id] = array(
					'name' => $target->title(),
					'link' => $target->url(),
					'count' => 1,
				);
			else
			{
				$cnt = ++$users[$target_id]['count'];
				if($cnt > $maxu)
					$maxu = $cnt;
			}

			@$edges[$voter_id][$target_id]['score'.$r->score()]++;
			@$edges[$voter_id][$target_id]['count']++;
			$cnt = $edges[$voter_id][$target_id]['count'];
			if(abs($cnt) > $max)
				$max = abs($cnt);
		}

		$this->edges_count = count($edges);

		$title = 'Граф последних '.REPUTATION_GRAPH_LIMIT.' репутаций (с '.date('d.m.Y', $reps[count($reps)-1]->create_time()).' по '.date('d.m.Y').')';

		require_once 'Image/GraphViz.php';
		$graph = new Image_GraphViz(true, array(), 'Репутации форумов Balancer.Ru', true, true);
		$graph->setAttributes(array(
			'label' => $title,
			'labelloc' => 't',
//			'splines' => true,
			'URL' => 'http://www.balancer.ru/users/toprep/',
		));

		foreach($users as $uid => $ud)
			$graph->addNode(
				$uid,
					array(
						'URL'   => $ud['link'],
						'label' => $ud['name'],
					)
			);

		foreach($edges as $from_id => $to_ids)
		{
			foreach($to_ids as $to_id => $x)
			{
				$score = @$x['score1'] - @$x['score-1'];

				$tooltip = array();
				if(!empty($x['score1']))
					$tooltip[] = '+'.$x['score1'];
				if(!empty($x['score-1']))
					$tooltip[] = '-'.$x['score-1'];

				$tooltip = join('/', $tooltip);
				if($tooltip == '+1' || $tooltip == '-1')
					$tooltip = ' ';

				$graph->addEdge(
					array(
						$from_id => $to_id,
					),

					array(
						'label' => $tooltip,
						'penwidth' => pow(abs($x['count'])/$max, 0.5)*3+1,
						'weight' => @$x['score1'] + @$x['score-1'],
						'color' => $score > 0 ? '#00ff00' : ($score < 0 ? '#ff0000' : 'black'),
					)
				);
			}
		}

		$svg = $graph->fetch('svg');

		if(!$svg)
			return bors_message("Unknown error: empty result");

		if(PEAR::isError($svg))
			return $svg->getMessage();

		return $svg; // str_replace('<title>G</title>', '<title>BalancerRu</title>', $svg);
	}

	function cache_static() { return rand(3600, 7200); }
}
