<?
	require_once("classes/bors/BorsBaseDbPage.php");

	class user_blog extends BorsBaseDbPage
	{
		function _class_file() { return __FILE__; }

		var $user;
	
		function uri_name() { return 'blog'; }

		function title() { return $this->user->title().ec(": Блог"); }
		function nav_name() { return ec("блог"); }

		function parents()
		{
			return array("forum_user://".$this->id());
		}

		function user_blog($id)
		{
			$GLOBALS['cms']['cache_disabled'] = true;
		
			$this->user = class_load('forum_user', $id);
			parent::BorsBaseDbPage($id);
			
			$this->add_template_data('user_id', $id);
		}

		function uri()
		{
			return "http://balancer.ru/user/".$this->id()."/blog.html";
		}

		function cache_static()
		{
			return 86400*7;
		}
		
		function template()
		{
			return "forum/forum.html";
		}
	}
