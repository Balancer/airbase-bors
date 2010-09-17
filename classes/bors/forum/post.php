<?php

// bors_exit('Форум в стадии модификации базы данных. Минут 30 (до ~03:50) будет недоступен. Можете пока сходить на <a href="http://balancer.endofinternet.net/mybb/index.php">Запасные форумы</a>.');

include_once('engines/lcml.php');
include_once('inc/browsers.php');

class forum_post extends base_page_db
{
	function config_class() { return 'balancer_board_config'; }
	function template() { return 'forum/page.html'; }

	function main_db() { return config('punbb.database', 'punbb'); }
	function main_table() { return 'posts'; }

	function main_db_fields()
	{
		return array(
			$this->main_table() => $this->main_table_fields(),
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
			'title_raw' => 'field1',
			'topic_id',
			'topic_page' => 'page',
			'create_time'	=> 'posted',
			'edited',
			'edited_by',
			'owner_id' => 'poster_id',
			'poster_ip',
			'poster_email',
			'poster_ua',
			'author_name' => 'poster',
			'answer_to_id' => 'answer_to',
			'answer_to_user_id' => 'anwer_to_user_id',
			'post_source' => 'source',
			'post_body' => 'source_html',
			'hide_smilies',
			'have_attach',
			'have_cross',
			'have_answers',
			'score' => 'field2',
			'is_moderatorial',
			'is_deleted',
			'is_spam',
			'is_incorrect',
			'last_moderator_id',
		);
	}

// Заняты: 	field1 => title
//			field2 => score

// Свободны:field3 string
//			field4 => is_spam int(11)

//	function __orm_setters() { return array('';); }

function topic_id() { return @$this->data['topic_id']; }
function set_topic_id($v, $dbup) { return $this->set('topic_id', $v, $dbup); }
function topic_page()
{
	$page = @$this->data['topic_page'];
	if(!$page)
	{
		$this->topic()->repaging_posts();
		$post = object_load('forum_post', $this->id(), array('no_load_cache' => true));
		$page = @$post->data['topic_page'];
	}

	return $page;
}

function edited() { return @$this->data['edited']; }
function set_edited($v, $dbup) { return $this->set('edited', $v, $dbup); }
function edited_by() { return @$this->data['edited_by']; }
function set_edited_by($v, $dbup) { return $this->set('edited_by', $v, $dbup); }
function owner_id() { return @$this->data['owner_id']; }
function set_owner_id($v, $dbup) { return $this->set('owner_id', $v, $dbup); }
function poster_ip() { return @$this->data['poster_ip']; }
function set_poster_ip($v, $dbup) { return $this->set('poster_ip', $v, $dbup); }
function poster_email() { return @$this->data['poster_email']; }
function set_poster_email($v, $dbup) { return $this->set('poster_email', $v, $dbup); }
function poster_ua() { return @$this->data['poster_ua']; }
function set_poster_ua($v, $dbup) { return $this->set('poster_ua', $v, $dbup); }
function author_name()
{
	if(empty($this->data['author_name']))
	{
		debug_hidden_log('empty-data', 'no author name');
		if($author = $this->owner())
			$this->set_author_name($author->title(), true);
		else
			$this->set_author_name('Неизвестный', true);
	}

	return $this->data['author_name'];
}
function set_author_name($v, $dbup) { return $this->set('author_name', $v, $dbup); }
function answer_to_id() { return @$this->data['answer_to_id']; }
function set_answer_to_id($v, $dbup) { return $this->set('answer_to_id', $v, $dbup); }
function post_source() { return @$this->data['post_source']; }
function set_post_source($v, $dbup) { return $this->set('post_source', $v, $dbup); }
function post_body() { return @$this->data['post_body']; }
function hide_smilies() { return @$this->data['hide_smilies']; }
function set_hide_smilies($v, $dbup) { return $this->set('hide_smilies', $v, $dbup); }
function have_attach() { return @$this->data['have_attach']; }
function set_have_attach($v, $dbup) { return $this->set('have_attach', $v, $dbup); }
function have_cross() { return @$this->data['have_cross']; }
function set_have_cross($v, $dbup) { return $this->set('have_cross', $v, $dbup); }
function have_answers() { return @$this->data['have_answers']; }
function set_have_answers($v, $dbup) { return $this->set('have_answers', $v, $dbup); }
function score() { return @$this->data['score']; }
function set_score($v, $dbup) { return $this->set('score', $v, $dbup); }

	function set_post_body($value, $dbupd)
	{
		if($value == '' && $value !== NULL && $dbupd)
			debug_hidden_log('body', 'Set empty body');
		$this->set('post_body', $value, $dbupd); 
	}

	//TODO: странно, при прямом вызове пропадают флаги.
	function flag_db() { return $this->data['flag_db']; }
	function warning_id() { return $this->data['warning_id']; }

