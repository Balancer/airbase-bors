<?php

class base_page extends def_page
{
	function render_engine() { return 'render_page'; }
	function can_be_empty() { return true; }
	
	var $stb_source = NULL;
	function set_source($source, $db_update) { $this->set("source", $source, $db_update); }
	function source() { return $this->stb_source; }

	function items_around_page() { return 10; }

	function pages_links($css='pages_select')
	{
		include_once("funcs/design/page_split.php");
		$pages = '<li>'.join('</li><li>', pages_show($this, $this->total_pages(), $this->items_around_page())).'</li>';

		if($this->total_pages() > 1)
			return '<div class="'.$css.ec('"><ul><li>Страницы:</li>').$pages.'</ul></div>';
		else
			return '';
	}

	function getsort($t, $def = false)
	{
		$sort = @$_GET['s'];
		if(!$sort)
			$sort = $t;
		
		$r = intval(@$_GET['r']);
		if($t == $sort)
			$r = ($r ? 0 : 1);
		else
			$r = 0;
			
		return "s={$t}" . ($r ? '&r=1' : '');
	}

	function total_pages() { return  intval(($this->total_items() - 1)/$this->items_per_page()) + 1; }

	function items_per_page() { return 25; }
}
