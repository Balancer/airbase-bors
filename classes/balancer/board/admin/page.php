<?php

class balancer_board_admin_page extends balancer_board_page
{
	function config_class() { return 'balancer_board_admin_config'; }
	function template() { return 'xfile:bootstrap/index.html'; }
	function auto_map() { return true; }
}
