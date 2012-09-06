<?php

class balancer_board_topic_usersGraphSVG extends base_image_svg
{
	private $edges_count = 0;

	function pre_parse()
	{
		if(bors()->client()->is_bot())
		{
			debug_hidden_log('002', 'bot trapped!');
			return go(object_load('forum_topic', $this->id())->url());
		}

		return false;
	}

	function image()
	{
		debug_hidden_log('000', 'check for balancer_board_topic_usersGraphSVG');

		$posts = objects_array('forum_post', array('topic_id' => $this->id()));
		$topic = object_load('forum_topic', $this->id());

		$users = array();
		$edges = array();
		$max = 0;
		$maxu = 0;
		foreach($posts as $p)
		{
			$user_id = $p->owner_id();
			$user = $p->owner();
			if(empty($users[$user_id]))
				$users[$user_id] = array(
					'name' => $p->author_name(),
//					'reputation' => $user->reputation(), !! сделать учёт на !$user
					'link' => "http://www.balancer.ru/forum/user-{$user_id}-posts-in-topic-{$this->id()}/",
					'count' => 1,
				);
			else
			{
				$cnt = ++$users[$user_id]['count'];
				if($cnt > $maxu)
					$maxu = $cnt;
			}

			if($answer_to = $p->answer_to())
			{
				$from_id = $user_id;
				$to_id = $p->answer_to()->owner_id();
				
				if(!$this->args('ordered'))
					if($from_id < $to_id)
						list($to_id, $from_id) = array($from_id, $to_id);
				
				if(empty($edges[$from_id][$to_id]))
					$edges[$from_id][$to_id] = array(
						'count' => 1,
					);
				else
				{
					$cnt = ++$edges[$from_id][$to_id]['count'];
					if($cnt > $max)
						$max = $cnt;
				}
			}
		}

		$this->edges_count = count($edges);
		debug_hidden_log('001', "Total edges: {$this->edges_count}", false);

		$title = 'Граф взаимных ответов участников темы «'.$topic->title().'»';

		require_once 'Image/GraphViz.php';
		$graph = new Image_GraphViz();
		$graph->setAttributes(array(
			'label' => $title,
			'labelloc' => 't',
//			'spline' => true,
			'URL' => $topic->url(),
		));
		
		foreach($users as $uid => $ud)
			$graph->addNode(
				$uid,
					array(
						'URL'   => $ud['link'],
						'label' => $ud['name'],
//						'tooltip' => $ud['reputation'],
//						'shape' => 'box',
//				 		'fontsize' => 8+intval(12*$ud['count']/$maxu),
//						'fillcolor' => $ud['reputation'] >= 0 ? 
					)
			);

		foreach($edges as $from_id => $to_ids)
		{
			foreach($to_ids as $to_id => $x)
			{
				if(empty($users[$to_id]))
				{
					$user = object_load('balancer_board_user', $to_id);
					$graph->addNode(
						$to_id,
						array(
							'URL'   => "http://www.balancer.ru/forum/user-{$to_id}-posts-in-topic-{$this->id()}/",
							'label' => $user ? $user->title() : $to_id,
						)
					);
				}

				if(empty($users[$from_id]))
				{
					$user = object_load('balancer_board_user', $from_id);
					$graph->addNode(
						$from_id,
						array(
							'URL'   => "http://www.balancer.ru/forum/user-{$from_id}-posts-in-topic-{$this->id()}/",
							'label' => $user ? $user->title() : $from_id,
						)
					);
				}
				
				$graph->addEdge(
					array(
						$from_id => $to_id,
					),

					array(
						'label' => $x['count'] > 1 ? $x['count'] : ' ',
						'arrowhead' => $this->args('ordered') ? 'normal' : 'none',
						'penwidth' => pow($x['count']/$max, 0.25)*4,
//						'style' => 'dashed',
						'color' => sprintf('#%2x%2x%2x', rand(0,128), rand(0,128), rand(0,128)),
					)
				);
			}
		}
		
		ob_start();
		$graph->image('svg');
		$svg = ob_get_contents();
		ob_end_clean();
		
		return $svg; // str_replace('<title>G</title>', '<title>BalancerRu</title>', $svg);
	}	

	function cache_static()
	{
		$base = max($this->edges_count, 5);
		$ttl = rand(100*$base, 200*$base);
		debug_hidden_log('001', "TTL for {$this->edges_count} = $ttl", false);
		return $ttl;
	}
}
