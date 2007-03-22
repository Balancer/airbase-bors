<?
	require_once('BorsBaseObject.php');
	class BorsClassTopic extends BorsBaseObject
	{
		function type() { return 'topic'; }

		var $stb_forum_id = '';
		function forum_id() { return $this->stb_forum_id; }
		function set_forum_id($forum_id, $db_update = false) { $this->set("forum_id", $forum_id, $db_update); }
		function field_forum_id_storage() { return 'punbb.topics.forum_id(id)'; }
		
		function field_title_storage() { return 'punbb.topics.subject(id)'; }
		function field_create_time_storage() { return 'punbb.topics.posted(id)'; }
		function field_modify_time_storage() { return 'punbb.topics.last_post(id)'; }


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
			if(empty($posts))
			{
				$db->query("INSERT IGNORE posts SELECT * FROM posts_archive WHERE topic_id = {$this->id()}");
				$posts = $db->get_array($query);
			}

			$data['posts'] = array();

			foreach($posts as $pid)
				$data['posts'][] = class_load('post', $pid);

			$total = $db->get("SELECT COUNT(*) FROM posts WHERE topic_id = {$this->id()}");
			if($total > $posts_per_page)
			{
				include_once('funcs/design/page_split.php');
				$data['pagelist'] = join(" ", pages_select($this, $this->page(), ($total-1)/$posts_per_page+1));
			}
			
			return template_assign_data("BorsClassTopicBody.html", $data);
		}

		var $stb_last_poster;
		function last_poster() { return $this->stb_last_poster; }
		function set_last_poster($last_poster, $db_update = false) { $this->set("last_poster", $last_poster, $db_update); }
		function field_last_poster_storage() { return 'punbb.topics.last_poster(id)'; }
	}
