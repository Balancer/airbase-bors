<?php

class forum_user extends base_object_db
{
	function storage_engine() { return 'storage_db_mysql_smart'; }
	function main_db_storage() { return 'punbb'; }

	function __construct($id)
	{
		if($id == -1)
			$id = $this->id_by_cookie();
		
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
			'karma',
			'salt',
			'saltp' => 'password',
			'saltu' => 'user_cookie_hash',
			'create_time' => 'registered',
			'last_post_time' => 'last_post',
			'www' => 'url',
			'realname',
			'location',
			'jabber',
			'icq',
			'msn',
			'aim',
			'yahoo',
			'timezone',
			'language',
			'admin_note',
			'email',
			'rep_r', 'rep_g', 'rep_b',
			'rep_x', 'rep_y',
		)));
	}

	function set_title($value, $dbupd) { return $this->fset('title', $value, $dbupd); }
	function title() { return $this->stb_title; }

	function set_group_id($value, $dbupd) { return $this->fset('group_id', $value, $dbupd); }
	function group_id() { return $this->stb_group_id; }

	function set_user_title($value, $dbupd) { return $this->fset('user_title', $value, $dbupd); }
	function user_title() { return $this->stb_user_title; }
	
	function set_use_avatar($value, $dbupd) { return $this->fset('use_avatar', $value, $dbupd); }

	function use_avatar()
	{
		if(!$this->stb_use_avatar)
			return $this->stb_use_avatar;
			
		if(preg_match('/^\d+\.\w+/', $this->stb_use_avatar))
			return $this->stb_use_avatar;

		$avatars_dir = '/var/www/balancer.ru/htdocs/forum/punbb/img/avatars';
		$id = $this->id();
		
		if($img_size = @getimagesize("$avatars_dir/$id.gif"))
			$user_avatar = "$id.gif";
		elseif($img_size = @getimagesize("$avatars_dir/$id.png"))
			$user_avatar = "$id.png";
		elseif($img_size = @getimagesize("$avatars_dir/$id.jpg"))
			$user_avatar = "$id.jpg";
		else
			$user_avatar = "";

		return $this->set_use_avatar($user_avatar, true);
	}
	
	function set_avatar_width($value, $dbupd) { return $this->fset('avatar_width', $value, $dbupd); }
	function avatar_width() { return $this->stb_avatar_width; }

	function set_avatar_height($value, $dbupd) { return $this->fset('avatar_height', $value, $dbupd); }
	function avatar_height() { return $this->stb_avatar_height; }

	function set_num_posts($value, $dbupd) { return $this->fset('num_posts', $value, $dbupd); }
	function num_posts() { return $this->stb_num_posts; }
	
	function set_signature($value, $dbupd) { return $this->fset('signature', $value, $dbupd); }
	function signature() { return $this->stb_signature; }
	
	function set_warnings($value, $dbupd) { return $this->fset('warnings', $value, $dbupd); }
	function warnings() { return $this->stb_warnings; }

	function set_warnings_total($value, $dbupd) { return $this->fset('warnings_total', $value, $dbupd); }
	function warnings_total() { return $this->stb_warnings_total; }
	
	function set_reputation($value, $dbupd) { return $this->fset('reputation', $value, $dbupd); }
	function reputation() { return $this->stb_reputation; }

	function set_create_time($value, $dbupd) { return $this->fset('create_time', $value, $dbupd); }
	function create_time() { return $this->stb_create_time; }

	function set_last_post_time($value, $dbupd) { return $this->fset('last_post_time', $value, $dbupd); }
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
		{
			$db = new driver_mysql('punbb');
			$bors_forum_user_ranks = $db->select_array('ranks', 'rank, min_posts', array('order' => '-min_posts'));
			$db->close();
		}
		
		foreach($bors_forum_user_ranks as $x)
			if($this->num_posts() >= $x['min_posts'])
				return $this->__rank = $x['rank'];
		
		return $this->__rank = 'Unknown';
	}

	function signature_html()
	{
		if(empty($this->stb_signature_html) || !empty($GLOBALS['bors_data']['lcml_cache_disabled']))
		{
			$body = lcml(preg_replace("!\n+$!", '', $this->signature()),
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

		if($this->warnings() >= 10)
			return $this->is_banned = true;

		if($ban = forum_ban::ban_by_username($this->title()))
			return $this->is_banned = $ban;
			
		return $this->is_banned = false;
	}

	static function id_by_cookie($user_hash_password = NULL)
	{
		if(is_null($user_hash_password))
			$user_hash_password = @$_COOKIE['cookie_hash'];

		if(!$user_hash_password)
			return 0;
			
		$db = new driver_mysql('punbb');
		$result = intval($db->select('users', 'id', array('user_cookie_hash=' => $user_hash_password)));
		$db->close();
		return $result;
	}

	function warnings_rate($period)
	{
		return $period * 86400 * $this->warnings_total() / ($this->last_post_time() - $this->create_time() + 1);
	}

    function check_password($password, $handle_errors = true)
   	{
		$sha_password = sha1(strtolower($this->title()) . $password);
		$user_sha_password = $this->saltp();
	
		if(!$handle_errors)
			return ($password != '') && ($user_sha_password == $sha_password);

       	if(!$password)
        {
   	        $nick = user_data('nick');
       	    echo "<h3><span style=\"text-color: red;\">Пароль пользователя $nick ($member_id) не может быть пустой!</span></h3>Залогиниться, зарегистрироваться или сменить аккаунт можно <a href=\"http://forums.airbase.ru/\">форуме Авиабазы</a>.<br><span style=\"font-size: xx-small;\">Внимание! Вместо старой системы регистрации теперь будет использоваться новая, объединённая с регистрацией на форумах!";
            die();
        }

   	    if($sha_password != $user_sha_password)
       	{
           	$nick=user_data('nick');
            echo "<h3><span style=\"text-color: red;\">Ошибка пароля или логина пользователя $nick! ($member_id)</span></h3>Залогиниться, зарегистрироваться или сменить аккаунт можно <a href=\"http://forums.airbase.ru/\">форуме Авиабазы</a>.<br><span style=\"font-size: xx-small;\">Внимание! Вместо старой системы регистрации теперь будет использоваться новая, объединённая с регистрацией на форумах!";
   	        die();
       	}
		
		return true;
    }

	function cookie_hash()
	{
		return sha1(strtolower($this->salt()) . $this->saltp());
	}

	function cookie_hash_update($expired = -1, $all_domains = true)
	{
		if($expired == -1)
			$expired = time()+86400*365;

		$salt = sha1(rand());
		$this->set_salt($salt, true);
		$this->set_saltu($this->cookie_hash(), true);
		$this->store();

		$this->cookie_hash_set($expired, $all_domains);
		return $this->saltu();
	}

	function cookie_hash_set($expired = -1, $all_domains = true)
	{
		if($expired == -1)
			$expired = time()+86400*365;

		SetCookie("user_id", $this->id(), $expired, "/", '.'.$_SERVER['HTTP_HOST']);
		SetCookie("cookie_hash", $this->saltu(), $expired, "/", '.'.$_SERVER['HTTP_HOST']);
			
		$_COOKIE['user_id'] = $this->id();
		$_COOKIE['cookie_hash'] = $this->saltu();

/*		if($all_domains)
		{
			$ch = curl_init($url);
			foreach(bors_vhosts() as $host)
			{
				echo "Set $host<br/>";
//				file_get_contents("http://{$host}/user/cookie-hash-update.bas?".$this->saltu());
				curl_setopt_array($ch, array(CURLOPT_TIMEOUT => 1));
				curl_exec($ch);
				$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
			}
		}

		bors_exit(">$all_domains");*/
	}

	static function do_login($user, $password, $handle_error = true)
   	{
		$check_user = objects_first('forum_user', array('username' => $user));
	
		if(!$check_user)
			return ec("Неизвестный пользователь '").$user."'";
			
		$test = $check_user->check_password($password, $handle_error);
		if(!$test)
			return ec("Ошибка пароля пользователя '").$user."'";

		if($check_user->saltu())
			$check_user->cookie_hash_set();
		else
			$check_user->cookie_hash_update();

//		SetCookie("user_id", $check_user->id(), time() + 86400*365, "/", $_SERVER['HTTP_HOST']);
//		SetCookie("cookie_hash", $check_user->saltu(), time() + 86400*365, "/", $_SERVER['HTTP_HOST']);
			
		return $check_user;
	}

    function do_logout()
	{
//		print_d($_COOKIE);
		SetCookie('cookie_hash', '', 0, '/', $_SERVER['HTTP_HOST']);
		SetCookie('user_id', '', 0, '/');
		SetCookie('do_logout', 1, time()+3, '/', $_SERVER['HTTP_HOST']);
		unset($_COOKIE['user_id']);
		$_COOKIE['do_logout'] = 1;
		unset($_COOKIE['cookie_hash']);
//		print_d($_COOKIE);
//		exit();
	}
	
	function reputation_titled_url() { return "<a href=\"http://balancer.ru/user/{$this->id()}/reputation/\">{$this->title()}</a>"; }

	function weight()
	{
		$_group_weights = array(
				1 => 8, // admin
				2 => 6, // moder
				3 => 0, // guest
				5 => 4, // coordin
				6 => 2, // старожилы
				21 => 4, // координатор-литератор
		);

		$weight = @$this->_group_weights[$group];
		if(!$weight)
			$weight = 1;

		if($this->id() == 10000) // Balancer ;)
			$weight = 10;
					
		if($this->num_posts() < 50 || $this->create_time() > time() - 86400*2)
			$weight = 0;
	}

	function set_last_visit_time() { }
}