	function topic() { return object_load('forum_topic', $this->topic_id()); }
	function parents() { return array("forum_topic://".$this->topic_id()); }

	function set_topic_page($page, $dbupd)
	{
		if($page && !is_numeric($page)/*gettype($page) != 'integer'*/)
			debug_hidden_log('type-mismatch-page', 'Set topic_page to '.gettype($page).'('.$page.')');

		$this->set('topic_page', $page, $dbupd);
	}

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

	function source()
	{
		if($ps = $this->post_source())
			return $ps;

		debug_hidden_log('messages-lost-3', 'Empty post source!');
		return '';
	}

	var $_source_changed = false;

	function set_source($message, $db_update)
	{
		if(!$message)
		{
			debug_hidden_log('data-lost', 'Set to empty post source!');
			bors_exit('Set to empty post source!');
		}

		$this->set_post_source($message, $db_update);
		$this->_source_changed |= $db_update;

		if($db_update)
			$this->set_post_body(NULL, $db_update);

		return $this->_post_source = $message;
	}

	function set_body($html, $dbup) { return $this->set_post_body($html, $dbup); }

	function body()
	{
		$this->source();

		if(!$this->post_body() || !empty($GLOBALS['bors_data']['lcml_cache_disabled']))
		{
			$body = lcml($this->post_source(),
				array(
					'cr_type' => 'save_cr',
					'forum_type' => 'punbb',
					'forum_base_uri' => 'http://balancer.ru/forum',
					'sharp_not_comment' => true,
					'html_disable' => 'direct',
					'uri' => $this->internal_uri(),
					'nocache' => true,
				)
			);

			$this->set_post_body($body, true);
		}

		return $this->post_body();
	}

	function flag()
	{
		// Вторая часть условия - проверка на баг обрезания строки.
		if(!$this->flag_db() || !preg_match("!>$!", $this->flag_db()))
		{
			include_once('funcs/users/geoip/get_flag.php');
			$db = new driver_mysql(config('punbb.database', 'punbb'));
			$db->insert_ignore('posts_cached_fields', array('post_id' => $this->id()));
			$this->set_flag_db(get_flag($this->poster_ip(), $this->owner()), true);
			$db->close();
		}

		return $this->flag_db();
	}

