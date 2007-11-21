<?php

class def_empty
{
	var $id;
	var $initial_id = NULL;
	function id() { return $this->id; }
	function set_id($id) { $this->id = $id; }
	
	function __construct($id, $page)
	{
		$this->id = $this->initial_id = $id;
		$this->page	= $page;
	}

	var $page = '';
	function page() { return $this->page; }
	function set_page($page) { $this->page = $page; }

	function storage_engine() { return ''; }
	function body_engine() { return ''; }
	function loaded() { return true; }
	function internal_uri() { return get_class($this).'://'.$this->id(); }
	function cache_clean() { }

	function auto_search_index() { return false; }
}
