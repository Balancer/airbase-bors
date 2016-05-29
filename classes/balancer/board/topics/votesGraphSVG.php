<?php

define('REPUTATION_GRAPH_LIMIT', 200);

class balancer_board_topics_votesGraphSVG extends bors_image_svg
{
	private $edges_count = 0;

	function pre_parse()
	{
		if(bors()->client()->is_bot())
		{
			bors_debug::syslog('002', 'bot trapped!');
			return go('http://www.balancer.ru/forum/');
		}

		return false;
	}

	function image()
	{
//		bors_debug::syslog('003', 'test user');

		$topic_id = $this->id();
		$topic = bors_load('balancer_board_topic', $topic_id);

		$pids = array();
		foreach(bors_each('balancer_board_posts_pure', array('topic_id' => $topic_id)) as $p)
			$pids[] = $p->id();

		$votes = bors_find_all('bors_votes_thumb', array(
			'target_object_id IN' => $pids,
			'create_time>=' => $topic->create_time(),
			'create_time<' => time() - 86400,
			'order' => '-create_time',
			'limit' => REPUTATION_GRAPH_LIMIT,
		));

//echo '<xmp>'; var_dump($votes); exit();

		$users = array();
		$edges = array();

		$max = 0;
		$maxu = 0;

		foreach($votes as $r)
		{
			$voter = $r->owner();
			$voter_id = $voter ? $voter->id() : NULL;
			$target = $r->target_user();
			if(!$target)
				continue;

			$target_id = $target->id();
			if(empty($users[$voter_id]))
				$users[$voter_id] = array(
					'name' => $voter ? $voter->title() : 'Unknown user '.$r->user_id()." (vote_id={$r->id()})",
					'link' => $voter ? $voter->url() : NULL,
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

		$title = 'Граф последних '.REPUTATION_GRAPH_LIMIT.' оценок<br/>(с '
			.date('d.m.Y H:i', $start).' по '.date('d.m.Y H:i', time()-86400).')<br/>'
			.'темы «'.$topic->title().'»';

		if(!count($votes))
			$title .= "<br/>Оценок за указанный период не найдено";

		require_once 'Image/GraphViz.php';
		$graph = new Image_GraphViz(true, array(), 'Оценки темы «'.$topic->title().'»', true, true);
		$graph->setAttributes(array(
			'label' => $title,
			'labelloc' => 't',
			'rankdir' => 'LR',
// http://www.graphviz.org/doc/info/attrs.html#d:mode
//			'mode' => 'KK',
//			'splines' => 'spline',
//			'fontsize' => 12,
//			'size' => '30,30',
//			'ratio' => '1.3',
			'URL' => $topic->url(),
		));

		foreach($users as $uid => $ud)
			$graph->addNode(
				$uid,
					array(
						'URL'   => $ud['link'],
						'label' => $ud['name'],
						'fontname' => 'Tahoma',
						'fontsize' => 8,
					)
			);

		foreach($edges as $from_id => $to_ids)
		{
			foreach($to_ids as $to_id => $x)
			{
//				$score = @$x['score1'] - @$x['score-1'];

				$xc = rand(70,256);
				$c1 = sprintf('%02X', $xc);
				$c2 = sprintf('%02X', rand(0, $xc/2));
				$c3 = sprintf('%02X', rand(0, $xc/2));

				if(@$x['score1'])
				{
					$graph->addEdge(
						array(
							$from_id => $to_id,
						),

						array(
							'penwidth' => (max(1,abs($x['count']))-1)/$max*12+1,
//							'label' => pow(@$x['score1'] + @$x['score-1'], 2),
							'weight' => pow(@$x['score1'], 2)+1,
//							'weight' => $x['count']*$x['count']+1,
							'color' => /*$score > 0 ? */ "#{$c2}{$c1}{$c3}" /* : ($score < 0 ? "#{$c1}{$c2}{$c3}" : 'black')*/,
						)
					);
				}

				if(@$x['score-1'])
				{
					$graph->addEdge(
						array(
							$from_id => $to_id,
						),

						array(
							'penwidth' => (max(1,abs($x['count']))-1)/$max*12+1,
//							'label' => pow(@$x['score1'] + @$x['score-1'], 2),
							'weight' => pow(@$x['score-1'], 2)+1,
//							'weight' => $x['count']*$x['count']+1,
							'color' => /*$score > 0 ? "#{$c2}{$c1}{$c3}" : ($score < 0 ? */ "#{$c1}{$c2}{$c3}" /*: 'black')*/,
						)
					);
				}
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