	function owner_user_agent()
	{
		if(!$this->poster_ua())
			return NULL;

		list($os, $browser) = get_browser_info($this->poster_ua());

		$out_os = '';
		switch($os)
		{
			case 'Linux':
				$out_os = '<img src="/bors-shared/images/os/linux.gif" width="16" height="16" alt="Linux" />';
				break;
			case 'FreeBSD':
				$out_os = '<img src="/bors-shared/images/os/freebsd.png" width="16" height="16" alt="FreeBSD" />';
				break;
			case 'MacOSX':
				$out_os = '<img src="/bors-shared/images/os/macos.gif" width="16" height="16" alt="Mac OS X" />';
				break;
			case 'iPhone':
				$out_os = '<img src="/bors-shared/images/os/iphone.gif" width="16" height="16" alt="iPhone" />';
				break;
			case 'Symbian':
				$out_os = '<img src="/bors-shared/images/os/symbian.gif" width="16" height="16" alt="Symbian" />';
				break;
			case 'J2ME':
				$out_os = '<img src="/bors-shared/images/os/java.gif" width="16" height="16" alt="J2ME" />';
				break;
			case 'OS/2':
				$out_os = '<img src="/bors-shared/images/os/os2.gif" width="16" height="16" alt="OS/2" />';
				break;
			case 'PocketPC':
			case 'J2ME':
				break;
			case 'WindowsVista':
			case 'WindowsXP':
			case 'Windows2000':
			case 'Windows98':
			case 'Windows98':
			case 'Windows':
				$out_os = '<img src="/bors-shared/images/os/windows.gif" width="16" height="16" alt="Windows" />';
				break;
			default:
		}

		$out_browser = '';
		switch($browser)
		{
			case 'Opera':
				$out_browser = '<img src="/bors-shared/images/browsers/opera.gif" width="16" height="16" alt="Opera" />';
				break;
			case 'Konqueror':
				break;
			case 'SeaMonkey':
				$out_browser = '<img src="/bors-shared/images/browsers/seamonkey.gif" width="16" height="16" alt="SeaMonkey" />';
				break;
			case 'Google Chrome':
				$out_browser = '<img src="/bors-shared/images/browsers/chrome.png" width="16" height="16" alt="Google Chrome" />';
				break;
			case 'Firefox':
				$out_browser = '<img src="/bors-shared/images/browsers/firefox.gif" width="16" height="16" alt="Firefox" />';
				break;
			case 'Iceweasel':
				$out_browser = '<img src="/bors-shared/images/browsers/iceweasel.png" width="16" height="16" alt="Iceweasel" />';
				break;
			case 'Safari':
				$out_browser = '<img src="/bors-shared/images/browsers/safari.png" width="16" height="16" alt="Safari" />';
				break;
			case 'Gecko':
				$out_browser = '<img src="/bors-shared/images/browsers/mozilla.gif" width="16" height="16" alt="Mozilla" />';
				break;
			case 'IE5':
			case 'IE6':
			case 'IE7':
			case 'IE8':
			case 'MSIE':
				$out_browser = '<img src="/bors-shared/images/browsers/ie6.gif" width="16" height="16" alt="IE" />';
				break;
			default:
		}

		if((!$out_browser || !$out_os) && $this->poster_ua())
			debug_hidden_log("user_agent", "Unknown user agent: '".$this->poster_ua()."' in post ".$this->id());

		return '<span title="'.htmlspecialchars($this->poster_ua())
			.' ['.htmlspecialchars($os).','.htmlspecialchars($browser).']">'
			.$out_browser.$out_os.'</span>';
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

	// Осторожнее и использованием $topic! Если, например, ищем ссылку на ответ, находящийся 
	// не в текущей теме.
	function url_in_topic($topic = NULL)
	{
		$pid = $this->id();

		if(!$topic)
		{
			$tid = $this->topic_id();

			if(!$tid)
				bors_exit(ec("Указанный Вами топик [topic_id={$this->topic_id()}, post_id={$this->id()}] не найден"));

			$topic = object_load('forum_topic', $tid);

			if(!$topic)
				bors_exit(ec("Указанный Вами топик [topic_id={$this->topic_id()}, post_id={$this->id()}] не найден"));
		}

		if(!$topic->is_repaged())
		{
			$topic->repaging_posts();
			$post = object_load($this->class_name(), $this->id());
		}
		else
			$post = $this;

		return $topic->url($post->topic_page())."#p".$post->id();
	}

	function modify_time($exact = false)
	{
		if($time = $this->edited())
			return $time;

		return $this->create_time($exact);
	}

	function url()
	{
		return dirname($this->topic()->url()).'/p'.$this->id().'.html';
//		require_once("inc/urls.php");
//		return 'http://balancer.ru/'.strftime("%Y/%m/%d/post-", $this->modify_time()).$this->id().".html";
//		return 'http://balancer.ru/_bors/igo?o='.$this->internal_uri_ascii();
	}

	function url_for_igo() { return 'http://balancer.ru/_bors/igo?o='.$this->internal_uri_ascii(); }

	function titled_link($text = NULL, $css=NULL) 
	{
		if(!$title)
			$title = $this->title();

		if($css)
			$css = " class=\"{$css}\"";

		return "<a href=\"{$this->url()}\"{$css}>{$title}</a>";
	}

	function title() { return object_property($this->topic(), 'title')." [".$this->nav_name()."]"; }
	function page_title() { return object_property($this->topic(), 'title')." <small>[".$this->nav_name()."]</small>"; }
	function nav_name() { return ($this->author_name() ? $this->author_name() : ($this->owner() ? $this->owner()->title() : 'Unknown'))."#".date("d.m.y H:i", $this->create_time()); }
/*
	function description()
	{
		return "<ul><li><a href=\"{$this->url_in_topic()}\">Помотреть это сообщение в теме</a></li></ul>";
	}
*/
	function local_data()
	{
		return array(
			'p' => $this,
		);
	}

	function base_url()
	{
		return $this->topic()->forum()->category()->category_base_full();
	}

	private $_attach_ids = false;
	function attach_ids()
	{
		if($this->_attach_ids !== false)
			return $this->_attach_ids;

		if($this->have_attach() === NULL)
		{
			$db = new driver_mysql('punbb');
			$ids = $db->select_array('attach_2_files', 'id', array('post_id' => $this->id()));
			$db->close();
			if($this->_attach_ids = $ids)
			{
				if(count($ids) > 1)
					$this->set_have_attach(-1, true);
				else
					$this->set_have_attach($ids[0], true);
			}
			else
				$this->set_have_attach(0, true);

			return $ids;
		}

		if(!$this->have_attach())
			return $this->_attach_ids = array();

		return $this->_attach_ids = array($this->have_attach());
	}

	private $_attaches = NULL;
	function attaches()
	{
		if($this->_attaches !== NULL)
			return $this->_attaches;

		if($this->have_attach() === NULL)
		{
			$attaches = objects_array('airbase_forum_attach', array('post_id' => $this->id()));

			if($this->_attaches = $attaches)
			{
				if(count($attaches) > 1)
					$this->set_have_attach(-1, true);
				else
					$this->set_have_attach($attaches[0]->id(), true);
			}
			else
				$this->set_have_attach(0, true);
		}

		if(!$this->have_attach())
			return $this->_attaches = array();

		if($this->have_attach() == -1)
			return $this->_attaches = objects_array('airbase_forum_attach', array('post_id' => $this->id()));

		if(!($attach = object_load('airbase_forum_attach', $this->have_attach())))
		{
			debug_hidden_log('lost-objects', "Incorrect attach {$this->have_attach()} in post {$this->id()}");
			return array();
		}
		return $this->_attaches = array($attach);
	}

	function set_attaches($attaches)
	{
		return $this->_attaches = $attaches;
	}

	function search_source() { return $this->source(); }

	function num_replies()
	{
		return 0;

//		$db = &new DataBase('punbb');
//		return intval($db->get("SELECT COUNT(*) FROM posts WHERE answer_to = {$this->id}"));
	}

	function visits() { return $this->topic()->num_views(); }

	function class_title() { return ec("Сообщение форума"); }
	function class_title_vp() { return ec("сообщение форума"); }

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
		$GLOBALS['move_tree_to_topic_changed_topics'] = array();

		$this->__move_tree_to_topic($new_tid, $this->topic_id());

		foreach(array_keys($GLOBALS['move_tree_to_topic_changed_topics']) as $tid)
			object_load('forum_topic', $tid, array('no_load_cache' => true))->recalculate();
	}

