<?
	require_once('classes/bors/BorsPageJS.php');
	class forum_userPersonalJS extends BorsPageJS
	{
		function _class_file() { return __FILE__; }
		
		function cache_static()
		{
			return 7*86400;
		}
		
		function uri()
		{
			return "http://balancer.ru/user/".$this->id()."/personal.js";
		}
	}
