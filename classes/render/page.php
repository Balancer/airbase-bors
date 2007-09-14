<?php

class_load('def_empty');

class render_page extends def_empty
{
	function render($object)
	{
	    require_once('funcs/templates/bors.php');
		return template_assign_bors_object($object);
	}
}
