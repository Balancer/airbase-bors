<?php

class balancer_board_posts_lib
{
	static function load_keywords(&$posts, $topics = NULL, $blogs = NULL)
	{
		if(!$blogs)
		{
			$pids = array_unique(bors_field_array_extract($posts, 'id'));
			$blogs  = bors_find_all('balancer_board_blog',  array('id IN' => $pids, 'by_id' => true));
		}

		if(!$topics)
		{
			$tids = array_unique(bors_field_array_extract($posts, 'topic_id'));
			$topics = bors_find_all('balancer_board_topic', array('id IN' => $tids, 'by_id' => true));
		}

		foreach($posts as $id => $p)
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
	}
}
