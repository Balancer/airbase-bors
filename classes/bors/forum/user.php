<?php

class forum_user extends base_object_db
{
	function storage_engine() { return 'storage_db_mysql_smart'; }

	function __construct($id)
	{
//		echo "user($id)<br />";
		if($id == -1)
		{
			global $me;
			if(empty($me) || !is_object($me))
				$me = &new User();
			$id = $me->get('id');
//			echo "Current user id = $id<br />";
		}
			
		parent::__construct($id);
	}

	function fields()
	{
		return array('punbb' => array('users' => array(
			'id',
			'title' => 'username',
			'group_id',
			'user_title' => 'title',
			'use_avatar',
			'avatar_width',
			'avatar_height',
			'num_posts',
			'signature',
			'signature_html',
			'warnings',
			'reputation',
		)));
	}

	function group() { class_load('forum_group', $this->group_id() ? $this->group_id() : 3); }

	var $_title = NULL;
	function group_title()
	{
		if($this->_title)
			return $this->_title;
			
		if($this->_title = $this->user_title())
			return $this->_title;
				
//		if($this->_title = $this->group()->user_title())
//			return $this->_title;

		$this->_title = $this->rank();

		return $this->_title;
	}

	function rank()
	{
		$db = &new DataBase('punbb');
		return $db->get("SELECT rank FROM ranks WHERE min_posts < ".intval($this->num_posts())." ORDER BY min_posts DESC LIMIT 1");
	}

	function signature_html()
	{
		if(empty($this->stb_signature_html) || !empty($GLOBALS['bors_data']['lcml_cache_disabled']))
		{
			$body = lcml($this->signature(), 
				array(
					'cr_type' => 'save_cr',
					'forum_type' => 'punbb',
					'forum_base_uri' => 'http://balancer.ru/forum',
					'sharp_not_comment' => true,
					'html_disable' => true,
				)
			);

			$this->set_signature_html($body, true);
		}				
				
		return $this->stb_signature_html; 
	}

	function cache_clean_self()
	{
		parent::cache_clean_self();
//			include_once('funcs/filesystem_ext.php');
//			rec_rmdir("/var/www/balancer.ru/htdocs/user/".$this->id());
	}

	function url() { return "http://balancer.ru/user/{$this->id()}/"; }
	function parents() { return array("http://balancer.ru/users/"); }
}
