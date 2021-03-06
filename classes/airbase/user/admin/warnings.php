<?php

class airbase_user_admin_warnings extends airbase_user_warnings
{
	function access_engine() { return 'airbase_user_admin_access'; }

	function object() { return ($obj=$this->args('object')) ? bors_load($obj) : NULL; }

	function body_data()
	{
		$object = $this->object();
		if($object)
		{
			$previous_warning = bors_find_first('airbase_user_warning', array(
				'user_id' => $this->id(),
				'moderator_id>' => 0,
				'warn_class_id' => $object->class_id(),
				'warn_object_id' => $object->id(),
			));
		}
		else
			$previous_warning = false;

		$warns_from_me = intval(driver_mysql::factory('AB_FORUMS')->select('warnings', 'SUM(score)', array(
			'user_id' => $this->id(),
			'moderator_id' => bors()->user_id(),
			'`expired_timestamp` > NOW()',
//			'posts.posted>' => time()-86400*14,
//			'inner_join' => array('forum_post ON forum_post.id = airbase_user_warning.warn_object_id', 'topics ON topics.id = posts.topic_id'),
		)));

		return array_merge(parent::body_data(true), array(
			'show_form' => (bors()->user()->is_coordinator() && $warns_from_me < 4) || bors()->user()->is_moderator(),
			'warns_from_me' => $warns_from_me,
			'passive_warnings' => array(),
			'object' => $object,
			'previous_warning' => $previous_warning,
		));
	}

	function cache_static() { return 0; }

	function url_ex($page)
	{
		return '/admin/users/'.$this->id().'/warnings.html'.(($obj=$this->args('object'))?"?object=$obj":'');
	}

	function total_items() { return 0; }

	function pre_show()
	{
		if(!bors()->user())
			return bors_message(ec('А аутентифицироваться кто будет??'));

		if(!$this->args('object'))
			return go(bors_load('airbase_user_warnings', $this->id())->url());
		else
			return false;
	}
}
