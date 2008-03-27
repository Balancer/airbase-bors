<?
	include_once(BORS_CORE.'/config.php');
	include_once("engines/lcml.php");

	function pun_lcml($text)
	{
		$ch = &new Cache();
		if($ch->get('lcml-compiled', $text))
			return $ch->last();

		global $cur_post;
//		$GLOBALS['main_uri'] = $GLOBALS['cms']['page_path'] = '/forum/post'.@$cur_post['id'];
			
		return $ch->set(lcml($text, 
			array(
				'cr_type' => 'save_cr',
				'forum_type' => 'punbb',
				'forum_base_uri' => 'http://balancer.ru/forum',
				'sharp_not_comment' => true,
				'html_disable' => true,
				'uri' => "post://{$cur_post['id']}/",
			)), 7*86400);
	}
