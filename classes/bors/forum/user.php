<?php

class forum_user extends base_object_db
{
	function main_db() { return 'punbb'; }
	function main_table() { return 'users'; }

	static function id_prepare($id)
	{
		if($id != -1)
			return $id;

		if(!($cookie = @$_COOKIE['cookie_hash']))
			return NULL;

		return objects_first('forum_user', array('user_cookie_hash' => $cookie));
	}

	function __construct($id)
	{
		if($id == -1)
		{
			$id = $this->id_by_cookie();
			debug_hidden_log('__critical', 'user_id is -1 =>'.$id);
		}

		parent::__construct($id);
	}

	function loaded()
	{
		return parent::loaded() && $this->id() > 1;
	}

	function main_table_fields()
	{
		return array(
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
			'last_message_md',
			'mailing_period',
			'last_mailing',
		);
	}

function group_id() { return @$this->data['group_id']; }
function set_group_id($v, $dbup) { return $this->set('group_id', $v, $dbup); }
function user_title() { return @$this->data['user_title']; }
function set_user_title($v, $dbup) { return $this->set('user_title', $v, $dbup); }
function set_use_avatar($v, $dbup) { return $this->set('use_avatar', $v, $dbup); }
function avatar_width() { return @$this->data['avatar_width']; }
function set_avatar_width($v, $dbup) { return $this->set('avatar_width', $v, $dbup); }
function avatar_height() { return @$this->data['avatar_height']; }
function set_avatar_height($v, $dbup) { return $this->set('avatar_height', $v, $dbup); }
function num_posts() { return @$this->data['num_posts']; }
function set_num_posts($v, $dbup) { return $this->set('num_posts', $v, $dbup); }
function signature() { return @$this->data['signature']; }
function set_signature($v, $dbup) { return $this->set('signature', $v, $dbup); }
function set_signature_html($v, $dbup) { return $this->set('signature_html', $v, $dbup); }
function warnings() { return @$this->data['warnings']; }
function set_warnings($v, $dbup) { return $this->set('warnings', $v, $dbup); }
function warnings_total() { return @$this->data['warnings_total']; }
function set_warnings_total($v, $dbup) { return $this->set('warnings_total', $v, $dbup); }
function reputation() { return @$this->data['reputation']; }
function set_reputation($v, $dbup) { return $this->set('reputation', $v, $dbup); }
function pure_reputation() { return @$this->data['pure_reputation']; }
function set_pure_reputation($v, $dbup) { return $this->set('pure_reputation', $v, $dbup); }
function karma() { return @$this->data['karma']; }
function set_karma($v, $dbup) { return $this->set('karma', $v, $dbup); }
function salt() { return @$this->data['salt']; }
function set_salt($v, $dbup) { return $this->set('salt', $v, $dbup); }
function saltp() { return @$this->data['saltp']; }
function set_saltp($v, $dbup) { return $this->set('saltp', $v, $dbup); }
function saltu() { return @$this->data['saltu']; }
function set_saltu($v, $dbup) { return $this->set('saltu', $v, $dbup); }
function last_post_time() { return @$this->data['last_post_time']; }
function set_last_post_time($v, $dbup) { return $this->set('last_post_time', $v, $dbup); }
function www() { return @$this->data['www']; }
function set_www($v, $dbup) { return $this->set('www', $v, $dbup); }
function realname() { return @$this->data['realname']; }
function set_realname($v, $dbup) { return $this->set('realname', $v, $dbup); }
function location() { return @$this->data['location']; }
function set_location($v, $dbup) { return $this->set('location', $v, $dbup); }
function jabber() { return @$this->data['jabber']; }
function set_jabber($v, $dbup) { return $this->set('jabber', $v, $dbup); }
function icq() { return @$this->data['icq']; }
function set_icq($v, $dbup) { return $this->set('icq', $v, $dbup); }
function msn() { return @$this->data['msn']; }
function set_msn($v, $dbup) { return $this->set('msn', $v, $dbup); }
function aim() { return @$this->data['aim']; }
function set_aim($v, $dbup) { return $this->set('aim', $v, $dbup); }
function yahoo() { return @$this->data['yahoo']; }
function set_yahoo($v, $dbup) { return $this->set('yahoo', $v, $dbup); }
function timezone() { return @$this->data['timezone']; }
function set_timezone($v, $dbup) { return $this->set('timezone', $v, $dbup); }
function language() { return @$this->data['language']; }
function set_language($v, $dbup) { return $this->set('language', $v, $dbup); }
function admin_note() { return @$this->data['admin_note']; }
function set_admin_note($v, $dbup) { return $this->set('admin_note', $v, $dbup); }
function email() { return @$this->data['email']; }
function set_email($v, $dbup) { return $this->set('email', $v, $dbup); }
function rep_r() { return @$this->data['rep_r']; }
function set_rep_r($v, $dbup) { return $this->set('rep_r', $v, $dbup); }
function rep_g() { return @$this->data['rep_g']; }
function set_rep_g($v, $dbup) { return $this->set('rep_g', $v, $dbup); }
function rep_b() { return @$this->data['rep_b']; }
function set_rep_b($v, $dbup) { return $this->set('rep_b', $v, $dbup); }
function rep_x() { return @$this->data['rep_x']; }
function set_rep_x($v, $dbup) { return $this->set('rep_x', $v, $dbup); }
function rep_y() { return @$this->data['rep_y']; }
function set_rep_y($v, $dbup) { return $this->set('rep_y', $v, $dbup); }
function last_message_md() { return @$this->data['last_message_md']; }
function set_last_message_md($v, $dbup) { return $this->set('last_message_md', $v, $dbup); }

function use_avatar()
{
	if($this->data['use_avatar'] && !is_numeric($this->data['use_avatar']))
		return $this->data['use_avatar'];
			
	if(preg_match('/^\d+\.\w+/', $this->data['use_avatar']) && $this->data['avatar_width'])
		return $this->data['use_avatar'];

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

	if($img_size)
	{
		$this->set_avatar_width($img_size[0], true);
		$this->set_avatar_height($img_size[1], true);
	}

	return $this->set_use_avatar($user_avatar, true);
}
	
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
		if(empty($this->data['signature_html']) || !empty($GLOBALS['bors_data']['lcml_cache_disabled']))
		{
			$body = lcml(preg_replace("!\n+$!", '', $this->signature()),
				array(
					'cr_type' => 'save_cr',
					'forum_type' => 'punbb',
					'forum_base_uri' => 'http://balancer.ru/forum',
					'sharp_not_comment' => true,
					'html_disable' => 'full',
				)
			);

			$this->set_signature_html($body, true);
		}				
				
