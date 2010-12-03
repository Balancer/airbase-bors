<?php

class balancer_board_stat_coordinators extends bors_page
{
	function title() { return ec('Активность координаторов'); }
	function nav_name() { return ec('координаторы'); }
	function template() { return 'xfile:forum/_header.html'; }
	function is_auto_url_mapped_class() { return true; }

	function body_data()
	{
		$dbh = new driver_mysql('punbb');
		$month_warns = $dbh->select_array('warnings', 'users.username as coordinator_name, count(*) as warnings_count', array(
			'inner_join' => 'users ON users.id = moderator_id',
			'time>' => time()-30.6*86400,
			'group' => 'moderator_id',
			'having' => 'warnings_count>0',
			'order' => 'warnings_count DESC',
		));

		$year_warns = $dbh->select_array('warnings', 'users.username as coordinator_name, count(*) as warnings_count', array(
			'inner_join' => 'users ON users.id = moderator_id',
			'time>' => time()-365.24*86400,
			'group' => 'moderator_id',
			'having' => 'warnings_count>0',
			'order' => 'warnings_count DESC',
		));

		return compact('month_warns', 'year_warns');
	}
}
