<?php

	class user_main extends base_page
	{
		function _class_file() { return __FILE__; }

		function template()
		{
			templates_noindex();
			return 'forum/_header.html';
		}

		var $user = NULL;
	
		function title() { return $this->user()->title().ec(": Информация"); }
		function nav_name() { return $this->user()->title(); }
		
		function user()
		{
			if($this->user === NULL)
				$this->user = class_load('forum_user', $this->id());

			return $this->user;
		}

		function parents()
		{
			return array("http://balancer.ru/users/");
		}

		function url() { return "http://balancer.ru/user/".$this->id()."/"; }

		function cache_static()
		{
			return 86400*14;
		}
	}
