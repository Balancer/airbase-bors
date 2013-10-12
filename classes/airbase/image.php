<?php

class airbase_image extends bors_image
{
//	function db_name() { return config('airbase.db'); }
//	function table_name() { return 'images'; }
//	function table_fields()
//	{
//		return array(
//		);
//	}

	function xfull_url()
	{
//		if(empty)
	}

	function id_96x96() { return $this->id().',96x96(up,crop)'; }
	function _thumbnail_96x96_def() { return $this->thumbnail('96x96(up,crop)'); }
}
