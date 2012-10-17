<?php

class aviatop_week extends bors_object_db
{
	function db_name() { return 'top'; }
	function table_name() { return 'aviatop_week'; }
	function table_fields()
	{
		return array(
			'id',
			'top_id',
			'visits',
			'check_time',
			'sum' => 'SUM(visits)',
			'per_week' => 'IF(SUM(visits) > 5, ROUND(86400*7*SUM(visits)/(MAX(check_time)-MIN(check_time) + 1)), 0)',
		);
	}
}
