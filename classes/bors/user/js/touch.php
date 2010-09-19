<?php

class user_js_touch extends base_page
{
	function template() { return 'empty.html'; }

	function init()
	{
		template_nocache();

		$this->set_loaded();

		$obj = object_load($this->id());
		if(!$obj || !bors()->user())
			return;

		$obj->touch(bors()->user()->id());
	}

	function __wakeup()
	{
		$this->init();
	}

	function empty_body() { return 'true;'; }
}
