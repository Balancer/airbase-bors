<?php

define('REPUTATION_GRAPH_LIMIT', 100);

class balancer_board_users_votes_lastgraph extends bors_image_svg
{
	private $edges_count = 0;

	function pre_parse()
	{
		if(bors()->client()->is_bot())
			return go('http://forums.balancer.ru/');

		return false;
	}

	function image()
	{
		$user_id = intval($this->id());
		$user = bors_load('balancer_board_user', $user_id);

		$votes = bors_find_all('balancer_board_vote', array(
			"(user_id = $user_id OR target_user_id = $user_id)",
			'create_time<' => time() - 86400,
			'order' => '-create_time',
			'limit' => REPUTATION_GRAPH_LIMIT,
		));

// echo '<xmp>'; var_dump($votes); exit();

		$users = array();
		$edges = array();

		$max = 0;
		$maxu = 0;

		foreach($votes as $r)
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

		if(count($votes))
			$start = $votes[count($votes)-1]->create_time();
		else
			$start = $topic->create_time();

		$title = 'Граф последних '.REPUTATION_GRAPH_LIMIT.' оценок, связанных с пользователем '.$user->title();

		if(!count($votes))
			$title .= "<br/>Оценок за указанный период не найдено";

		require_once 'Image/GraphViz.php';
		$graph = new Image_GraphViz(true, array(), 'Оценки пользователя '.$user->title(), true, true);
		$graph->setAttributes(array(
			'label' => $title,
			'labelloc' => 't',
//			'rankdir' => 'LR',
// http://www.graphviz.org/doc/info/attrs.html#d:mode
//			'mode' => 'KK',
//			'splines' => 'spline',
//			'fontsize' => 20,
//			'size' => '20,20',
//			'ratio' => '0.1',
			'URL' => $user->url(),
		));

		foreach($users as $uid => $ud)
			$graph->addNode(
				$uid,
					array(
						'URL'   => $ud['link'],
//						'URL' => "http://forums.balancer.ru/users/{$uid}/votes/lastgraph.svg",
						'label' => $ud['name'],
						'fontname' => 'Tahoma',
						'fontsize' => 8,
					)
			);

		foreach($edges as $from_id => $to_ids)
		{
			foreach($to_ids as $to_id => $x)
			{
				$score = @$x['score1'] - @$x['score-1'];

				$xc = rand(70,256);
				$c1 = sprintf('%02X', $xc);
				$c2 = sprintf('%02X', rand(0, $xc/2));
				$c3 = sprintf('%02X', rand(0, $xc/2));

				$graph->addEdge(
					array(
						$from_id => $to_id,
					),

					array(
						'penwidth' => (max(1,abs($x['count']))-1)/$max*12+1,
//						'label' => pow(@$x['score1'] + @$x['score-1'], 2),
						'weight' => pow(@$x['score1'] + @$x['score-1'], 2)+1,
//						'weight' => $x['count']*$x['count']+1,
						'color' => $score > 0
							? "#{$c2}{$c1}{$c3}"
							: ($score < 0
								? "#{$c1}{$c2}{$c3}"
								: 'black'),
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
