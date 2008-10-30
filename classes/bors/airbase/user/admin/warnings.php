<?php

class airbase_user_admin_warnings extends airbase_user_warnings
{
	function object() { return ($obj=$this->args('object')) ? object_load($obj) : NULL; }

	function data_providers()
	{
		return array_merge(parent::data_providers(true), array(
			'show_form' => true,
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
