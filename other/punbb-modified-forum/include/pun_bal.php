<?php

require_once('bors_config.php');
require_once('engines/lcml/main.php');

	function pun_lcml($text, $can_be_cached = true, $post = NULL)
	{
		$ch = new bors_cache();
		if($ch->get('lcml-compiled', $text) && $can_be_cached)
			return $ch->last();

		global $cur_post;

		return $ch->set(lcml($text,
			array(
				'cr_type' => 'save_cr',
				'forum_type' => 'punbb',
				'forum_base_uri' => 'http://www.balancer.ru/forum',
				'sharp_not_comment' => true,
				'html_disable' => true,
				'uri' => "post://{$cur_post['id']}/",
				'self' => $post,
				'container' => $post ? $post->topic() : NULL,
			)), 7*86400);
	}
