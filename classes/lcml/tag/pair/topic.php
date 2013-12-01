<?php

class lcml_tag_pair_topic extends bors_lcml_tag_pair
{
	function html($content, &$params = array())
	{
		$topic_id = $content;
		$page = defval($params, 'page');

		$topic = bors_load('balancer_board_topic', $topic_id);
		$topic->set_page($page);
		$first_post = $topic->first_post();
		if($params['is_alone'])
			$html = bors_lcml_helper::box($topic, array(
				'url' => $topic->url($page),
				'description' => $first_post->snip(500),
				'reference' => $topic->forum()->titled_link(),
			));
		else
			$html = $topic->titled_link($page);

		return $html;
	}
}
