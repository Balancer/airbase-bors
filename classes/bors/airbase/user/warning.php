<?php

class airbase_user_warning extends base_object_db
{
	function main_db_storage() { return 'punbb'; }
	function main_table_storage() { return 'warnings'; }

	function fields()
	{
		return array('punbb' => array('warnings' => array(
			'id',
			'user_id',
			'create_time' => 'time',
			'score',
			'type_id',
			'moderator_id',
			'moderator_name',
			'referer' => 'uri',
			'source' => 'comment',
			'warn_class_id',
			'warn_object_id',
		)));
	}

	function moderator() { return object_load('forum_user', $this->moderator_id()); }
	function user() { return object_load('forum_user', $this->user_id()); }

	function referer_titled_url()
	{
		if($this->warn_class_id() > 0)
			return object_load($this->warn_class_id(), $this->warn_object_id())->titled_url();
			
		if($this->warn_class_id() == 0)
		{
			if($obj = object_load($this->referer()))
			{
				$this->set_warn_class_id($obj->class_id(), true);
				$this->set_warn_object_id($obj->id(), true);
				return $obj->titled_url();
			}

			$this->set_warn_class_id(-1, true);
		}
		
		return "<a href=\"{$this->referer()}\">{$this->referer()}</a>";
	}
}
