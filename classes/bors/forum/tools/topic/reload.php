<?php

class forum_tools_topic_reload extends base_object
{
	function can_be_empty() { return true; }

	function pre_parse($data)
	{
		$topic = object_load('forum_topic', $this->id());
		
		if($posts = $topic->all_posts_ids())
			$this->db('punbb')->query('UPDATE messages SET html=\'\' WHERE id IN (' . join(',', $posts) . ')');
		
		$topic->recalculate();
		return go($topic->url());
	}
}
