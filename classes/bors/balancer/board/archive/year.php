<?php

class balancer_board_archive_year extends balancer_board_page
{
	function nav_name() { return $this->id(); }
	function title() { return ec('Архив тем форума за ').$this->id().ec(' год'); }

	function body_data()
	{
		return array(
			'month' => driver_mysql::factory('AB_FORUMS')->select_array('topics', 
				'MONTH(FROM_UNIXTIME(posted)) AS `month`, COUNT(*) AS `topics_count`', 
				array(
					'YEAR(FROM_UNIXTIME(posted))=' => $this->id(),
					'group' => '`month`',
					'order' => '`month`',
				)),
		);
	}

	function cache_static() { return config('static_forum') ? rand(3600, 7200) : 0; }
}
