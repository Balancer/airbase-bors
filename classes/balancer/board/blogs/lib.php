<?php

class balancer_board_blogs_lib
{
	static function load_keywords(&$blogs)
	{
		$pids = array_unique(bors_field_array_extract($blogs, 'id'));
		$posts  = bors_find_all('balancer_board_post',  array('id IN' => $pids, 'order' => '-create_time', 'by_id' => true));
		$tids = array_unique(bors_field_array_extract($posts, 'topic_id'));
		$topics = bors_find_all('balancer_board_topic', array('id IN' => $tids, 'by_id' => true));

		foreach($posts as $p)
		{
			$pid = $p->id();
			if($blog = @$blogs[$pid])
			{
				if($kws = $blog->keywords())
					$p->set_keyword_links(balancer_blogs_tag::linkify($kws, '', ' | ', true), false);

				continue;
			}

			if($topic = @$topics[$p->topic_id()])
			{
				if($kws = $topic->keywords())
					$p->set_keyword_links(balancer_blogs_tag::linkify($kws, '', ' | ', true), false);

				continue;
			}
		}

		$blogs = $posts;
	}
}
