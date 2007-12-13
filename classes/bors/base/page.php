<?php

class_include('def_page');

class base_page extends def_page
{
	function render_engine() { return 'render_page'; }
	function can_be_empty() { return true; }
	
	var $stb_source = NULL;
	function set_source($source, $db_update) { $this->set("source", $source, $db_update); }
	function source() { return $this->stb_source; }

	function items_around_page() { return 10; }

	function pages_list($css='pages_select')
	{
		include_once("funcs/design/page_split.php");
		$pages = '<li>'.join('</li><li>', pages_show($this, $this->total_pages(), $this->items_around_page())).'</li>';

		if($this->total_pages() > 1)
			return '<div class="'.$css.ec('"><ul><li>Страницы:</li>').$pages.'</ul></div>';
		else
			return '';
	}
}
