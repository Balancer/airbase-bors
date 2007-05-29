<?
	require_once('ForumAbstract.php');
	class Topic extends ForumAbstract
	{
		function class_name() { return 'forum/Topic'; }
		function uri_name() { return 'topic'; }

		var $stb_forum_id = '';
		function forum_id() { return $this->stb_forum_id; }
		function set_forum_id($forum_id, $db_update = false) { $this->set("forum_id", $forum_id, $db_update); }
		function field_forum_id_storage() { return 'punbb.topics.forum_id(id)'; }
		
		function field_title_storage() { return 'punbb.topics.subject(id)'; }
		function field_create_time_storage() { return 'punbb.topics.posted(id)'; }
		function field_modify_time_storage() { return 'punbb.topics.last_post(id)'; }
		function field_owner_id_storage() { return 'punbb.topics.poster_id(id)'; }


        function parents()
		{
			return array(array('forum/Forum', $this->forum_id() ));
		}

        function body()
		{
//			$this->cache_clean_self();
		
			global $bors;

			$forum = class_load('forum/Forum', $this->forum_id());
		
			if(!$forum->can_read())
				return ec("Извините, доступ к этому ресурсу закрыт для Вас");

			if($forum->is_public_access())
				$GLOBALS['cms']['cache_static'] = true;

			$GLOBALS['cms']['cache_disabled'] = true;

			$bors->config()->set_cache_uri($this->internal_uri());
			
//			if($this->id() == 32510)
//				$GLOBALS['bors_data']['lcml_cache_disabled'] = true;

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

			$total = $this->num_replies()+1;
			if($total > $posts_per_page)
			{
				include_once('funcs/design/page_split.php');
				$data['pagelist'] = join(" ", pages_select($this, $this->page(), ($total-1)/$posts_per_page+1));
			}
			
			return template_assign_data("templates/TopicBody.html", $data);
		}

		function total_pages()
		{
			$posts_per_page = 25;
			return intval($this->num_replies() / $posts_per_page) + 1;
		}

		function pages_links()
		{
			if($this->total_pages() < 2)
				return "";

			include_once('funcs/design/page_split.php');
			return join(" ", pages_show($this, $this->total_pages(), 5));
		}

		var $stb_last_poster_name;
		function last_poster_name() { return $this->stb_last_poster_name; }
		function set_last_poster_name($last_poster_name, $db_update = false) { $this->set("last_poster_name", $last_poster_name, $db_update); }
		function field_last_poster_name_storage() { return 'punbb.topics.last_poster(id)'; }

		function cache_parents() { return array(class_load('forum', $this->forum_id()));}

		function forum() { return class_load('forum', $this->forum_id()); }

		var $stb_author_name = '';
		function set_author_name($author_name, $db_update = false) { $this->set("author_name", $author_name, $db_update); }
		function field_author_name_storage() { return 'punbb.topics.poster(id)'; }
		function author_name() { return $this->stb_author_name; }

		var $stb_num_replies = '';
		function set_num_replies($num_replies, $db_update = false) { $this->set("num_replies", $num_replies, $db_update); }
		function field_num_replies_storage() { return 'punbb.topics.num_replies(id)'; }
		function num_replies() { return $this->stb_num_replies; }

		function get_all_posts_id()
		{
			$db = &new DataBase('punbb');
			return $db->get_array("SELECT id FROM posts WHERE topic_id={$this->id} ORDER BY posted");
		}
	}
