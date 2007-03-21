<?
	require_once('BaseObject.php');
	class BorsClassPost extends BaseObject
	{
		function type() { return 'post'; }

		var $stb_topic_id = '';
		function topic_id() { return $this->stb_topic_id; }
		function set_topic_id($topic_id, $db_update = false) { $this->set("topic_id", $topic_id, $db_update); }
		function field_topic_id_storage() { return 'punbb.posts.topic_id(id)'; }
		
		function field_create_time_storage() { return 'punbb.posts.posted(id)'; }
		
        function parents()
		{
			return array(array('topic', $this->topic_id() ));
		}

		var $stb_body = '';
		function set_body($body, $db_update = true) { $this->set("body", $body, $db_update); }
		function field_body_storage() { return 'punbb.messages.html(id)'; }
		function body()
		{
			if(empty($this->stb_body))
				$this->set_body(parse_message($this->source(), $this->hide_smilies()));
			
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
			if(empty($this->stb_flag))
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
	}
