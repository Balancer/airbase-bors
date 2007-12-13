<?php

class base_null
{
	function can_be_empty() { return true; }
	function class_name() { return get_class($this); }
	function init() { }
}
