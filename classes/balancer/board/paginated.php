<?php

class balancer_board_paginated extends bors_paginated
{
	function config_class() { return 'balancer_board_config'; }
	function is_public_access() { return true; }
}
