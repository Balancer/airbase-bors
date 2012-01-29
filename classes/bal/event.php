<?php

class bal_event extends bors_common_event
{
	function db_name() { return 'BALANCER'; }

	function html() { return bors_close_tags($this->text()); }
}
