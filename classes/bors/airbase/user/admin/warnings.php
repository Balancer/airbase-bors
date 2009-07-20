<?php

class airbase_user_admin_warnings extends airbase_user_warnings
{
	function object() { return ($obj=$this->args('object')) ? object_load($obj) : NULL; }

	function local_data()
	{
		$warns_from_me = intval($this->db('punbb')->select('warnings', 'SUM(score)', array(
			'user_id' => $this->id(),
			'moderator_id' => bors()->user_id(),
			'time>' => time()-86400*WARNING_DAYS, 
//			'posts.posted>' => time()-86400*14,
//			'inner_join' => array('forum_post ON forum_post.id = airbase_user_warning.warn_object_id', 'topics ON topics.id = posts.topic_id'),
		)));
	
		return array_merge(parent::local_data(true), array(
			'show_form' => $warns_from_me < 4,
			'warns_from_me' => $warns_from_me,
			'passive_warnings' => array(),
			'object' => $this->object(),
		));
	}
	
	function cache_static() { return 0; }
	
	function url() { return '/admin/users/'.$this->id().'/warnings.html'.(($obj=$this->args('object'))?"?object=$obj":''); }

	function total_items() { return 0; }

	function pre_show()
	{
		if(!$this->args('object'))
			return go(object_load('airbase_user_warnings', $this->id())->url());
		else
			return false;
	}
}
