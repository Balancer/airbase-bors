<?
	require_once("BorsBaseDbPage.php");

	class BorsPageJS extends BorsBaseDbPage
	{
		function preShowProcess()
		{
			include_once("inc/js.php");
			header("Content-type", "text/javascript");
			return str2js($this->cacheable_body());
		}
	}