		return $this->data['signature_html'];
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

	function is_banned_in($forum_id) { return $this->__havec('is_banned_in') ? $this->__lastc() : $this->__setc(_is_banned_in($forum_id)); }

	private function _is_banned_in($forum_id)
	{
		if($this->warnings() >= 10)
			return true;

		if($ban = forum_ban::ban_by_username($this->title()))
			return $ban;

		if($this->warnings_in($forum_id) >= 5)
			return true;

		return false;
	}

	function warnings_in($forum_id)
	{
		return intval($this->db('punbb')->select('warnings', 'SUM(score)', array(
			'user_id' => $this->id(),
			'posts.posted>' => time()-86400*14,
			'inner_join' => array('forum_post ON forum_post.id = airbase_user_warning.warn_object_id', 'topics ON topics.id = posts.topic_id'),
			'topics.forum_id=' => $forum_id,
		)));
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

	function warnings_rate($period, $type='per_time')
	{
		switch($type)
		{
			case 'per_posts_and_time':
				$total_posts = $this->db('punbb')->select('posts', 'COUNT(*)', array(
					'poster_id' => $this->id(),
					'posted>' => time() - 86400*$period,
				));
				$total_warns = $this->db('punbb')->select('warnings', 'SUM(score)', array(
					'user_id' => $this->id(),
					'time>' => time() - 86400*$period,
				));
				if($total_warns == 0)
					return ec('нет предупреждений');
				if($total_posts == 0)
					return ec('нет сообщений');
				if($total_posts > $total_warns)
					return "1/".round($total_posts/$total_warns, 1);
				else
					return round($total_warns/$total_posts, 1);

			case 'per_time':
			default:
				return $period * 86400 * $this->warnings_total() / ($this->last_post_time() - $this->create_time() + 1);
		}
	}

    function check_password($password, $handle_errors = true)
   	{
		$sha_password = sha1(bors_lower($this->title()) . $password);
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
            echo "<h3><span style=\"text-color: red;\">Ошибка пароля или логина пользователя {$this->title()}! ({$this->id()})</span></h3>Залогиниться, зарегистрироваться или сменить аккаунт можно <a href=\"http://forums.airbase.ru/\">форуме Авиабазы</a>.<br><span style=\"font-size: xx-small;\">Внимание! Вместо старой системы регистрации теперь будет использоваться новая, объединённая с регистрацией на форумах!";
   	        die();
       	}
		
		return true;
    }

