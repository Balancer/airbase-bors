<?
	require_once('BorsBaseObject.php');
	class BorsClassTopic extends BorsBaseObject
	{
		function type() { return 'topic'; }

		var $stb_forum_id = '';
		function forum_id() { return $this->stb_forum_id; }
		function set_forum_id($forum_id, $db_update = false) { $this->set("forum_id", $forum_id); }
		function field_forum_id_storage() { return 'punbb.topics.forum_id(id)'; }
		
		function field_title_storage() { return 'punbb.topics.subject(id)'; }
		function field_create_time_storage() { return 'punbb.topics.posted(id)'; }
		function field_modify_time_storage() { return 'punbb.topics.last_post(id)'; }

		BorsBaseObject::storage_register('last_author', 'punbb.topics.last_poster(id)');
		
        function parents()
		{
			return array(array('forum', $this->forum_id() ));
		}

        function body()
		{
			include_once("funcs/templates/assign.php");
			$data = array();

			$db = &new DataBase('punbb');

			$posts_per_page = 25;
			$start_from = ($this->page() - 1) * $posts_per_page;

			$query = "SELECT id FROM posts WHERE topic_id={$this->id()} ORDER BY id LIMIT $start_from, $posts_per_page";
			
			$posts = $db->get_array($query);
			if(empty($data['posts']))
			{
				$db->query("INSERT IGNORE posts SELECT * FROM posts_archive WHERE topic_id = {$this->id()}");
				$posts = $db->get_array($query);
			}

			$data['posts'] = array();

			foreach($posts as $pid)
				$data['posts'][] = class_load('post', $pid);

			return template_assign_data("BorsClassTopicBody.html", $data);
		}
	}
	