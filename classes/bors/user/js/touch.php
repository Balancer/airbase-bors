<?php

class user_js_touch extends bors_js
{
	function pre_show()
	{
		template_nocache();

		parent::pre_show();

		$this->set_is_loaded(true);

		$obj = object_load($this->id());
		if(!$obj || !bors()->user())
			return 'true;';

		$obj->touch(bors()->user()->id());
		return 'true;';
	}
}
