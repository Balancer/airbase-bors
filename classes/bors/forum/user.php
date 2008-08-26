<?php

class forum_user extends base_object_db
{
	function storage_engine() { return 'storage_db_mysql_smart'; }

	function __construct($id)
	{
		if($id == -1)
			$id = $this->check_cookie();
		
		parent::__construct($id);
	}

	function loaded()
	{
		return parent::loaded() && $this->id() > 1;
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
			'warnings_total',
			'reputation',
			'pure_reputation',
			'create_time' => 'registered',
			'last_post_time' => 'last_post',
		)));
	}

	function set_title($value, $dbupd) { $this->fset('title', $value, $dbupd); }
	function title() { return $this->stb_title; }

	function set_group_id($value, $dbupd) { $this->fset('group_id', $value, $dbupd); }
	function group_id() { return $this->stb_group_id; }

	function set_user_title($value, $dbupd) { $this->fset('user_title', $value, $dbupd); }
	function user_title() { return $this->stb_user_title; }
	
	function set_use_avatar($value, $dbupd) { $this->fset('use_avatar', $value, $dbupd); }
	function use_avatar() { return $this->stb_use_avatar; }
	
	function set_avatar_width($value, $dbupd) { $this->fset('avatar_width', $value, $dbupd); }
	function avatar_width() { return $this->stb_avatar_width; }

	function set_avatar_height($value, $dbupd) { $this->fset('avatar_height', $value, $dbupd); }
	function avatar_height() { return $this->stb_avatar_height; }

	function set_num_posts($value, $dbupd) { $this->fset('num_posts', $value, $dbupd); }
	function num_posts() { return $this->stb_num_posts; }
	
	function set_signature($value, $dbupd) { $this->fset('signature', $value, $dbupd); }
	function signature() { return $this->stb_signature; }
	
	function set_warnings($value, $dbupd) { $this->fset('warnings', $value, $dbupd); }
	function warnings() { return $this->stb_warnings; }

	function set_warnings_total($value, $dbupd) { $this->fset('warnings_total', $value, $dbupd); }
	function warnings_total() { return $this->stb_warnings_total; }
	
	function set_reputation($value, $dbupd) { $this->fset('reputation', $value, $dbupd); }
	function reputation() { return $this->stb_reputation; }

	function set_create_time($value, $dbupd) { $this->fset('create_time', $value, $dbupd); }
	function create_time() { return $this->stb_create_time; }

	function set_last_post_time($value, $dbupd) { $this->fset('last_post_time', $value, $dbupd); }
	function last_post_time() { return $this->stb_last_post_time; }

	function group() { return class_load('forum_group', $this->group_id() ? $this->group_id() : 3); }

	var $_title = NULL;
	function group_title()
	{
		if($this->_title)
			return $this->_title;
			
		if($this->_title = $this->user_title())
			return $this->_title;
				
		if($this->_title = $this->group()->user_title())
			return $this->_title;

		$this->_title = $this->rank();

		return $this->_title;
	}

	private $__rank = NULL;
	function rank()
	{
		if($this->__rank !== NULL)
			return $this->__rank;
		
		global $bors_forum_user_ranks;
		if($bors_forum_user_ranks === NULL)
			$bors_forum_user_ranks = $this->db('punbb')->select_array('ranks', 'rank, min_posts', array('order' => '-min_posts'));

		foreach($bors_forum_user_ranks as $x)
			if($this->num_posts() >= $x['min_posts'])
				return $this->__rank = $x['rank'];
		
		return $this->__rank = 'Unknown';
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
//			include_once('inc/filesystem.php');
//			rec_rmdir("/var/www/balancer.ru/htdocs/user/".$this->id());
	}

	function user_dir()
	{
		return "/var/www/balancer.ru/htdocs/user/".$this->id();
	}

	function cache_children()
	{
		$res = array(
			object_load('airbase_user_warnings', $this->id()),
		);
			
		return $res;
	}


	function url() { return "http://balancer.ru/user/{$this->id()}/"; }
	function parents() { return array("http://balancer.ru/users/"); }

	private $is_banned;
	function is_banned()
	{
		if($this->is_banned !== NULL)
			return $this->is_banned;
	
		if($ban = forum_ban::ban_by_username($this->title()))
			return $this->is_banned = $ban;
			
		return $this->is_banned = false;
	}

    function check_cookie()
	{
		if(!$user_hash_password = @$_COOKIE['cookie_hash'])
			return 0;
			
		return intval($this->db('punbb')->select('users', 'id', array('user_cookie_hash=' => $user_hash_password)));
	}

	function warnings_rate($period)
	{
		return $period * 86400 * $this->warnings_total() / ($this->last_post_time() - $this->create_time() + 1);
	}
}
