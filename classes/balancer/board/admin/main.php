<?php

class balancer_board_admin_main extends base_page
{
	function title() { return ec('Управление форумами'); }
	function nav_name() { return ec('управление'); }
	function config_class() { return 'balancer_board_admin_config'; }
}
