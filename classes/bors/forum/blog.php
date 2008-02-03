<?php

class forum_blog extends base_page_db
{
	function storage_engine() { return 'storage_db_mysql_smart'; }
	function can_be_empty() { return false; }
	
	function main_db_storage() { return 'punbb'; }
	function main_table_storage() { return 'blog'; }

	function main_table_fields()
	{
		return array('id' => 'post_id', 'owner_id', 'forum_id', 'blogged_time');
	}

	function new_instance()
	{
		$tab = $this->main_table_storage();

		$this->db()->replace($tab, array('post_id' => $this->id(), 'blogged_time' => time()));
	}

	function delete()
	{
		$this->cache_clean();
		$this->db()->query('DELETE FROM blog WHERE post_id = ' . $this->id());

		delete_cached_object($this);
	}

	function cache_clean()
	{
//		debug_exit('clean'.$this->owner_id());
		$blog = object_load('user_blog', $this->owner_id());
		if($blog)
			$blog->cache_clean_self($this->id());
	}
}
