<?php

class lcml_tag_pair_topic extends bors_lcml_tag_pair
{
	function html($content, &$params = array())
	{
		$topic_id = $content;
		$page = defval($params, 'page');

		$o = @$params['original'];
		if(bors_exec_time() > 10)
			return "<a href=\"$o\">$o</a>";

		$topic = bors_load('balancer_board_topic', $topic_id);

		if(!$topic)
		{
			bors_debug::syslog('forum-data-lost', "Unknown topic $topic_id");
			return "Тема <a href=\"$o\">$o</a> не найдена";
		}

		$topic->set_page($page);


		if(bors_exec_time() > 5)
			return $topic->titled_link();

		$first_post = $topic->first_post();

		if(!$first_post)
		{
			bors_debug::syslog('forum-data-lost', "Unknown first post in topic {$topic} ($topic_id)");
			$description = "";
		}
		else
			$description = $first_post->snip(500);

		if($params['is_alone'])
			$html = bors_lcml_helper::box($topic, array(
				'url' => $topic->url($page),
				'description' => $description,
				'reference' => $topic->forum()->titled_link(),
			));
		else
			$html = $topic->titled_link($page);

		return $html;
	}
}
