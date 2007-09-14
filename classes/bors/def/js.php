<?
class_load("def_dbpage");

class def_js extends def_dbpage
{
	function preShowProcess()
	{
		include_once("funcs/js.php");
		header("Content-type", "text/javascript");
		return str2js($this->cacheable_body());
	}

	function storage_engine() { return ''; }
}
