<?php
require_once('borsForumAbstract.php');

class forum_topic extends borsForumAbstract
{
	function storage_engine() { return 'storage_db_mysql_smart'; }
	function can_be_empty() { return false; }
	
	function main_db_storage() { return 'punbb'; }
	function main_table_storage() { return 'topics'; }

	function uri_name() { return 'topic'; }

	function main_db_fields()
	{
		return array(
			$this->main_table_storage() => $this->main_table_fields(),
		);
	}

	function main_table_fields()
	{
		return array(
			'forum_id',
			'title'	=> 'subject',
			'create_time'	=> 'posted',
			'modify_time'=> 'last_post',
			'owner_id'=> 'poster_id',
			'poster_name' => 'last_poster',
			'author_name' => 'poster',
			'num_replies',
			'num_views',
			'first_post_id' => 'first_pid',
			'last_post_id' => 'last_post_id',
		);
	}

	function set_forum_id($value, $dbupd) { $this->fset('forum_id', $value, $dbupd); }
	function set_title($value, $dbupd) { $this->fset('title', $value, $dbupd); }
	function set_create_time($value, $dbupd) { $this->fset('create_time', $value, $dbupd); }
	function set_modify_time($value, $dbupd) { $this->fset('modify_time', $value, $dbupd); }
	function set_owner_id($value, $dbupd) { $this->fset('owner_id', $value, $dbupd); }
	function set_poster_name($value, $dbupd) { $this->fset('poster_name', $value, $dbupd); }
	function set_author_name($value, $dbupd) { $this->fset('author_name', $value, $dbupd); }
	function set_num_replies($num_replies, $db_update) { $this->fset('num_replies', $num_replies, $db_update); }
	function set_num_views($num_views, $db_update) { $this->fset('num_views', $num_views, $db_update); }
	function set_first_post_id($first_post_id, $db_update) { $this->fset('first_post_id', $first_post_id, $db_update); }
	function set_last_post_id($last_post_id, $db_update) { $this->fset('last_post_id', $last_post_id, $db_update); }


	function forum() { return object_load('forum_forum', $this->forum_id()); }
	function first_post() { return object_load('forum_post', $this->first_post_id()); }

		
	function parents() { return array("forum_forum://".$this->forum_id()); }


	function body()
	{
//			$this->cache_clean_self();
		
		global $bors;

		if(!$this->forum()->can_read())
		{
			templates_noindex();
			return ec("Извините, доступ к этому ресурсу закрыт для Вас");
		}
		
		$GLOBALS['cms']['cache_disabled'] = true;

		$bors->config()->set_cache_uri($this->internal_uri());
			
//		if($this->id() == 32510)
//			$GLOBALS['bors_data']['lcml_cache_disabled'] = true;

		include_once("funcs/templates/assign.php");
		$data = array();

		$data['posts'] = objects_array('forum_post', array(
			'where' => array('int topic_id' => intval($this->id())),
			'order' => 'id',
			'page' => $this->page(),
			'per_page' => $this->items_per_page(),
		));

		$this->add_template_data_array('header', "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".$this->rss_url()."\" title=\"Новые сообщения в теме '".addslashes($this->title())."'\" />");

		$data['this'] = $this;

		return template_assign_data("templates/TopicBody.html", $data);
	}

	function items_per_page() { return 25; }

	function total_pages()
	{
		return intval($this->num_replies() / $this->items_per_page()) + 1;
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


	function cache_parents()
	{
		$res = array(
			object_load('forum_forum', $this->forum_id()),
			object_load('forum_printable', $this->id()),
		);

		foreach($this->all_users() as $user_id)
			$res[] = object_load('forum_user', $user_id);
			
		return $res;
	}


		
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

		$start_from = ($this->page() - 1) * $this->items_per_page();

		$query = "SELECT poster, message FROM posts INNER JOIN messages ON posts.id = messages.id WHERE topic_id={$this->id()} ORDER BY posts.id LIMIT $start_from, $posts_per_page";
			
		$posts = $db->get_array($query);
//		if(empty($posts))
//		{
//			$db->query("INSERT IGNORE posts SELECT * FROM posts_archive WHERE topic_id = {$this->id()}");
//			$posts = $db->get_array($query);
//		}

		$data['posts'] = array();

		foreach($posts as $x)
		{
//			$post = class_load('forum_post', $pid);
			if($x['message'])
				$result[] = $x['poster'].":\n---------------\n".$x['message'];
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
