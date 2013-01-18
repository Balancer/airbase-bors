<?php

class forum_abstract extends bors_page_db
{

	function db_name() { return config('punbb.database', 'AB_FORUMS'); }

	function template() { return 'forum/_header.html'; }

	function cache_life_time()
	{
		$GLOBALS['cms']['cache_disabled'] = true;
		 return -1;
	}
}
