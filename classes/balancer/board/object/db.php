<?php

class balancer_board_object_db extends bors_object_db
{
	function db_name() { return config('punbb.database', 'AB_FORUMS'); }
	function storage_engine() { return 'bors_storage_mysql'; }
}
