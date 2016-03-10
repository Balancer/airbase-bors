<?php

class balancer_board_stat_coordinators extends balancer_board_page
{
	function title() { return ec('Активность координаторов'); }
	function nav_name() { return ec('координаторы'); }
	function template() { return 'xfile:forum/_header.html'; }
	function is_auto_url_mapped_class() { return true; }

	function body_data()
	{
		$dbh = new driver_mysql('AB_FORUMS');
		$month_warns = $dbh->select_array('warnings', 'users.username as coordinator_name, count(*) as warnings_count', array(
			'inner_join' => 'users ON users.id = moderator_id',
			'time>' => time()-30.6*86400,
			'group' => 'moderator_id',
			'having' => 'warnings_count>0',
			'order' => 'warnings_count DESC',
		));

		$toyear = strtotime("-1 year");
		$to5year = strtotime("-5 year");
		$to10year = strtotime("-10 year");

		$month_warns_toyear = $dbh->select_array('warnings', 'users.username as coordinator_name, count(*) as warnings_count', array(
			'inner_join' => 'users ON users.id = moderator_id',
			'time BETWEEN' => [$toyear-30.6*86400, $toyear],
			'group' => 'moderator_id',
			'having' => 'warnings_count>0',
			'order' => 'warnings_count DESC',
		));


		$month_warns_to5year = $dbh->select_array('warnings', 'users.username as coordinator_name, count(*) as warnings_count', array(
			'inner_join' => 'users ON users.id = moderator_id',
			'time BETWEEN' => [$to5year-30.6*86400, $to5year],
			'group' => 'moderator_id',
			'having' => 'warnings_count>0',
			'order' => 'warnings_count DESC',
		));

		$month_warns_to10year = $dbh->select_array('warnings', 'users.username as coordinator_name, count(*) as warnings_count', array(
			'inner_join' => 'users ON users.id = moderator_id',
			'time BETWEEN' => [$to10year-30.6*86400, $to10year],
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

		$month_actions = $dbh->select_array('board_actions', 'users.username as coordinator_name, count(*) as actions_count', array(
			'inner_join' => 'users ON users.id = owner_id',
			'message NOT LIKE "Предупреждение%"',
			'create_time>' => time()-30.6*86400,
			'group' => 'owner_id',
			'having' => 'actions_count>0',
			'order' => 'actions_count DESC',
		));

		$year_actions = $dbh->select_array('board_actions', 'users.username as coordinator_name, count(*) as actions_count', array(
			'inner_join' => 'users ON users.id = owner_id',
			'message NOT LIKE "Предупреждение%"',
			'create_time>' => time()-365.24*86400,
			'group' => 'owner_id',
			'having' => 'actions_count>0',
			'order' => 'actions_count DESC',
		));

		return compact('month_actions', 'month_warns', 'month_warns_toyear', 'month_warns_to5year', 'month_warns_to10year', 'year_actions', 'year_warns');
	}
}
