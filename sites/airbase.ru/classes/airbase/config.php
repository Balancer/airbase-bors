<?php

class airbase_config extends bors_config
{
	function view_data()
	{
		return array_merge(parent::view_data(), array(
			'template' => 'xfile:airbase/default/index2.html',
		));
	}

	function model_data()
	{
		return array_merge(parent::model_data(), array(
			'db_name' => 'AB_RESOURCES',
			'view_class' => 'airbase_view',
		));
	}
}
