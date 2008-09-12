<?php

class airbase_forum_attach extends base_object_db
{
	function main_db_storage(){ return 'punbb'; }
	function main_table_storage(){ return 'attach_2_files'; }
	function storage_engine() { return 'storage_db_mysql_smart'; }
	
	function fields()
	{
		return array('punbb'=>array('attach_2_files'=>array(
			'id',
			'post_id',
			'owner_id' => 'owner',
			'title' => 'filename',
			'filename',
			'extension',
			'mime',
			'size',
			'downloads',
			'location',
		)));
	}

	function set_post_id($value, $db_update) { $this->fset('post_id', $value, $db_update); }
	function post_id() { return $this->stb_post_id; }

	function set_owner_id($value, $db_update) { $this->fset('owner_id', $value, $db_update); }
	function owner_id() { return $this->stb_owner_id; }

	function set_title($value, $db_update) { $this->fset('title', $value, $db_update); }
	function title() { return $this->stb_title; }

	function set_filename($value, $db_update) { $this->fset('filename', $value, $db_update); }
	function filename() { return $this->stb_filename; }

	function set_extension($value, $db_update) { $this->fset('extension', $value, $db_update); }
	function extension() { return $this->stb_extension; }

	function set_mime($value, $db_update) { $this->fset('mime', $value, $db_update); }
	function mime() { return $this->stb_mime; }

	function set_size($value, $db_update) { $this->fset('size', $value, $db_update); }
	function size() { return $this->stb_size; }

	function set_downloads($value, $db_update) { $this->fset('downloads', $value, $db_update); }
	function downloads() { return $this->stb_downloads; }

	function set_location($value, $db_update) { $this->fset('location', $value, $db_update); }
	function location() { return $this->stb_location; }

	function url() { return "http://balancer.ru/forum/punbb/attachment.php?item=".$this->id(); }

	function thumbnail_link($geometry)
	{
		if(preg_match("!(jpe?g|png|gif)!i", $this->extension()))
			return "<br /><a href=\"{$this->url()}\"><img src=\"http://files.balancer.ru/cache/forums/attaches/".preg_replace("!/([^/]+)$!", "/$geometry/$1", $this->location())."\"></a>";
		return '';
	}
}
