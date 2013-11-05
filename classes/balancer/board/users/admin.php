<?php

class balancer_board_users_admin extends balancer_board_page
{
	function title() { return ec('Управление пользователями'); }
	function nav_name() { return ec('пользователи'); }
	function config_class() { return 'balancer_board_admin_config'; }
}
