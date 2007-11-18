<?php
require_once('borsForumAbstract.php');

class forum_topic extends borsForumAbstract
{
	function storage_engine() { return 'storage_db_mysql'; }
	function can_be_empty() { return false; }
	
	function main_db_storage() { return 'punbb'; }
	function main_table_storage() { return 'topics'; }

	function uri_name() { return 'topic'; }

	var $stb_forum_id = '';
	function forum_id() { return $this->stb_forum_id; }
	function set_forum_id($forum_id, $db_update) { $this->set("forum_id", $forum_id, $db_update); }
	function field_forum_id_storage() { return 'punbb.topics.forum_id(id)'; }
		
	function field_title_storage() { return 'punbb.topics.subject(id)'; }
	function field_create_time_storage() { return 'punbb.topics.posted(id)'; }
	function field_modify_time_storage() { return 'punbb.topics.last_post(id)'; }
	function field_owner_id_storage() { return 'punbb.topics.poster_id(id)'; }

	function parents() { return array("forum_forum://".$this->forum_id()); }

	function body()
	{
//			$this->cache_clean_self();
		
		global $bors;

		$forum = class_load('forum_forum', $this->forum_id());
		
		if(!$forum->can_read())
			return ec("Извините, доступ к этому ресурсу закрыт для Вас");

		$GLOBALS['cms']['cache_disabled'] = true;

		$bors->config()->set_cache_uri($this->internal_uri());
			
//		if($this->id() == 32510)
//			$GLOBALS['bors_data']['lcml_cache_disabled'] = true;

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
			$data['posts'][] = class_load('forum_post', $pid);

		$this->add_template_data_array('header', "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".$this->rss_url()."\" title=\"Новые сообщения в теме '".addslashes($this->title())."'\" />");

		$data['this'] = $this;

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
		return join(" ", pages_show($this, $this->total_pages(), 20));
	}

	function title_pages_links()
	{
		if($this->total_pages() < 2)
			return "";

		include_once('funcs/design/page_split.php');
		return join(" ", pages_show($this, $this->total_pages(), 5, false));
	}

	var $stb_last_poster_name;
	function last_poster_name() { return $this->stb_last_poster_name; }
	function set_last_poster_name($last_poster_name, $db_update) { $this->set("last_poster_name", $last_poster_name, $db_update); }
	function field_last_poster_name_storage() { return 'punbb.topics.last_poster(id)'; }

	function cache_parents()
	{
		$res = array(
			class_load('forum_forum', $this->forum_id()),
			class_load('forum_printable', $this->id()),
		);

		foreach($this->all_users() as $user_id)
			$res[] = class_load('forum_user', $user_id);
			
		return $res;
	}

	function forum() { return class_load('forum_forum', $this->forum_id()); }

	var $stb_author_name = '';
	function set_author_name($author_name, $db_update) { $this->set("author_name", $author_name, $db_update); }
	function field_author_name_storage() { return 'punbb.topics.poster(id)'; }
	function author_name() { return $this->stb_author_name; }

	var $stb_num_replies = '';
	function set_num_replies($num_replies, $db_update) { $this->set("num_replies", $num_replies, $db_update); }
	function field_num_replies_storage() { return 'num_replies(id)'; }
	function num_replies() { return $this->stb_num_replies; }

	var $stb_num_views = '';
	function set_num_views($num_views, $db_update) { $this->set("num_views", $num_views, $db_update); }
	function field_num_views_storage() { return 'punbb.topics.num_views(id)'; }
	function num_views() { return $this->stb_num_views; }

	var $stb_first_post_id = '';
	function set_first_post_id($first_post_id, $db_update) { $this->set("first_post_id", $first_post_id, $db_update); }
	function field_first_post_id_storage() { return 'punbb.topics.first_pid(id)'; }
	function first_post_id() { return $this->stb_first_post_id; }

	function first_post() { return class_load('forum_post', $this->first_post_id()); }

	var $stb_last_post_id = '';
	function set_last_post_id($last_post_id, $db_update) { $this->set("last_post_id", $last_post_id, $db_update); }
	function field_last_post_id_storage() { return 'last_post_id(id)'; }
	function last_post_id() { return $this->stb_last_post_id; }
		
	function get_all_posts_id()
	{
		$db = &new DataBase('punbb');
		return $db->get_array("SELECT id FROM posts WHERE topic_id={$this->id} ORDER BY posted");
	}

	function all_users()
	{
		$db = &new DataBase('punbb');
		return $db->get_array("SELECT DISTINCT poster_id FROM posts WHERE topic_id={$this->id}");
	}
		
	function cache_static() { return $this->forum()->is_public_access() ? 86400*30 : 0; }
	function base_url() { return $this->forum()->category()->category_base_full(); }
		
	function title_url()
	{
		return "<a href=\"".$this->url()."\">".$this->title()."</a>";
	}

	function rss_url() { return $this->base_url().strftime("%Y/%m/%d/", $this->modify_time())."topic-".$this->id()."-rss.xml"; }

	function search_source()
	{
		$result = array();
	
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
		{
			$post = class_load('forum_post', $pid);
			if($post)
				$result[] = $post->author_name().":\n---------------\n".$post->source();
		}
		
		return join("\n============================\n\n", $result);
	}
	
	function page_by_post_id($post_id)
	{
		$post_id = intval($post_id);
	
		$db = &new DataBase('punbb');

		$posts = $db->get_array("SELECT id FROM posts WHERE topic_id={$this->id()} ORDER BY posted");
/*		if(sizeof($posts) == 0)
		{
			$db->query("INSERT IGNORE posts SELECT * FROM posts_archive WHERE topic_id = $post_id");
			$archive_loaded = true;
			$posts = $db->get_array("SELECT id FROM posts WHERE topic_id=$id ORDER BY posted");
		}
*/
		for($i = 0, $stop=sizeof($posts); $i < $stop; $i++)
			if($posts[$i] == $post_id)
				return intval( $i / 25) + 1;
	}

	function recalculate()
	{
		global $bors;
		$bors->changed_save();
		
		$num_replies = $this->db->select('posts', 'COUNT(*)', array('topic_id='=>$this->id())) - 1;
//		echo "Num repl of {$this->id()} =   $num_replies<br />\n";
		$this->set_num_replies($num_replies, true);
		$last_pid = $this->db->select('posts', 'MAX(id)', array('topic_id='=>$this->id()));
		$this->set_last_post_id($last_pid, true);
		$last_post = object_load('forum_post', $last_pid);
		$this->set_modify_time($last_post->create_time(true), true);
		$this->set_last_poster_name($last_post->owner()->title(), true);
	}
}