	private function __move_tree_to_topic($new_tid, $old_tid)
	{
		$GLOBALS['move_tree_to_topic_changed_topics'][$new_tid] = true;
		$GLOBALS['move_tree_to_topic_changed_topics'][$this->topic_id()] = true;

//		echo "Move {$this->debug_title()} from {$this->topic_id()} to {$new_tid}<br />\n";

		if($this->topic_id() == $old_tid)
		{
			$this->set_topic_id($new_tid, true);
			$this->cache_clean();
			cache_static::drop($this);
		}

		foreach($this->answers() as $answer)
			$answer->__move_tree_to_topic($new_tid, $old_tid);
	}

	function move_to_topic($new_tid)
	{
		$old_tid = $this->topic_id();

		if($new_tid == $old_tid)
			return;

		$this->set_topic_id($new_tid, true);

		object_load('forum_topic', $old_tid)->recalculate();
		object_load('forum_topic', $new_tid)->recalculate();

		cache_static::drop($this);
		$this->cache_clean();
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

		$db = new driver_mysql('punbb');
		$db->insert_ignore('posts_cached_fields', array('post_id' => $this->id()));
		$db->close();
		$this->set_warning_id($warn ? $warn->id() : -1, true);
		return $this->warning = $warn ? $warn : NULL;
	}

	function cache_children()
	{
		$res = array(
			object_load('forum_topic', $this->topic_id()),
			object_load('airbase_user_topics', $this->owner_id()),
			object_load('balancer_board_blog', $this->id()),
		);

		return $res;
	}

	function is_edit_disable()
	{
		if($this->id() == $this->topic()->first_post_id())
			return false;

		if(($me = bors()->user()) && $me->group()->is_coordinator())
			return false;

		if($this->create_time() < time() - 86400)
			return ec("Вы не можете редактировать это сообщение, так как прошло более суток с момента его создания");

		return false;
	}

	function edit_url() { return "{$this->topic()->forum()->category()->category_base_full()}edit.php?id={$this->id()}"; }

//	function pre_show() { return go($this->url_in_topic()); }
	function igo($permanent = true) { return go($this->url_in_topic(), $permanent); }

	function score_colorized($recalculate = false)
	{
		if(is_null($this->score()) && !$recalculate)
			return "";

		$data = array(
			'target_class_name' => $this->extends_class(),
			'target_object_id' => $this->id(),
			'score' => 1,
		);

		$positives = objects_count('bors_votes_thumb', $data);

		$data['score'] = -1;
		$negatives = objects_count('bors_votes_thumb', $data);

		$score = $positives - $negatives;

		if($positives == 0 && $negatives == 0)
			$this->set_score(NULL,   true);
		else
			$this->set_score($score, true);

		if($score > 0)
			$color = 'green';
		elseif($score<0)
			$color = 'red';
		else
			$color = 'black';

		if($score>0)
			$score = "+{$score}";

		if($positives && $negatives)
			$rate = " <small>(<span style=\"color:green\">+{$positives}</span>/<span style=\"color:red\">-{$negatives}</span>)</small>";
		else
			$rate = "";

		return "<span style=\"color:$color\">{$score}</span>{$rate}";
	}

	function has_readed_by_user($user)
	{
		$topic = $this->topic();
		return $topic->last_visit_time_for_user($user) > $topic->last_post_create_time();
	}

	function on_delete_pre() { $this->topic(); }
	function on_delete_post() { $this->topic()->recalculate(); }
}
