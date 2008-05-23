<?php

class airbase_user_admin_warnings extends airbase_user_warnings
{
	function data_providers()
	{
		return array_merge(parent::data_providers(true), array(
			'show_form' => true,
			'passive_warnings' => array(),
			'object' => object_load($this->args('object')),
		));
	}
	
	function cache_static() { return 0; }
	
	function url() { return '/admin/users/'.$this->id().'/warnings.html'; }

	function total_items() { return 0; }

	function pre_show()
	{
		if(!$this->args('object'))
			return go(object_load('airbase_user_warnings', $this->id())->url());
		else
			return false;
	}
}
