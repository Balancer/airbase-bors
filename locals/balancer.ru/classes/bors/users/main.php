<?
	class users_main extends base_page
	{
		function _class_file() { return __FILE__; }

	function template()
	{
		templates_noindex();
		return 'forum/main.html';
	}

		function title() { return ec("Пользователи Balancer.ru"); }
		function nav_name() { return ec("пользователи"); }

		function parents() { return array("http://balancer.ru/forum/"); }

		function url()
		{	
			return "http://balancer.ru/users/"; 
		}

		function cache_static()
		{
			return 86400*7;
		}
	}
