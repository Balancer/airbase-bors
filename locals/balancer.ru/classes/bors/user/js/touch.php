<?php

class user_js_touch extends base_page
{
	function template() { return 'empty.html'; }
	
	function init()
	{
		header("Content-type", "text/javascript");
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

		$this->set_loaded();
	
		$obj = object_load($this->id());
		if(!$obj || !bors()->user())
			return;
		
		$obj->touch(bors()->user()->id());
		
		echo "true;";
	}

	function __wakeup()
	{
		$this->init();
	}

	function empty_body() { return ''; }
}
