<?php

class balancer_board_page extends bors_page
{
	function config_class() { return 'balancer_board_config'; }
	function template() { return 'xfile:forum/page.html'; }
	function auto_map() { return true; }
}
