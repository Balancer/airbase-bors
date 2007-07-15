<?
	require_once('classes/objects/BorsBaseObject.php');
	class forum_post extends BorsBaseObject
	{
		var $stb_topic_id = '';
		function topic_id() { return $this->stb_topic_id; }
		function set_topic_id($topic_id, $db_update = false) { $this->set("topic_id", $topic_id, $db_update); }
		function field_topic_id_storage() { return 'punbb.posts.topic_id(id)'; }

		function topic() { return class_load('forum_topic', $this->topic_id()); }
		
		function field_create_time_storage() { return 'punbb.posts.posted(id)'; }
		
        function parents() { return array("forum_topic://".$this->topic_id()); }

		var $stb_body = '';
		function set_body($body, $db_update = true) { $this->set("body", $body, $db_update); }
		function field_body_storage() { return 'punbb.messages.html(id)'; }
		function body()
		{
			if(empty($this->stb_body) || !empty($GLOBALS['bors_data']['lcml_cache_disabled']))
			{
				$body = lcml($this->source(), 
					array(
						'cr_type' => 'save_cr',
						'forum_type' => 'punbb',
						'forum_base_uri' => 'http://balancer.ru/forum',
						'sharp_not_comment' => true,
						'html_disable' => true,
						'uri' => "post://{$this->id()}/",
					)
				);
	
				$this->set_body($body);
			}
			return $this->stb_body; 
		}

		var $stb_source = '';
		function set_source($source, $db_update = false) { $this->set("source", $source, $db_update); }
		function field_source_storage() { return 'punbb.messages.message(id)'; }
		function source() { return $this->stb_source; }


		var $stb_flag = '';
		function set_flag($flag, $db_update = true) { $this->set("flag", $flag, $db_update); }
		function field_flag_storage() { return 'punbb.posts_cached_fields.flag(post_id)'; }
		function flag()
		{
			// Вторая часть условия - проверка на баг обрезания строки.
			if(empty($this->stb_flag) || !preg_match("!>$!", $this->stb_flag))
			{
				include_once('funcs/users/geoip/get_flag.php');
				$this->set_flag(get_flag($this->poster_ip()));
			}
			
			return $this->stb_flag; 
		}

		var $stb_poster_ip = '';
		function set_poster_ip($poster_ip, $db_update = false) { $this->set("poster_ip", $poster_ip, $db_update); }
		function field_poster_ip_storage() { return 'punbb.posts.poster_ip(id)'; }
		function poster_ip() { return $this->stb_poster_ip; }

		var $stb_author_name = '';
		function set_author_name($author_name, $db_update = false) { $this->set("author_name", $author_name, $db_update); }
		function field_author_name_storage() { return 'punbb.posts.poster(id)'; }
		function author_name() { return $this->stb_author_name; }

		var $stb_owner_id;
		function set_owner_id($owner_id, $db_update = false) { $this->set("owner_id", $owner_id, $db_update); }
		function field_owner_id_storage() { return 'punbb.posts.poster_id(id)'; }
		function owner_id() { return $this->stb_owner_id; }

		function owner() { return class_load('forum_user', $this->owner_id()); }

		var $stb_answer_to_id = '';
		function set_answer_to_id($answer_to_id, $db_update = false) { $this->set("answer_to_id", $answer_to_id, $db_update); }
		function field_answer_to_id_storage() { return 'punbb.posts.answer_to(id)'; }
		function answer_to_id() { return $this->stb_answer_to_id; }
		
		function answer_to()
		{
			if($id = $this->answer_to_id())
				return class_load('forum_post', $id);

			return false;
		}

		function preShowProcess()
		{
			$tid = $this->topic_id();
			$pid = $this->id();

			if(!$tid)
			{
				$this->set_body(ec("Указанный Вами топик не найден"), false);
				return false;
			}
	
			$topic = class_load('forum_topic', $tid);
	
			$posts = $topic->get_all_posts_id();

			$page = 1;

			for($i = 0, $stop=sizeof($posts); $i < $stop; $i++)
				if ($posts[$i] == $pid)
				{
					$page = intval( $i / 25) + 1;
					break;
				}
			
			require_once('funcs/navigation/go.php');
			return go($topic->uri($page)."#p".$pid, true, 0, false);
		}

		function uri() 
		{ 
			require_once("funcs/modules/uri.php");
			return "http://balancer.ru/".strftime("%Y/%m/%d/post-", $this->modify_time()).$this->id().".html"; 
		}
		
		function title()
		{
			return $this->topic()->title()." <small>[".$this->owner()->title().", ".strftime("%d.%m.%y", $this->create_time())."]</small>";
		}

		function base_uri()
		{
			return $this->topic()->forum()->category()->category_base_full();
		}
	}
