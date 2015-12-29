<?php

class forum_topic_ubb extends bors_object
{
	private $topic_id = false;
	function topic_id()
	{
		if($this->topic_id === false)
		{
			if(preg_match('!^f=(\d+)&t=(\d+)$!', $this->id(), $m))
				$fid = intval($m[1]).'-'.intval($m[2]);
			elseif(preg_match('!(\d+)/HTML/(\d+)$!', $this->id(), $m))
				$fid = intval($m[1]).'-'.intval($m[2]);
			else
				$fid = str_replace('/', '-', $this->id());

			list($forum_id, $topic_id) = explode('-', $fid);

			$this->topic_id = driver_mysql::factory('AB_FORUMS')->select('z_ubb_topics_map', 'new_topic_id', array('ubb_topic_id' => $topic_id, 'ubb_forum_id' => $forum_id));
		}

		return $this->topic_id;
	}

	function pre_parse()
	{
		if(!$this->topic_id())
			return false;

		$topic = bors_load('balancer_board_topic', $this->topic_id());
		return go($topic->url_ex($this->args('page')));
	}

	function topic() { return bors_load('balancer_board_topic', $this->topic_id()); }

	function pre_show() { return true; }

	function can_be_empty() { return false; }
	function is_loaded() { return $this->topic_id() > 0; }

	function title() { return $this->topic()->title(); }
	function url($page = NULL) { return $this->topic()->url_ex($page>0 ? $page : $this->args('page')); }
}
