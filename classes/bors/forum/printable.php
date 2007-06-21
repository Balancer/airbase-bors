<?
	class_include('forum_topic');
	class forum_printable extends forum_topic
	{
		function uri_name() { return 'printable'; }

        function parents() { return array("forum_topic://".$this->id()); }
        function nav_name() { return ec('Версия для печати'); }

        function body()
		{
			global $bors;

			$forum = class_load('forum_forum', $this->forum_id());
		
			if(!$forum->can_read())
				return ec("Извините, доступ к этому ресурсу закрыт для Вас");

			$GLOBALS['cms']['cache_disabled'] = true;

			$bors->config()->set_cache_uri($this->internal_uri());
			
			include_once("funcs/templates/assign.php");
			$data = array();

			$db = &new DataBase('punbb');

			$query = "SELECT id FROM posts WHERE topic_id={$this->id()} ORDER BY id";
			
			$posts = $db->get_array($query);

			$data['posts'] = array();

			foreach($posts as $pid)
				$data['posts'][] = class_load('forum_post', $pid);

			return template_assign_data("templates/printable.html", $data);
		}
		
		function template()
		{
			return "forum/printable.html";
		}

		function cache_static()
		{
			return class_load('forum_forum', $this->forum_id())->is_public_access() ? 86400*30 : 0;
		}
	}