	function cookie_hash()
	{
		return sha1(bors_lower($this->salt()) . $this->saltp());
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

	function cookie_hash_set($expired = -1)
	{
		if($expired == -1)
			$expired = time()+86400*365;

		foreach(array(
			'user_id' => $this->id(), 
			'cookie_hash' => $this->saltu(), 
			'is_admin' => $this->is_admin()
		) as $k => $v)
		{
			SetCookie($k, $v, $expired, "/", '.'.$_SERVER['HTTP_HOST']);
			SetCookie($k, $v, $expired, "/", $_SERVER['HTTP_HOST']);
			SetCookie($k, $v, $expired, "/");
		}
	}

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

		bors_exit(">$all_domains");
*/

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

	static function do_logout()
	{
		foreach(array('user_id', 'cookie_hash', 'is_admin') as $k)
		{
			SetCookie($k, NULL, 0, "/");
			SetCookie($k, NULL, 0, "/", $_SERVER['HTTP_HOST']);
			SetCookie($k, NULL, 0, "/", '.'.$_SERVER['HTTP_HOST']);
		}
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

	function is_admin() { return in_array($this->id(), array(3310, 10000)); }
	function is_moderator() { return $this->group()->is_moderator() || $this->is_admin(); }
	function is_coordinator() { return $this->group()->is_coordinator() || $this->is_moderator(); }
	function is_watcher() { return in_array($this->id(), array(46099)); }

	function can_edit($object)
	{
		return $this->is_admin();
	}

	function reputation_weight()
	{
		$r = $this->reputation();
		$w = 0.5 + atan($r*abs($r)/200)/pi();
		return $w*$w;
	}

	function messages_daily_limit() // -1, если без ограничений.
	{
		$w = $this->warnings();

		if($w <= 0)
			return -1;

		if($w >= 10)
			return 0;

//		$offset = max(2, round(15 - (time() - 1247947991)/86400));

		$limit = round(max(0, 300/($w*$w) * $this->reputation_weight() + 2 ));
		if($limit > 40)
			return -1;

		return $limit;
	}

	function today_posted()
	{
		return $this->__havec('today_posted') ? $this->__lastc() : $this->__setc(objects_count('forum_post', array(
			'owner_id' => $this->id(), 
			'create_time>' => time()-86400,
		)));
	}

	function tomonth_posted()
	{
		return $this->__havec('tomonth_posted') ? $this->__lastc() : $this->__setc(objects_count('forum_post', array(
			'owner_id' => $this->id(), 
			'create_time>' => time()-86400*31,
		)));
	}

	function today_posted_in_forum($forum_id)
	{
		return $this->__havec('today_posted') ? $this->__lastc() : $this->__setc(objects_count('forum_post', array(
			'owner_id' => $this->id(), 
			'create_time>' => time()-86400,
			'inner_join' => 'forum_topic ON (forum_post.topic_id = forum_topic.id)',
			'forum_topic.forum_id=' => $forum_id,
		))); 
	}

	function next_can_post($limit, $forum_id)
	{
		if($this->attr('next_can_post'))
			return $this->attr('next_can_post');

		$first_in_day = objects_array('forum_post', array(
			'owner_id' => $this->id(), 
			'create_time>' => time()-86400*2,
			'order' => '-create_time',
			'limit' => $limit,
			'inner_join' => 'forum_topic ON (forum_post.topic_id = forum_topic.id)',
			'forum_topic.forum_id=' => $forum_id,
		));

		$first_in_day = $first_in_day[count($first_in_day)-1];

		return $this->set_attr('next_can_post', $first_in_day ? $first_in_day->create_time()+86400 : NULL);
	}
}
