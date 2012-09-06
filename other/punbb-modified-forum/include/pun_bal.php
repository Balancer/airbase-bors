<?php

require_once('bors_config.php');
require_once('engines/lcml/main.php');

	function pun_lcml($text, $can_be_cached = true)
	{
		$ch = new Cache();
		if($ch->get('lcml-compiled', $text) && $can_be_cached)
			return $ch->last();

		global $cur_post;
//		$GLOBALS['main_uri'] = $GLOBALS['cms']['page_path'] = '/forum/post'.@$cur_post['id'];

//		if(config('is_developer')) var_dump($cur_post);
		return $ch->set(lcml($text, 
			array(
				'cr_type' => 'save_cr',
				'forum_type' => 'punbb',
				'forum_base_uri' => 'http://www.balancer.ru/forum',
				'sharp_not_comment' => true,
				'html_disable' => true,
				'uri' => "post://{$cur_post['id']}/",
			)), 7*86400);
	}
