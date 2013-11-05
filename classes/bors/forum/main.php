<?php

class forum_main extends balancer_board_page
{
	function title() { return ec("Старые форумы Balancer'а"); }
	function nav_name() { return ec('старые'); }
	function parents() { return array('http://forums.balancer.ru/'); }
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

	function url() { return 'http://www.balancer.ru/forum/'; }
}
