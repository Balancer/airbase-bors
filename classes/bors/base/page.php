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
	function pages_list()
	{
		include_once("funcs/design/page_split.php");
//		set_loglevel(9);
//		echo "total=".$this->total_pages();
//		set_loglevel(2);
		$pages = join("", pages_show($this, $this->total_pages(), $this->items_around_page()));
		if($pages)
			return ec('<div class="pages_select">Страницы: ').$pages.'</div>';
		else
			return '';
	}
}
