<?php

class users_main extends base_page
{
	function template()
	{
		template_noindex();
		return 'forum/common.html';
	}

	function title() { return ec("Пользователи Balancer.ru"); }
	function nav_name() { return ec("пользователи"); }

	function parents() { return array("http://www.balancer.ru/forum/"); }

	function url() { return "http://www.balancer.ru/users/"; }

	function cache_static() { return config('static_forum') ? 86400*7 : 0; }
}
