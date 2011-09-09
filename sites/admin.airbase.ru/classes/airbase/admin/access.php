<?php

class airbase_admin_access extends bors_access
{
	function can_read() { return bors()->user_id() == 10000; }
}
