<?php

class forum_tools_topic_reload extends base_object
{
	function can_be_empty() { return true; }

	function pre_parse($data)
	{
		$topic = object_load('forum_topic', $this->id());
		
		$this->db('punbb')->query('UPDATE messages SET html=\'\' WHERE id IN (' . join(',', $topic->all_posts_ids()) . ')');
		
		$topic->recalculate();
		return go($topic->url());
	}
}
