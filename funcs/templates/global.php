<?
	function set_global_template_var($name, $value)
	{
		$GLOBALS['cms']['templates']['data'][$name] = $value;
	}
