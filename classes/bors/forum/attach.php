<?php

class forum_attach extends base_object_db
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
				
	function url() { return "http://balancer.ru/forum/punbb/attachment.php?item=".$this->id(); }

	function thumbnail_link($geometry)
	{
		if(preg_match("!(jpe?g|png|gif)!i", $this->extension()))
			return "<br /><a href=\"{$this->url()}\"><img src=\"http://files.balancer.ru/cache/forums/attaches/".preg_replace("!/([^/]+)$!", "/$geometry/$1", $this->location())."\"></a>";
		return '';
	}

}
