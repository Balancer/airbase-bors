<?php

class forum_topic extends forum_abstract
{
	function storage_engine() { return 'storage_db_mysql_smart'; }
	function can_be_empty() { return false; }
	
	function main_db_storage() { return 'punbb'; }
	function main_table_storage() { return 'topics'; }

	function uri_name() { return 'topic'; }

	function fields() { return array($this->main_db_storage() => $this->main_db_fields()); }

	function main_db_fields()
	{
		if(bors()->user()->id() == 10000)
			set_loglevel(10);
	
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
			'last_poster_name' => 'last_poster',
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
	function set_last_poster_name($value, $dbupd) { $this->fset('last_poster_name', $value, $dbupd); }
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

		$data['posts'] = $this->posts();

		if(empty($data['posts']))
		{
			$this->db->query("INSERT IGNORE posts SELECT * FROM posts_archive_".($this->id()%10)." WHERE topic_id = {$this->id()}");
			$data['posts'] = $this->posts();
		}

		$this->add_template_data_array('header', "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".$this->rss_url()."\" title=\"Новые сообщения в теме '".addslashes($this->title())."'\" />");

		$data['this'] = $this;

		return template_assign_data("templates/TopicBody.html", $data);
	}

	private $__posts = NULL;
	private $__posts_map = NULL;
	private $__raw_posts = NULL;
	private $__posts_ids = NULL;

	private function raw_posts()
	{
		if($this->__raw_posts !== NULL)
			return $this->__posts;

		return $this->__raw_posts = objects_array('forum_post', array(
				'where' => array('topic_id=' => intval($this->id())),
				'order' => 'id',
				'page' => $this->page(),
				'per_page' => $this->items_per_page(),
			));
	}

	function get_all_posts_id()
	{
		if($this->__posts_ids !== NULL)
			return $this->__posts_ids;

		$post_ids = array();
		$this->raw_posts();

		for($i = 0; $i < count($this->__raw_posts); $i++)
		{
			$post = &$this->__raw_posts[$i];
			$pid = $post->id();
			$post_ids[] = $pid;
			$this->__posts_map[$pid] = &$post;
		}

		return $this->__posts_ids = $post_ids;
	}


	protected function posts()
	{
		if($this->__posts !== NULL)
			return $this->__posts;
	
		$user_ids = array();
		$answ_ids = array();

		foreach($this->get_all_posts_id() as $pid)
		{
			$post = &$this->__posts_map[$pid];

			$user_ids[] = $post->owner_id();
			$answ_ids[] = $post->answer_to_id();
		}

		$answ_ins = array();
		$answer_ids = array();
		foreach($answ_ids as $aid)
		{
			if(!$aid)
				continue;

			$answer_id[] = $aid;

			if(!in_array($aid, $this->__posts_ids))
				$answ_ins[] = $aid;
		}
		
		$post_ids = 'id IN('.join(',', $this->__posts_ids).')';
		$user_ids = 'id IN('.join(',', $user_ids).')';
		$answ_ins = 'id IN('.join(',', $answer_ids).')';

		$users = objects_array('forum_user', array($user_ids));
		$users_map = array();
		for($i = 0; $i < count($users); $i++)
		{
			$user = &$users[$i];
			$uid = $user->id();
			$users_map[$uid] = &$user;
		}

		if($answ_ins != 'id IN()')
			$answers = objects_array('forum_post', array($answ_ins));
		else
			$answers = array();
			
		$answers_map = array();
		for($i = 0; $i < count($answers); $i++)
		{
			$answer = &$answers[$i];
			$aid = $answer->id();
			$answers_map[$aid] = &$answer;
		}

		foreach($this->db($this->main_db_storage())->select_array('messages', 'id,message,html', array($post_ids)) as $x)
		{
			$post = &$this->__posts_map[$x['id']];
			$post->set_source($x['message'], false);
			$post->set_body($x['html'], false);
		}

		for($i = 0; $i < count($this->__posts); $i++)
		{
			$post = &$this->__posts[$i];
			$pid = $post->id();
			$uid = $post->owner_id();
			$post->set_owner($users_map[$uid], false);
			if($aid = $post->answer_to_id())
			{
				$answer = @$answers_map[$aid];
				if(!$answer)
					$answer = $posts[$aid];

				$post->set_answer_to($answer, false);
			}
		}
		
		return $this->__posts = $this->__raw_posts;
	}

	function items_per_page() { return 25; }

	function total_pages() { return intval($this->num_replies() / $this->items_per_page()) + 1; }

	function pages_links()
	{
		if($this->total_pages() < 2)
			return "";

		include_once('funcs/design/page_split.php');
		return join(" ", pages_show($this, $this->total_pages(), 16));
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

		$query = "SELECT poster, message FROM posts INNER JOIN messages ON posts.id = messages.id WHERE topic_id={$this->id()} ORDER BY posts.id LIMIT $start_from, ".$this->items_per_page();
			
		$posts = $db->get_array($query);
		if(empty($posts))
		{
			$db->query("INSERT IGNORE posts SELECT * FROM posts_archive_".($this->id()%10)." WHERE topic_id = {$this->id()}");
			$posts = $db->get_array($query);
		}

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
		if(sizeof($posts) == 0)
		{
			$db->query("INSERT IGNORE posts SELECT * FROM posts_archive_".($post_id%10)." WHERE topic_id = $post_id");
			$archive_loaded = true;
			$posts = $db->get_array("SELECT id FROM posts WHERE topic_id=$id ORDER BY posted");
		}

		for($i = 0, $stop=sizeof($posts); $i < $stop; $i++)
			if($posts[$i] == $post_id)
				return intval( $i / 25) + 1;
	}

	function recalculate()
	{
	
		global $bors;
		$bors->changed_save();
		
		$db = &new driver_mysql('punbb');
		$db->query("INSERT IGNORE posts SELECT * FROM posts_archive_".($this->id()%10)." WHERE topic_id = {$this->id()}");
		$num_replies = $db->select('posts', 'COUNT(*)', array('topic_id='=>$this->id())) - 1;
//		echo "Num repl of {$this->id()} =   $num_replies<br />\n";
		$this->set_num_replies($num_replies, true);
		$last_pid = $db->select('posts', 'MAX(id)', array('topic_id='=>$this->id()));
		$this->set_last_post_id($last_pid, true);
		$last_post = object_load('forum_post', $last_pid);
		$this->set_modify_time($last_post->create_time(true), true);
		$this->set_last_poster_name($last_post->owner()->title(), true);

		$bors->changed_save();

		$this->cache_clean_self();
		
		if($printable = object_load('forum_printable', $this->id()))
			$printable->cache_clean_self();
	}

	function url_engine() { return 'url_titled'; }
}
