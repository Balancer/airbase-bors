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
//			'messages' => array(
//				'body' => 'html',
//				'source' => 'message',
//			),
			'posts_cached_fields(post_id)' => array(
				'flag_db' => 'flag',
				'warning_id',
			),
		);
	}

	function main_table_fields()
	{
		return array(
			'id',
			'topic_id',
			'create_time'	=> 'posted',
			'edited',
			'owner_id'=> 'poster_id',
			'poster_ip',
			'author_name' => 'poster',
			'answer_to_id' => 'answer_to',
		);
	}

	function set_topic_id($value, $dbupd) { $this->fset('topic_id', $value, $dbupd); }
	function set_create_time($value, $dbupd) { $this->fset('create_time', $value, $dbupd); }
	function set_edited($value, $dbupd) { $this->fset('edited', $value, $dbupd); }
	function set_flag_db($flag, $db_update) { $this->fset('flag_db', $flag, $db_update); }
	function set_owner_id($owner_id, $db_update) { $this->fset('owner_id', $owner_id, $db_update); }
	function set_poster_ip($poster_ip, $db_update) { $this->fset('poster_ip', $poster_ip, $db_update); }
	function set_author_name($author_name, $db_update) { $this->fset('author_name', $author_name, $db_update); }
	
	function topic() { return object_load('forum_topic', $this->topic_id()); }
	function parents() { return array("forum_topic://".$this->topic_id()); }

	private $__owner = NULL;
	function owner()
	{
		if($this->__owner === NULL)
			$this->__owner =  object_load('forum_user', $this->owner_id());

		return $this->__owner;
	}

	function set_owner($owner, $dbup)
	{
		$this->__owner = $owner;
	}

	var $_post_source = false;
	var $_post_body = false;

	function source()
	{
		if($this->_post_source === false)
		{
			$x = $this->db->select('messages', 'message,html', array('id=' => $this->id()));
			$this->_post_source = $x['message'];
			$this->_post_body = $x['html'];
		}
		
		return $this->_post_source;
	}

	var $_source_changed = false;

	function set_source($message, $db_update)
	{
		if($db_update)
		{
			$this->db->store('messages', array(
				'id' => $this->id(),
				'message' => $message,
			));
		}

		$this->_source_changed |= $db_update;

		return $this->_post_source = $message;
	}

	function set_body($html, $db_update)
	{
		if($db_update)
		{
			$this->db->store('messages', 'id='.$this->id(), array(
				'id' => $this->id(),
				'html' => $html,
			));
		}
		return $this->_post_body = $html;
	}

	function body()
	{
		$this->source();
	
		if(empty($this->_post_body) || !empty($GLOBALS['bors_data']['lcml_cache_disabled']))
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

		return $this->_post_body; 
	}

	function flag()
	{
		// Вторая часть условия - проверка на баг обрезания строки.
		if(!$this->flag_db() || !preg_match("!>$!", $this->flag_db()))
		{
			include_once('funcs/users/geoip/get_flag.php');
			$this->db()->insert_ignore('posts_cached_fields', array('post_id' => $this->id()));
			$this->set_flag_db(get_flag($this->poster_ip(), $this->owner()), true);
		}
		
		return $this->flag_db();
	}

	private $__answer_to = 0;
	function answer_to()
	{
		if($this->__answer_to !== 0)
			return $this->__answer_to;
	
		if($id = $this->answer_to_id())
			return $this->__answer_to =  class_load('forum_post', $id);

		return $this->__answer_to = false;
	}

	function set_answer_to($post, $dbup)
	{
		return $this->__answer_to = $post;
	}

	function cache_static() { return rand(86400*7, 86400*8); }

	function template() { return 'empty.html'; }
	function render() { return 'render_fullpage'; }

	function empty_body()
	{
		require_once('engines/smarty/assign.php');
		return template_assign_data('post.html', array('this' => $this));
	}
	
	function url_in_topic($topic = NULL)
	{
		$pid = $this->id();
		
		if(!$topic)
		{
			$tid = $this->topic_id();

			if(!$tid)
			{
				$this->set_body(ec("Указанный Вами топик [{$this->topic_id()}/{$this->id()}] не найден"), false);
				return false;
			}

			$topic = object_load('forum_topic', $tid);
		}
	
		$posts = $topic->all_posts_ids();

		$page = 1;

		for($i = 0, $stop=sizeof($posts); $i < $stop; $i++)
			if ($posts[$i] == $pid)
			{
				$page = intval( $i / 25) + 1;
				break;
			}
			
		return $topic->url($page)."?#p".$pid;
	}

	function modify_time()
	{
		if($time = $this->edited())
			return $time;

		return $this->create_time();
	}

	function url() 
	{ 
		require_once("inc/urls.php");
		return 'http://balancer.ru/'.strftime("%Y/%m/%d/post-", $this->modify_time()).$this->id().".html";
	}
		
	function title() { return $this->topic()->title()." <small>[".$this->nav_name()."]</small>"; }
	function nav_name() { return ($this->owner() ? $this->owner()->title() : 'Unknown').", ".strftime("%d.%m.%y", $this->create_time()); }

	function base_url()
	{
		return $this->topic()->forum()->category()->category_base_full();
	}

	private $_attach_ids = false;
	function attach_ids()
	{
		if($this->_attach_ids !== false)
			return $this->_attach_ids;

		$db = &new DataBase('punbb');
		return $this->_attach_ids = $db->get_array("SELECT id FROM attach_2_files WHERE post_id = ".$this->id());
	}
		
	private $_attaches = NULL;
	function attaches()
	{
		if($this->_attaches !== NULL)
			return $this->_attaches;
			
		$result = array();
		foreach($this->attach_ids() as $attach_id)
			$result[] = class_load('forum_attach', $attach_id);

		return $this->_attaches = $result;
	}

	function set_attaches($attaches)
	{
		return $this->_attaches = $attaches;
	}

	function search_source() { return $this->source(); }
	
	function num_replies()
	{
		return 0;
		
		$db = &new DataBase('punbb');
		return intval($db->get("SELECT COUNT(*) FROM posts WHERE answer_to = {$this->id}"));
	}

	function visits() { return $this->topic()->num_views(); }

	function class_title() { return ec("Сообщение форума"); }
	
	function answers()
	{
		return objects_array('forum_post', array(
			'where' => array('answer_to=' => intval($this->id())),
			'order' => 'id',
		));
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

	private $warning = false;
	function warning()
	{
		if($this->warning !== false)
			return $this->warning;
	
		if($this->warning_id() == -1)
			return $this->warning = NULL;
			
		if($this->warning_id())
			return $this->warning = object_load('airbase_user_warning', $this->warning_id());
			
		$warn = objects_first('airbase_user_warning', array(
			'warn_class_id=' => $this->class_id(),
			'warn_object_id='=>$this->id(),
			'order' => '-time'));
			
		$this->db()->insert_ignore('posts_cached_fields', array('post_id' => $this->id()));
		$this->set_warning_id($warn ? $warn->id() : -1, true);
		return $this->warning = $warn ? $warn : NULL;
	}

	function cache_children()
	{
		$res = array(
			object_load('forum_topic', $this->topic_id()),
			object_load('airbase_user_topics', $this->owner_id()),
		);
			
		return $res;
	}
}
