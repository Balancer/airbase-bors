<?php

class airbase_forum_attach extends base_object_db
{
	function main_db(){ return 'punbb'; }
	function main_table(){ return 'attach_2_files'; }
	function storage_engine() { return 'storage_db_mysql_smart'; }
	
	function main_table_fields()
	{
		return array(
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
		);
	}

function post_id() { return @$this->data['post_id']; }
function set_post_id($v, $dbup) { return $this->set('post_id', $v, $dbup); }
function owner_id() { return @$this->data['owner_id']; }
function set_owner_id($v, $dbup) { return $this->set('owner_id', $v, $dbup); }
function filename() { return @$this->data['filename']; }
function set_filename($v, $dbup) { return $this->set('filename', $v, $dbup); }
function extension() { return @$this->data['extension']; }
function set_extension($v, $dbup) { return $this->set('extension', $v, $dbup); }
function mime() { return @$this->data['mime']; }
function set_mime($v, $dbup) { return $this->set('mime', $v, $dbup); }
function size() { return @$this->data['size']; }
function set_size($v, $dbup) { return $this->set('size', $v, $dbup); }
function downloads() { return @$this->data['downloads']; }
function set_downloads($v, $dbup) { return $this->set('downloads', $v, $dbup); }
function location() { return @$this->data['location']; }
function set_location($v, $dbup) { return $this->set('location', $v, $dbup); }

	function url() { return "http://balancer.ru/forum/punbb/attachment.php?item=".$this->id(); }

	function thumbnail_link($geometry)
	{
		if(preg_match("!(jpe?g|png|gif)!i", $this->extension()))
			return "<br /><a href=\"{$this->url()}\"><img src=\"http://files.balancer.ru/cache/forums/attaches/".preg_replace("!/([^/]+)$!", "/$geometry/$1", $this->location())."\" alt=\"\" /></a>";
		return '';
	}
}
