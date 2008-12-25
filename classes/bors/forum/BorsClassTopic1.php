<?
	require_once('BaseObject.php');
	class BorsClassTopic extends BaseObject
	{
		function type() { return 'topic'; }

		var $stb_forum_id = '';
		function forum_id() { return $this->stb_forum_id; }
		function set_forum_id($forum_id, $db_update) { $this->set("forum_id", $forum_id); }
		function field_forum_id_storage() { return 'punbb.topics.forum_id(id)'; }
		
		function field_title_storage() { return 'punbb.topics.subject(id)'; }
		function field_create_time_storage() { return 'punbb.topics.posted(id)'; }
		
        function parents()
		{
			return array(array('forum', $this->forum_id() ));
		}

        function body()
		{
			include_once("engines/smarty/assign.php");
			$data = array();

			$db = &new DataBase('punbb');

			$posts_per_page = 25;
			$start_from = ($this->page() - 1) * $posts_per_page;

			$query = <<<_EOT_
					SELECT 
						p.id, 
						p.poster AS username, 
						p.poster_id, 
						p.poster_ip, 
						p.poster_email, 
						p.hide_smilies, 
						p.posted, 
						p.edited, 
						p.edited_by,
						p.answer_to,
						up.posted as up_posted,
						up.poster as up_poster,
						m.message,
						m.html,
						cf.flag
					FROM posts AS p
						LEFT JOIN posts AS up ON (p.answer_to = up.id)
						LEFT JOIN posts_cached_fields AS cf ON (p.id = cf.post_id)
						LEFT JOIN messages AS m ON (p.id = m.id)
					WHERE p.topic_id={$this->id()}
					ORDER BY p.id 
					LIMIT $start_from, $posts_per_page;
_EOT_;

			
			$posts = $db->get_array($query);
			if(empty($data['posts']))
			{
				$db->query("INSERT IGNORE posts SELECT * FROM posts_archive WHERE topic_id = {$this->id()}");
				$posts = $db->get_array($query);
			}

			$data['posts'] = array();

			foreach($posts as $p)
			{
				if(empty($p['flag']))
				{
					include_once('funcs/users/geoip/get_flag.php');
					$db->insert_ignore('posts_cached_fields', array(
						'post_id'	=> $p['id'],
						'flag'		=> $p['flag'] = "".get_flag($p['poster_ip']),
					));
				}
				
				$data['posts'][] = $p;
			}

			$db->close();
			return template_assign_data("BorsClassTopicBody.html", $data);
		}
	}

	