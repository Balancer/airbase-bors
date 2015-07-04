<?php

class balancer_board_topic_usersGraphPng extends bors_image_png
{
	function show_image()
	{
		$posts = bors_find_all('forum_post', array('topic_id' => $this->id()));

		$users = array();
		$edges = array();
		$max = 0;
		foreach($posts as $p)
		{
			$user_id = $p->owner_id();
			$user = $p->owner();
			if(empty($users[$user_id]))
				$users[$user_id] = array(
					'name' => $user->title(),
					'link' => "http://www.balancer.ru/forum/user-{$user_id}-posts-in-topic-{$this->id()}/",
					'count' => 1,
				);
			else
				$users[$user_id]['count']++;

			if($answer_to = $p->answer_to())
			{
				$from_id = $user_id;
				$to_id = $p->answer_to()->owner_id();

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

		require_once 'Image/GraphViz.php';
		$graph = new Image_GraphViz();
//		$graph->setDirected(false);
		
		foreach($users as $uid => $ud)
			$graph->addNode(
				$uid,
					array(
						'URL'   => $ud['link'],
						'label' => $ud['name'],
//						'shape' => 'box'
//				 		'fontsize' => '14'
					)
			);

		foreach($edges as $from_id => $to_ids)
		{
			foreach($to_ids as $to_id => $x)
			{
				$graph->addEdge(
					array(
						$from_id => $to_id,
					),

					array(
						'label' => $x['count'],
						'arrowhead' => 'none',
						'penwidth' => pow($x['count']/$max, 0.25)*4,
//						'style' => 'dashed',
//						'color' => 'red'
					)
				);
			}
		}
		
		$graph->image('png');
	}	

	function cache_static() { return 600; }
}
