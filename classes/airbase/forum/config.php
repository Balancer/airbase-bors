<?php

class airbase_forum_config extends bors_config
{
	function object_data()
	{
		return array_merge(parent::object_data(), array(
			'template' => 'xfile:forum/_header.html',
		));
	}
}
