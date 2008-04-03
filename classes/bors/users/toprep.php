<?php

class users_toprep extends base_page_db
{
	function main_db_storage(){ return 'punbb'; }

	function template()
	{
		templates_noindex();
		return 'forum/_header.html';
	}

	function title() { return ec("Репутации пользователей форума"); }
	function nav_name() { return ec("репутации"); }

	function parents() { return array("http://balancer.ru/users/"); }

	function _queries()
	{
		return array(
			'high' => 'SELECT * FROM users ORDER BY reputation DESC LIMIT 50',
			'low' => 'SELECT * FROM users ORDER BY reputation LIMIT 50',

			'pure_high' => 'SELECT * FROM users ORDER BY pure_reputation DESC LIMIT 20',
			'pure_low' => 'SELECT * FROM users ORDER BY pure_reputation LIMIT 20',
		);
	}

	function url()
	{	
		return "http://balancer.ru/users/toprep/"; 
	}

	function cache_static()
	{
		return 3600;
	}
	
	function can_be_empty() { return true; }
}
