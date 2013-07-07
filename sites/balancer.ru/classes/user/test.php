<?php

class user_test extends user_main
{
	function _class_file() { return __FILE__; }

	function title() { return "Test"; }
	function static_cache() { return 0; }

	function __toString() { return get_class($this).'://'.$this->id(); }

	function db_name() { return 'AB_FORUMS'; }
	function table_name() { return 'users'; }

	function exec()
	{
//		include_once('engines/search.php');
//		return print_r(bors_search_get_word_id_array(array("тест", "тест", 'су')), true);
	
		$_GET['qtime'] = 0.1;
		$_GET['log_level'] = 4;
		loglevel(4);
		// 54897 -> 1163134

		$post = object_load('forum_post', 1339072);
		$post->move_tree_to_topic(54897);
//		object_load('balancer_board_topic', 54897)->recalculate();
		// 31143
		// 54897
		return "Moved";
	
//		$salt = new user_salt(0);
//		include_once('engines/users.php');
//		return users_client_describe();
//		return $salt;
	}
	
	function post()
	{
		return object_load('forum_post', 1338600);
	}
}
