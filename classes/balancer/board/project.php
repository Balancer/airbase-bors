<?php

class balancer_board_project extends bors_project
{
	function nav_name() { return "Форумы Balancer'а"; }
	function url() { return 'http://balancer.ru/forum/'; }

	function register_url() { return 'http://www.balancer.ru/forum/punbb/register.php'; }
//	function login_url()    { return config('main_site_url').'/users/register/'; }

	function brand_nav_ajax_url() { return '/ajax/forums/list/'; }
//	function brand_nav() { return file_get_contents($this->brand_nav_ajax_url()); }
}
