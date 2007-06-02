<?
	require_once("BorsBasePage.php");

	class BorsPageJS extends BorsBasePage
	{
		function BorsPageJS($id)
		{
			parent::BorsBasePage($id);
		}

		function preShowProcess()
		{
			include_once("funcs/js.php");
			header("Content-type", "text/javascript");
			echo str2js($this->cacheable_body());
			return true;
		}
	}
