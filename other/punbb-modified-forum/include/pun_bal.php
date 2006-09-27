<?
	include_once($_SERVER['DOCUMENT_ROOT']."/cms/config.php");
	include_once("funcs/lcml.php");

	function pun_lcml($text)
	{
		$ch = new Cache();
		$type = "lcml-compiled";
		$key = md5($text);
		if($val = $ch->get($type, $key))
			return $val;

		global $cur_post;
		$GLOBALS['main_uri'] = $GLOBALS['cms']['page_path'] = '/forum/post'.@$cur_post['id'];
			
		return $ch->set($type, $key, lcml($text, 
			array(
				'cr_type' => 'save_cr',
				'forum_type' => 'punbb',
				'forum_base_uri' => 'http://balancer.ru/forum',
				'sharp_not_comment' => true,
				'html' => false,
				'uri' => "post://{$cur_post['id']}/",
			)));
	}
