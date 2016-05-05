<?php

require_once('inc/datetime.php');

class aviatop_main extends balancer_board_paginated
{
	function config_class() { return 'aviatop_config'; }
	function title() { return 'АвиаТОП'; }
	function description() { return 'Самые популярные русскоязычные сайты авиационной тематики'; }
	function parents() { return array('http://www.airbase.ru/'); }
	function create_time() { return strtotime('07.08.2000'); }

	function main_class() { return 'aviatop_week'; }
	function order() { return 'SUM(visits) DESC, top_id'; }
	function group() { return 'top_id'; }
//	function where() { return array('place > 0'); }

	private $_total;
	function total_items()
	{
		if(!is_null($this->_total))
			return $this->_total;

		return $this->_total = driver_mysql::factory('AVIATOP')->get('SELECT COUNT(DISTINCT(top_id)) FROM `aviatop_week`');
	}
}
