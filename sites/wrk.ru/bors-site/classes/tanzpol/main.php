<?php

class tanzpol_main extends balancer_board_main
{
	function title() { return ec("Tanzpol: политика на форумах Balancer'а"); }
	function nav_name() { return ec('Tanzpol'); }
	function parents() { return array('http://www.wrk.ru/'); }

	function forums_where()
	{
		return array_merge(parent::forums_where(), [
			'cat_id' => 27,
		]);
	}
}
