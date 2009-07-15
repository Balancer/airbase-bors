<?php

class forum_topic_ubb extends base_object
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

			$this->topic_id = $this->db('forums_airbase_ru')->select('ib_topics', 'tid', array('ubb_topic=' => $fid));
		}
			
		return $this->topic_id;
	}

	function pre_parse()
	{
		if(!$this->topic_id())
			return false;

		$topic = object_load('forum_topic', $this->topic_id());
		return go($topic->url($this->args('page')));
	}
	
	function pre_show() { return true; }
	
	function can_be_empty() { return false; }
	function loaded() { return $this->topic_id(); }
}
