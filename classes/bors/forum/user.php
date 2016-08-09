<?php

if(!defined('PUN_UNVERIFIED'))
{
	define('PUN_UNVERIFIED', 3);
	define('PUN_ADMIN', 1);
	define('PUN_MOD', 2);
	define('PUN_GUEST', 3);
	define('PUN_MEMBER', 4);
}

class forum_user extends balancer_board_object_db
{
	function table_name() { return 'users'; }

	static function id_prepare($id)
	{
		if($id != -1)
			return $id;

		if(!($cookie = @$_COOKIE['cookie_hash']))
			return NULL;

		return bors_find_first('balancer_board_user', array('user_cookie_hash' => $cookie));
	}

	function __construct($id)
	{
		if($id == -1)
		{
			$id = $this->id_by_cookie();
			bors_debug::syslog('__critical', 'user_id is -1 =>'.$id);
		}

		parent::__construct($id);
	}

	function is_loaded()
	{
		return parent::is_loaded() && $this->id() > 1;
	}

	function table_fields()
	{
		return array(
			'id',
			'login' => 'username',
			'username',
			'user_nick',
			'group_id',
			'rpg_level',
			'user_title' => 'title',
			'group_title_raw' => 'group_title',
			'level',
			'use_avatar',
			'avatar_width',
			'avatar_height',
			'num_posts',
			'signature',
			'signature_html',
			'warnings',
			'warnings_total',
			'reputation',
			'per_category_reputations' => 'per_forum_reputations',
			'pure_reputation',
			'karma', 'karma_rate',
			'password_hash_old' => 'password',
			'password_hash_new',
			'password_salt_new',
			'user_cookie_hash',
			'cookie_salt' => 'salt',
			'create_time' => 'registered',
			'registration_ip',
			'last_post_time' => 'last_post',
			'previous_session_end' => 'last_visit',
			'last_visit_time' => 'last_real_visit',
			'www' => 'url',
			'realname',
			'login_min',
			'nick_min',
			'realname_min',
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
			'has_invalid_email',
			'email_setting',
			'is_dead',
			'is_destructive',
			'is_deleted',
			'delete_notice',
			'invalid_mail_message',
			'rep_r', 'rep_g', 'rep_b',
			'rep_x', 'rep_y',
			'last_message_md',
			'mailing_period',
			'last_mailing',
			'xmpp_notify_enabled', // Период нотификации. 0 == запрещено, -1 - мгновенно, иначе - период времени.
			'xmpp_notify_new', // Подписка на новые темы
			'xmpp_notify_score', // Подписка на изменения оценок
			'xmpp_notify_reputation', // Подписка на изменения репутации
			'xmpp_notify_best', // Подписка на лучшие сообщения
			'utmx',

			'joke_id', 'joke_ban',

			'activate_string',	// Хэш нового пароля с текущей солью при смене пароля
			'activate_key',		// Ключ нового пароля при его смене
			'money',
		);
	}

function group_id() { return @$this->data['group_id']; }
function set_group_id($v, $dbup=true) { return $this->set('group_id', $v, $dbup); }
function user_title() { return @$this->data['user_title']; }
function set_user_title($v, $dbup=true) { return $this->set('user_title', $v, $dbup); }
function set_use_avatar($v, $dbup=true) { return $this->set('use_avatar', $v, $dbup); }
function avatar_width($size = false) { return $size ? intval($size*@$this->data['avatar_width']/100) : @$this->data['avatar_width']; }
function set_avatar_width($v, $dbup=true) { return $this->set('avatar_width', $v, $dbup); }
function avatar_height($size = false) { return $size ? intval($size*@$this->data['avatar_height']/100) : @$this->data['avatar_height']; }
function set_avatar_height($v, $dbup=true) { return $this->set('avatar_height', $v, $dbup); }
function num_posts() { return @$this->data['num_posts']; }
function set_num_posts($v, $dbup=true) { return $this->set('num_posts', $v, $dbup); }
function signature() { return @$this->data['signature']; }
function set_signature($v, $dbup=true) { return $this->set('signature', $v, $dbup); }
function set_signature_html($v, $dbup = true) { return $this->set('signature_html', $v, $dbup); }
function warnings() { return @$this->data['warnings']; }
function set_warnings($v, $dbup=true) { return $this->set('warnings', $v, $dbup); }
function warnings_total() { return @$this->data['warnings_total']; }
function set_warnings_total($v, $dbup=true) { return $this->set('warnings_total', $v, $dbup); }
function reputation() { return @$this->data['reputation']; }
function set_reputation($v, $dbup=true) { return $this->set('reputation', $v, $dbup); }
function pure_reputation() { return @$this->data['pure_reputation']; }
function set_pure_reputation($v, $dbup=true) { return $this->set('pure_reputation', $v, $dbup); }
function karma() { return @$this->data['karma']; }
function set_karma($v, $dbup=true) { return $this->set('karma', $v, $dbup); }
function cookie_salt() { return @$this->data['cookie_salt']; }
function set_cookie_salt($v, $dbup=true) { return $this->set('cookie_salt', $v, $dbup); }
function password_hash_old() { return @$this->data['password_hash_old']; }
function set_password_hash_old($v, $dbup=true) { return $this->set('password_hash_old', $v, $dbup); }
function user_cookie_hash() { return @$this->data['user_cookie_hash']; }
function set_user_cookie_hash($v, $dbup=true) { return $this->set('user_cookie_hash', $v, $dbup); }
function last_post_time() { return @$this->data['last_post_time']; }
function set_last_post_time($v, $dbup=true) { return $this->set('last_post_time', $v, $dbup); }
function www() { return @$this->data['www']; }
function set_www($v, $dbup=true) { return $this->set('www', $v, $dbup); }
function realname() { return @$this->data['realname']; }
function set_realname($v, $dbup=true) { return $this->set('realname', $v, $dbup); }
function location() { return @$this->data['location']; }
function set_location($v, $dbup=true) { return $this->set('location', $v, $dbup); }
function jabber() { return @$this->data['jabber']; }
function set_jabber($v, $dbup=true) { return $this->set('jabber', $v, $dbup); }
function icq() { return @$this->data['icq']; }
function set_icq($v, $dbup=true) { return $this->set('icq', $v, $dbup); }
function msn() { return @$this->data['msn']; }
function set_msn($v, $dbup=true) { return $this->set('msn', $v, $dbup); }
function aim() { return @$this->data['aim']; }
function set_aim($v, $dbup=true) { return $this->set('aim', $v, $dbup); }
function yahoo() { return @$this->data['yahoo']; }
function set_yahoo($v, $dbup=true) { return $this->set('yahoo', $v, $dbup); }
function timezone() { return @$this->data['timezone']; }
function set_timezone($v, $dbup=true) { return $this->set('timezone', $v, $dbup); }
function language() { return @$this->data['language']; }
function set_language($v, $dbup=true) { return $this->set('language', $v, $dbup); }
function admin_note() { return @$this->data['admin_note']; }
function set_admin_note($v, $dbup=true) { return $this->set('admin_note', $v, $dbup); }
function email() { return @$this->data['email']; }
function set_email($v, $dbup=true) { return $this->set('email', $v, $dbup); }
function rep_r() { return @$this->data['rep_r']; }
function set_rep_r($v, $dbup=true) { return $this->set('rep_r', $v, $dbup); }
function rep_g() { return @$this->data['rep_g']; }
function set_rep_g($v, $dbup=true) { return $this->set('rep_g', $v, $dbup); }
function rep_b() { return @$this->data['rep_b']; }
function set_rep_b($v, $dbup=true) { return $this->set('rep_b', $v, $dbup); }
function rep_x() { return @$this->data['rep_x']; }
function set_rep_x($v, $dbup=true) { return $this->set('rep_x', $v, $dbup); }
function rep_y() { return @$this->data['rep_y']; }
function set_rep_y($v, $dbup=true) { return $this->set('rep_y', $v, $dbup); }
function last_message_md() { return @$this->data['last_message_md']; }
function set_last_message_md($v, $dbup=true) { return $this->set('last_message_md', $v, $dbup); }

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

