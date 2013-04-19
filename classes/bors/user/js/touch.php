<?php

class user_js_touch extends bors_js
{
	function pre_show()
	{
		template_nocache();

		parent::pre_show();

		$this->set_is_loaded(true);

		$time = bors()->request()->data('time');
		$obj  = bors()->request()->data('obj');

		if($obj)
			$obj = bors_load_uri($obj);
		else
			$obj = object_load($this->id());

		if(!$time)
			$time = time();

		if(!$obj || !bors()->user())
			return 'true;';

		$obj->touch(bors()->user()->id(), $time);
		return 'true;';
	}
}
