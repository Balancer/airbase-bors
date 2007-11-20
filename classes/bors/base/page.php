<?php

class_include('def_page');

class base_page extends def_page
{
	function render_engine() { return 'render_page'; }
	function can_be_empty() { return true; }
	
	var $stb_source = NULL;
	function set_source($source, $db_update) { $this->set("source", $source, $db_update); }
	function source() { return $this->stb_source; }

	var $stb_cr_type = NULL;
	function set_cr_type($cr_type, $db_update) { $this->set("cr_type", $cr_type, $db_update); }
	function cr_type() { return $this->stb_cr_type ? $this->stb_cr_type : 'save_cr'; }
}
