<?php

class balancer_board_mobile_page extends bors_page
{
	function is_auto_url_mapped_class() { return true; }
	function template() { return 'xfile:balancer/board/mobile/index.html'; }
	function config_class() { return 'balancer_board_mobile_config'; }

	function access() { return bors_load('balancer_board_mobile_access', $this); }
}
