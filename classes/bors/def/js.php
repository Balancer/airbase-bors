<?php

class def_js extends base_page_db
{
	function preShowProcess()
	{
		include_once("funcs/js.php");
		header("Content-type", "text/javascript");
		return str2js($this->cacheable_body());
	}

	function storage_engine() { return ''; }
}
