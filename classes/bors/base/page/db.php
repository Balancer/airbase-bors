<?php

class base_page_db extends def_dbpage
{
//	function main_db_storage() { return $GLOBALS['cms']['mysql_database']; }
	function fields_first() { return NULL; }

	function select($field, $where_map) { return $this->db->select($this->main_table_storage(), $field, $where_map); }
	function select_array($field, $where_map) { return $this->db->select_array($this->main_table_storage(), $field, $where_map); }

	function storage_engine() { return 'storage_db_mysql'; }

	function pages_list($css='pages_select')
	{
		include_once("funcs/design/page_split.php");
		$pages = '<li>'.join('</li><li>', pages_show($this, $this->total_pages(), $this->items_around_page())).'</li>';

		if($this->total_pages() > 1)
			return '<div class="'.$css.ec('"><ul><li>Страницы:</li>').$pages.'</ul></div>';
		else
			return '';
	}

	function items_around_page() { return 10; }
}