	$this->set_use_avatar($user_avatar, true);
	return $user_avatar;
}

/*
function avatar_thumb($geo)
{
	$ava = $this->use_avatar();
	if(!$ava)
		return NULL;

	return preg_replace('!^(.+/)([^/]+)$!', '/cache$1');
}
*/

	function title()
	{
		$title = trim($this->user_nick());
		if(!preg_match('/[a-zA-Z0-9а-яёА-ЯЁ]+/ui', $title))
			$title = $this->set_user_nick($this->username(), true);

		return $title;
		return wordwrap($title, 10, ' ', true);
	}

	function group() { return bors_load('balancer_board_group', $this->group_id() ? $this->group_id() : 3); }

	var $_title = NULL;
	function group_title()
	{
		if($this->_title)
			return $this->_title;

		if($this->_title = $this->user_title())
			return $this->_title;

//		if($this->_title = $this->group_title_raw())
//			return $this->_title;

//		if(!$this->_title)
		//TODO: хардкод. 4 == «участники»
		if($this->group_id() != 4 && $this->group()->user_title())
			return $this->_title = $this->group()->user_title();

//		if(!$this->_title)
		return $this->_title = bors_lower($this->rank());

//		$this->set('group_title_raw', $this->_title);
//		return $this->_title;
	}

	private $__rank = NULL;
	function rank()
	{
		if($this->__rank !== NULL)
			return $this->__rank;

		global $bors_forum_user_ranks;
		if($bors_forum_user_ranks === NULL)
		{
			$db = new driver_mysql(config('punbb.database'));
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
			$savet = config('lcml_tags_enabled');
			$savef = config('lcml_functions_enabled');
			config_set('lcml_tags_enabled', explode(' ', 'b black blue br color green i red u url'));
			config_set('lcml_functions_enabled', explode(' ', 'lcml_text lcml_color lcml_classic_bb_url'));
			$body = lcml(preg_replace("!\n+$!", '', $this->signature()),
				array(
					'cr_type' => 'save_cr',
					'forum_type' => 'punbb',
					'forum_base_uri' => 'http://www.balancer.ru/forum',
					'sharp_not_comment' => true,
					'html_disable' => 'full',
				)
			);
			config_set('lcml_tags_enabled', $savet);
			config_set('lcml_functions_enabled', $savef);

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
			bors_load('airbase_user_warnings', $this->id()),
		);

		return $res;
	}


	function url() { return "http://www.balancer.ru/user/{$this->id()}/"; }
	function parents() { return array("http://www.balancer.ru/users/"); }

	function is_banned()
	{
		if($this->is_deleted())
			return true;

		if(array_key_exists('is_banned', $this->attr))
			return $this->attr['is_banned'];

		$ch = new bors_cache_fast;
		if($ch->check('user[4].is_banned', $this->id()))
			return $this->attr['is_banned'] = $ch->last();

		if($this->warnings() >= 10)
			return $ch->set($this->attr['is_banned'] = true, 600);

//		if($ban = balancer_board_ban::find_by_name($this->title()))
//			return $ch->set($this->attr['is_banned'] = $ban, 600);

		if($ban = balancer_board_ban::find_by_name($this->login()))
			return $ch->set($this->attr['is_banned'] = $ban, 600);

		return $ch->set($this->attr['is_banned'] = false, 600);
	}

	function is_admin_banned()
	{
		if(array_key_exists('is_banned', $this->attr))
			return $this->attr['is_banned'];

		$ch = new bors_cache_fast;
		if($ch->check('user.is_admin_banned.2', $this->id()))
			return $this->attr['is_admin_banned'] = $ch->last();

//		if($ban = balancer_board_ban::find_by_name($this->title()))
//			return $ch->set($this->attr['is_admin_banned'] = $ban, 600);

		if($ban = balancer_board_ban::find_by_name($this->login()))
			return $ch->set($this->attr['is_admin_banned'] = $ban, 600);

		return $ch->set($this->attr['is_admin_banned'] = false, 600);
	}

	function is_banned_in($forum_id) { return $this->__havec('is_banned_in') ? $this->__lastc() : $this->__setc(_is_banned_in($forum_id)); }

	private function _is_banned_in($forum_id)
	{
		if($this->warnings() >= 10)
			return true;

//		if($ban = balancer_board_ban::find_by_name($this->title()))
//			return $ban;

		if($ban = balancer_board_ban::find_by_name($this->login()))
			return $ban;

		if($this->warnings_in($forum_id) >= 5)
			return true;

		return false;
	}

	function warnings_in($forum_id)
	{
		return intval($this->db(config('punbb.database'))->select('warnings', 'SUM(warnings.score)', array(
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

		$db = new driver_mysql(config('punbb.database'));
		$result = intval($db->select('users', 'id', array('user_cookie_hash=' => $user_hash_password)));
		$db->close();
		return $result;
	}

	function warnings_rate($period, $type='per_time')
	{
		switch($type)
		{
			case 'per_posts_and_time':
				$total_posts = $this->db(config('punbb.database'))->select('posts', 'COUNT(*)', array(
					'poster_id' => $this->id(),
					'posted>' => time() - 86400*$period,
				));
				$total_warns = $this->db(config('punbb.database'))->select('warnings', 'SUM(score)', array(
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

	function check_password($password, $handle_errors = true, $test_new_engine = true)
   	{
		if($test_new_engine && ($password_hash = $this->password_hash_new()))
		{
	   		// Новая проверка, на рандомной соли
			$test_hash = sha1($password.$this->password_salt_new());
		}
		else
		{
			// Старая проверка
			$test_hash = sha1(bors_lower($this->login()) . $password);
			$password_hash = $this->password_hash_old();
		}

		if(!$handle_errors)
			return ($password != '') && ($test_hash == $password_hash);

	   	if(!$password)
		{
   			$nick = user_data('nick');
			echo "<h3><span style=\"text-color: red;\">Пароль пользователя $nick ($member_id) не может быть пустой!</span></h3>Залогиниться, зарегистрироваться или сменить аккаунт можно <a href=\"http://forums.airbase.ru/\">форуме Авиабазы</a>.<br><span style=\"font-size: xx-small;\">Внимание! Вместо старой системы регистрации теперь будет использоваться новая, объединённая с регистрацией на форумах!";
			die();
		}

   		if($password_hash != $test_hash)
	   	{
			echo "<h3><span style=\"text-color: red;\">Ошибка пароля или логина пользователя {$this->title()}! ({$this->id()})</span></h3>Залогиниться, зарегистрироваться или сменить аккаунт можно <a href=\"http://forums.airbase.ru/\">форуме Авиабазы</a>.<br><span style=\"font-size: xx-small;\">Внимание! Вместо старой системы регистрации теперь будет использоваться новая, объединённая с регистрацией на форумах!";
   			die();
	   	}

		return true;
	}

	function cookie_hash()
	{
		if($password_hash = $this->password_hash_new())
			return sha1($this->cookie_salt() . $this->password_hash_new());

		return sha1(bors_lower($this->cookie_salt()) . $this->password_hash_old());
	}

	function cookie_hash_update($expired = -1, $all_domains = true)
	{
		if($expired == -1)
			$expired = time()+86400*365;

		$cookie_salt = sha1(rand());
		$this->set_cookie_salt($cookie_salt, true);
		$this->set_user_cookie_hash($this->cookie_hash(), true);
		$this->store();

		$this->cookie_hash_set($expired, $all_domains);
		return $this->user_cookie_hash();
	}

	function cookie_hash_set($expired = -1)
	{
		if($expired == -1)
			$expired = time()+86400*30;

		if(!$this->user_cookie_hash())
			return $this->cookie_hash_update();

		$domains = config('balancer_board_domains');
		$next_domain = $domains[0];

//		$referer = isset($_GET['redirect_url']) ? $_GET['redirect_url'] : @$_SERVER['HTTP_REFERER'];
		$redirect = config('redirect_to', bors()->request()->url()); // isset($_GET['redirect_url']) ? $_GET['redirect_url'] : @$_SERVER['HTTP_REFERER'];

//		var_dump($next_domain, $domains);
		$haction = bal_user_haction::add($this->id(), 'bal_users_helper', 'haction_domain_login', 120, [
			'domain' => $next_domain,
			'redirect' => $redirect,
			'cookie_hash' => $this->user_cookie_hash(),
			'is_admin' => $this->is_admin(),
			'expired' => $expired,
		]);

		config_set('__login_redir', true);
		return go($haction->url_ex($next_domain), false, 0, true);
	}

	function cookies_set($ttl=3600, $fake_login = false)
	{
		$expiried = time() + $ttl;

		foreach(array('user_id' => $this->id(), 'cookie_hash' => $this->user_cookie_hash(), 'isa' => $this->is_admin()) as $k => $v)
			SetCookie($k, $v, $expired, "/", '.'.$_SERVER['HTTP_HOST']);
	}

	function password_hashing($string_password)
	{
		return sha1($string_password.$this->password_salt_new());
	}

	function change_password($new_password)
   	{
		$this->set_password_salt_new(md5(rand()));
		$this->set_password_hash_new($this->password_hashing($new_password));
		$this->store();
	}

	static function do_login($user, $password, $handle_error = true, $handle_cookie_set = true)
   	{
//		config_set('redirect_by_html', true);
		$check_user = bors_find_first('balancer_board_user', array('login' => $user));

		if(!$check_user && strpos($user, '@') !== false)
			$check_user = bors_find_first('balancer_board_user', array('email' => $user));

		if(!$check_user)
			return ec("Неизвестный пользователь '").$user."'";

		// Если пользователь не активровал новый механизм хранения пароля
		if(!$check_user->password_hash_new())
		{
			if($check_user->check_password($password, false, false))
			{
				// Если старый пароль был верный, то обновляем механизм
				$check_user->set_password_salt_new(md5(rand()));
				$check_user->set_password_hash_new(sha1($password.$check_user->password_salt_new()));
			}
		}

		$test = $check_user->check_password($password, $handle_error);

		if(!$test)
			return ec("Ошибка пароля пользователя '").$user."'";

		if($check_user->group_id() == PUN_UNVERIFIED)
		{
			$check_user->set_group_id(PUN_MEMBER, true);
			$check_user->store();
		}

//		livestreet_native_user::bb_copy($check_user, $password, true);
//		if($check_user->id()==10000) { var_dump($user); exit('debug: введите ещё раз'); }

		@file_get_contents("http://ls.balancer.ru/bors-api/user-new.php?"
			."login=".urlencode($check_user->login())
			."&id=".$check_user->id()
			."&mail=".urlencode($check_user->email())
			."&password_md=".md5($password)
			."&date=".$check_user->create_time()
			."&ip=".$check_user->registration_ip()
			."&loc=".urlencode($check_user->location())
		);

		@file_get_contents("http://photos.wrk.ru/bors-api/user-new.php?"
			."login=".urlencode($check_user->login())
			."&id=".$check_user->id()
			."&mail=".urlencode($check_user->email())
			."&password=".$password
			."&date=".$check_user->create_time()
		);

/*
		@file_get_contents("http://ls.balancer.ru/bors-api/user-login.php?"
			."&uid=".$check_user->id()
		);
*/

//		exit('debug');

		if(!$handle_cookie_set)
			return $check_user;

		if($check_user->user_cookie_hash())
			$check_user->cookie_hash_set();
		else
			$check_user->cookie_hash_update();

		if(config('__login_redir'))
			return true;

		return $check_user;
	}

	static function do_logout()
	{
		$domains = config('balancer_board_domains');
		$next_domain = $domains[0];

		$redirect = bors()->request()->referer(); // isset($_GET['redirect_url']) ? $_GET['redirect_url'] : @$_SERVER['HTTP_REFERER'];
//		$refo = bors_load($redirect);
//		if(!object_property($refo, 'is_public'))
//			$redirect = NULL;

		$haction = bal_user_haction::add(bors()->user_id(), 'bal_users_helper', 'haction_domain_logout', 120, array(
			'domain' => $next_domain,
			'redirect' => $redirect,
		));
//		exit('go '.$haction->url($next_domain));
//		config_set('redirect_by_html', true);

		@file_get_contents("http://ls.balancer.ru/bors-api/user-login.php?"
			."&uid=".bors()->user_id()
		);

		return go($haction->url_ex($next_domain));
	}

	function reputation_titled_link() { return "<a href=\"http://www.balancer.ru/user/{$this->id()}/reputation/\">{$this->title()}</a>"; }

	function weight()
	{
		$_group_weights = array(
				1 => 8, // admin
				2 => 6, // moder
				3 => 0, // guest
				5 => 4, // coordin
				6 => 2, // старожилы
				21 => 4, // координатор-литератор
				26 => 0.001, // пария -- удалено
				27 => 1.5, // Проверенный участник
				28 => 0.1, // Ограниченный
		);

		$weight = @$this->_group_weights[$group];
		if(!$weight)
			$weight = 1;

		if($this->id() == 10000) // Balancer ;)
			$weight = 10;

		if($this->num_posts() < 50 || $this->create_time() > time() - 86400*2)
			$weight = 0;
	}

//	function set_last_visit_time() { }

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

		// Временный R/O
		if($w >= 10)
			return 0;

		$limit = 999999;

		if($w>0)
		{
//			$offset = max(2, round(15 - (time() - 1247947991)/86400));

			$limit = round(max(0, 300/($w*$w) * $this->reputation_weight() + 2 ));
		}

		$m = $this->money();
		if($m < 0)
			$limit = min($limit, max(2, round(-50000/$m)));

		if($this->create_time() > time() - 86400)
			$limit = min($limit, 10);

		if($limit > 50)
			$limit = -1;

		return $limit;
	}

	function today_posted()
	{
		return $this->__havec('today_posted') ? $this->__lastc() : $this->__setc(bors_count('balancer_board_post', array(
			'owner_id' => $this->id(), 
			'create_time>' => time()-86400,
		)));
	}

	function toweek_posted()
	{
		return $this->__havefc() ? $this->__lastc() : $this->__setc(bors_count('balancer_board_post', array(
			'owner_id' => $this->id(), 
			'create_time>' => time()-86400*7,
		)));
	}

	function tomonth_posted()
	{
		return $this->__havec('tomonth_posted') ? $this->__lastc() : $this->__setc(bors_count('balancer_board_post', array(
			'owner_id' => $this->id(), 
			'create_time>' => time()-86400*31,
		)));
	}

	function today_posted_in_forum($forum_id)
	{
		return $this->__havec('today_posted') ? $this->__lastc() : $this->__setc(bors_count('balancer_board_post', array(
			'balancer_board_post.owner_id=' => $this->id(), 
			'create_time>' => time()-86400,
			'inner_join' => 'balancer_board_topic ON (forum_post.topic_id = balancer_board_topic.id)',
			'balancer_board_topic.forum_id=' => $forum_id,
		)));
	}

	function next_can_post($limit, $forum_id)
	{
		if($this->attr('next_can_post'))
			return $this->attr('next_can_post');

		$first_in_day = bors_find_all('balancer_board_post', array(
			'balancer_board_post.owner_id=' => $this->id(), 
			'create_time>' => time()-86400*2,
			'order' => '-create_time',
			'limit' => $limit,
			'inner_join' => 'balancer_board_topic ON (forum_post.topic_id = balancer_board_topic.id)',
			'balancer_board_topic.forum_id=' => $forum_id,
		));

		if($first_in_day)
			$first_in_day = $first_in_day[count($first_in_day)-1];
		else
			$first_in_day = NULL;

		return $this->set_attr('next_can_post', $first_in_day ? $first_in_day->create_time()+86400 : NULL);
	}

	function blog() { return bors_load('balancer_board_blog', $this->id()); }

	function utmx_update()
	{
		if(empty($_COOKIE['__utmx']))
		{
			$utmx = $this->utmx();
			if(!$utmx)
				$utmx = $this->set_utmx(md5(rand(0, time()).time()), true);

			SetCookie("__utmx", $utmx, time()+365*86400, "/", $_SERVER['HTTP_HOST']);
			$_COOKIE['__utmx'] = $utmx;
		}
		else
		{
			if(!$this->utmx())
				$this->set_utmx($_COOKIE['__utmx'], true);
		}
	}

	function category_reputation($category_id)
	{
		$pcr = $this->per_category_reputations();
		if($pcr)
		{
			$pcr = unserialize($pcr);
			if(array_key_exists($category_id, $pcr))
				return $pcr[$category_id];
		}

		$pcr = array();
		foreach(bors_find_all('airbase_user_reputation', array(
			'*set' => 'SUM(score) AS summ',
			'user_id' => $this->id(),
			'is_deleted' => false,
			'group' => 'voter_id,category_id',
		)) as $r)
		{
			@$pcr[$r->category_id()] += $r->summ() * $r->voter()->weight();
		}
	}

	function is_oldtimer() { return in_array($this->group_id(), [6,19,20]); }

	function titled_link()
	{
		$css = [];

		if($this->is_admin_banned())
			$css[] = 's';

		if($this->is_banned())
			$css[] = 'gray';

		if($this->is_admin())
			$css[] = 'red';
		elseif($this->is_moderator())
			$css[] = 'red';
		elseif($this->is_coordinator())
			$css[] = 'orange';
		elseif($this->is_oldtimer())
			$css[] = 'green';

		if($css)
			$css = ' class="'.join(' ', $css).'"';
		else
			$css = '';

		return "<a href=\"{$this->url()}\"{$css}>{$this->title()}</a>";
	}
}
