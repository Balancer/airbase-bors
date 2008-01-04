<?php

class forum_post extends base_page_db
{
	function storage_engine() { return 'storage_db_mysql_smart'; }
	function can_be_empty() { return false; }
	
	function main_db_storage() { return 'punbb'; }
	function main_table_storage() { return 'posts'; }
	function fields() { return array($this->main_db_storage() => $this->main_db_fields()); }

	function main_db_fields()
	{
		return array(
			$this->main_table_storage() => $this->main_table_fields(),
			'messages' => array(
				'body' => 'html',
				'source' => 'message',
			),
			'posts_cached_fields(post_id)' => array(
				'flag',
			),
		);
	}

	function main_table_fields()
	{
		return array(
			'topic_id',
			'create_time'	=> 'posted',
			'modify_time'=> 'edited',
			'owner_id'=> 'poster_id',
			'poster_ip',
			'author_name' => 'poster',
		);
	}

	function set_topic_id($value, $dbupd) { $this->fset('topic_id', $value, $dbupd); }
	function set_create_time($value, $dbupd) { $this->fset('create_time', $value, $dbupd); }
	function set_modify_time($value, $dbupd) { $this->fset('modify_time', $value, $dbupd); }
	function set_body($body, $db_update) { $this->fset('body', $body, $db_update); }
	function set_flag($flag, $db_update) { $this->fset('flag', $flag, $db_update); }
	function set_owner_id($owner_id, $db_update) { $this->fset('owner_id', $owner_id, $db_update); }
	function set_poster_ip($poster_ip, $db_update) { $this->fset('poster_ip', $poster_ip, $db_update); }
	function set_author_name($author_name, $db_update) { $this->fset('author_name', $author_name, $db_update); }
	
	function topic() { return object_load('forum_topic', $this->topic_id()); }
	function parents() { return array("forum_topic://".$this->topic_id()); }
	function owner() { return object_load('forum_user', $this->owner_id()); }

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
	
			$this->set_body($body, true);
		}

		return $this->stb_body; 
	}

	var $_source_changed = false;
	function set_source($source, $db_update) { $this->fset('source', $source, $db_update); $this->_source_changed |= $db_update; }

	function flag()
	{
		// Вторая часть условия - проверка на баг обрезания строки.
		if(empty($this->stb_flag) || !preg_match("!>$!", $this->stb_flag))
		{
			include_once('funcs/users/geoip/get_flag.php');
			$this->set_flag(get_flag($this->poster_ip()), true);
		}
		
		return $this->stb_flag; 
	}



	var $stb_answer_to_id = '';
	function set_answer_to_id($answer_to_id, $db_update) { $this->set("answer_to_id", $answer_to_id, $db_update); }
	function field_answer_to_id_storage() { return 'answer_to(id)'; }
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
			$this->set_body(ec("Указанный Вами топик [{$this->topic_id()}/{$this->id()}] не найден"), false);
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

		return go($topic->url($page)."#p".$pid, true, 0, false);
	}

	function url() 
	{ 
		require_once("funcs/modules/uri.php");
		return 'http://balancer.ru/'.strftime("%Y/%m/%d/post-", $this->modify_time()).$this->id().".html";
	}
		
	function title() { return $this->topic()->title()." <small>[".$this->nav_name()."]</small>"; }
	function nav_name() { return $this->owner()->title().", ".strftime("%d.%m.%y", $this->create_time()); }

	function base_url()
	{
		return $this->topic()->forum()->category()->category_base_full();
	}

	var $_attach_ids = false;

	function attach_ids()
	{
		if($this->_attach_ids !== false)
			return $this->_attach_ids;

		$db = &new DataBase('punbb');
		return $this->_attach_ids = $db->get_array("SELECT id FROM attach_2_files WHERE post_id = ".$this->id());
	}
		
	function attaches()
	{
		$result = array();
		foreach($this->attach_ids() as $attach_id)
			$result[] = class_load('forum_attach', $attach_id);

		return $result;
	}

	function search_source() { return $this->source(); }
	
	function num_replies()
	{
		return 0;
		
		$db = &new DataBase('punbb');
		return intval($db->get("SELECT COUNT(*) FROM posts WHERE answer_to = {$this->id}"));
	}

	function num_views() { return $this->topic()->num_views(); }

	function class_title() { return ec("Сообщение форума"); }
	
	function answers()
	{
		$result = array();
		foreach($this->select_array('id', array('answer_to=' => $this->id())) as $id)
			if($post = object_load('forum_post', $id))
				$result[] = $post;

		return $result;
	}

	function answers_in_other_topics()
	{
		$result = array();
		foreach($this->select_array('id', array('answer_to=' => $this->id(), 'topic_id<>' => $this->topic_id())) as $pid)
			if($post = object_load('forum_post', $pid))
				$result[] = $post;

		return $result;
	}
	
	function answers_in_this_topic()
	{
		$result = array();
		foreach($this->select_array('id', array('answer_to=' => $this->id(), 'topic_id=' => $this->topic_id())) as $pid)
			if($post = object_load('forum_post', $pid))
				$result[] = $post;

		return $result;
	}
	
	function move_tree_to_topic($new_tid)
	{
//		echo "Post {$this->id()}: create_time=".strftime("%c", $this->create_time()).", modify_time=".strftime("%c", $this->modify_time(true)).", change_time=".strftime("%c", $this->change_time(true))."<br />\n";
	
		$GLOBALS['move_tree_to_topic_changed_topics'] = array();
	
		$this->__move_tree_to_topic($new_tid, $this->topic_id());
		
//		print_r($GLOBALS['move_tree_to_topic_changed_topics']);
		
		foreach(array_keys($GLOBALS['move_tree_to_topic_changed_topics']) as $tid)
			object_load('forum_topic', $tid)->recalculate();
	}

	private function __move_tree_to_topic($new_tid, $old_tid)
	{
		$GLOBALS['move_tree_to_topic_changed_topics'][$new_tid] = true;
		$GLOBALS['move_tree_to_topic_changed_topics'][$this->topic_id()] = true;
		
//		echo "Move {$this->id()} from {$this->topic_id()} to {$new_tid}<br />\n";
	
		if($this->topic_id() == $old_tid)
			$this->set_topic_id($new_tid, true);

		foreach($this->answers() as $answer)
			$answer->__move_tree_to_topic($new_tid, $old_tid);
	}

	function auto_search_index() { return $this->_source_changed; }
}
