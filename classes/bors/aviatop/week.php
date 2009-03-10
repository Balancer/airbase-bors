<?php

class aviatop_week extends base_page_db
{
	function main_db() { return 'top'; }
	function main_table() { return 'aviatop_week'; }
	function main_table_fields()
	{
		return array(
			'id' => 'top_id',
			'visits',
			'check_time',
			'sum' => 'SUM(visits)',
		);
	}
}
