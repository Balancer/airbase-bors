<?php

class lcml_tag_pair_topic_inline extends bors_lcml_tag_pair
{
	function html($content, &$params = array())
	{
		$topic_id = $content;
		$topic = bors_load('balancer_board_topic', $topic_id);
		$html = $topic->titled_link();

		return $html;
	}
}
