<?php

class aviatop_week extends base_object_db
{
	function main_db() { return 'top'; }
	function main_table() { return 'aviatop_week'; }
	function main_table_fields()
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
