<?php

class forum_main extends base_page
{
	function title() { return ec("Форумы Balancer'а"); }
	function nav_name() { return ec('форумы'); }
	function parents() { return array('http://balancer.ru/'); }
	function create_time() { return 943531800; }

	function _queries()
	{
		return array(
			'base_categories' => 'SELECT * FROM categories WHERE parent = 0 ORDER BY disp_position',
			'topics' => 'SELECT id FROM topics ORDER BY last_post DESC LIMIT '.(($this->page()-1)*50).',50',
		);
	}

	function storage_engine() { return ''; }

//	function cache_static() { return 300; }

	function url() { return 'http://balancer.ru/forum/'; }
}
