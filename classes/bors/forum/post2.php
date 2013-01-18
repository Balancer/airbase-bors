<?php

// bors_exit('Форум в стадии модификации базы данных. Минут 30 (до ~03:50) будет недоступен. Можете пока сходить на <a href="http://balancer.endofinternet.net/mybb/index.php">Запасные форумы</a>.');

// Эксперименты по переносу forum_post на bors_storage_mysql. Не сносить, есть использующие!

include_once('engines/lcml.php');
include_once('inc/browsers.php');
include_once('inc/clients.php');

class forum_post2 extends base_page_db
{
	function config_class() { return 'balancer_board_config'; }
	function template() { return 'forum/page.html'; }

	function storage_engine() { return 'bors_storage_mysql'; }

	function db_name() { return config('punbb.database', 'AB_FORUMS'); }
	function table_name() { return 'posts'; }

	function table_fields()
	{
		return array(
			'id',
			'title_raw' => 'title',
			'topic_id',
			'topic_page' => 'page',
			'create_time'	=> 'posted',
			'edited',
			'edited_by',
			'owner_id' => 'poster_id',
			'avatar_raw_id' => 'avatar_id',
			'poster_ip',
			'poster_email',
			'poster_ua',
			'author_name' => 'poster',
			'answer_to_id' => 'answer_to_post_id',
			'answer_to_user_id',
			'post_source' => 'source',
			'hide_smilies',
			'have_attach',
			'have_cross',
			'have_answers',
			'score',
			'is_moderatorial',
			'is_deleted',
			'is_hidden',
			'is_spam',
			'is_incorrect',
			'last_moderator_id',
			'sort_order' => '`order`',
		);
	}

// Заняты: 	field1 => title
//			field2 => score

// Свободны:field3 string
//			field4 => is_spam int(11)


	function left_join_fields()
	{
		return array(
			$this->main_db() => array(
//				$this->main_table() => $this->main_table_fields(),
				'posts_cached_fields(post_id)' => array(
//				'posts_cached_fields' => array(	'id' => 'post_id',
					'flag_db' => 'flag',
					'warning_id',
//					'answers_count_raw' => 'answers_count',
					'mark_best_date',
					'score_positive_raw' => 'score_positive',
					'score_negative_raw' => 'score_negative',
					'post_body' => 'html',
					'full_html_content' => 'html_full_post',
//					),
				),
			),
			'CACHE_NO_BACKUP' => array(
				'board_posts(post_id)' => array(
					'html_full_post',
				),
			),
		);
	}

//	function __orm_setters() { return array('';); }

function topic_id() { return @$this->data['topic_id']; }
function set_topic_id($v, $dbup) { return $this->set('topic_id', $v, $dbup); }
function topic_page()
{
	$page = @$this->data['topic_page'];
	if(!$page)
	{
		$this->topic()->repaging_posts();
		$post = bors_load('forum_post', $this->id(), array('no_load_cache' => true));
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

	function topic() { return bors_load('balancer_board_topic', $this->topic_id()); }
	function parents() { return array("balancer_board_topic://".$this->topic_id()); }

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
			$this->__owner =  bors_load('balancer_board_user', $this->owner_id());

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

		if(!$this->post_body() || config('lcml_cache_disable'))
		{
			$body = lcml($this->post_source(),
				array(
					'cr_type' => 'save_cr',
					'forum_type' => 'punbb',
					'forum_base_uri' => 'http://www.balancer.ru/forum',
					'sharp_not_comment' => true,
					'html_disable' => 'direct',
					'uri' => $this->internal_uri(),
					'nocache' => true,
					'self' => $this,
				)
			);

//			if(config('is_debug')) echo 'x='.$body."\n";

			$this->set_post_body($body, true);
		}

		return $this->post_body();
	}

	function flag()
	{
		// Вторая часть условия - проверка на баг обрезания строки.
		if(!$this->flag_db() || !preg_match("!>$!", $this->flag_db()))
		{
			require_once('inc/clients/geoip-place.php');
//			$db = new driver_mysql(config('punbb.database', 'AB_FORUMS'));
//			$db->insert_ignore('posts_cached_fields', array('post_id' => $this->id()));
			$this->set_flag_db(geoip_flag($this->poster_ip(), $this->owner_id() == 10000), true);
//			$db->close();
		}

		return $this->flag_db();
	}

	function owner_user_agent()
	{
		if(!$this->poster_ua())
			return NULL;

		return bors_browser_images($this->poster_ua(), $this->poster_ip());
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

	function answer_to_user_id()
	{
		if(is_null(@$this->data['_answer_to_user_id']))
			return $this->set('_answer_to_user_id', object_property($this->answer_to(), 'owner_id'), true);

		return $this->data['_answer_to_user_id'];
	}

	function url_in_container() { return $this->url_in_topic(); }

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

			$topic = bors_load('balancer_board_topic', $tid);

			if(!$topic)
				bors_exit(ec("Указанный Вами топик [topic_id={$this->topic_id()}, post_id={$this->id()}] не найден"));
		}

		if(!$topic->is_repaged())
		{
			$topic->repaging_posts();
			$post = bors_load($this->class_name(), $this->id());
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
//		return 'http://www.balancer.ru/'.strftime("%Y/%m/%d/post-", $this->modify_time()).$this->id().".html";
//		return 'http://www.balancer.ru/_bors/igo?o='.$this->internal_uri_ascii();
	}

	function url_for_igo() { return 'http://www.balancer.ru/g/p'.$this->id(); }

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
	function shortest_title() { return "#".date("d.m.y H:i", $this->create_time()); }
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
			$db = new driver_mysql('AB_FORUMS');
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
			$attaches = bors_find_all('balancer_board_attach', array('post_id' => $this->id()));

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
			return $this->_attaches = bors_find_all('balancer_board_attach', array('post_id' => $this->id()));

		if(!($attach = bors_load('balancer_board_attach', $this->have_attach())))
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

//		$db = new DataBase('AB_FORUMS');
//		return intval($db->get("SELECT COUNT(*) FROM posts WHERE answer_to = {$this->id}"));
	}

	function visits() { return $this->topic()->num_views(); }

	function class_title() { return ec("Сообщение форума"); }
	function class_title_vp() { return ec("сообщение форума"); }

	function answers()
	{
		return bors_find_all('forum_post', array(
			'where' => array('answer_to=' => intval($this->id())),
			'order' => 'id',
		));
	}

	function answers_in_other_topics()
	{
		$result = array();
		foreach($this->select_array('id', array('answer_to=' => $this->id(), 'topic_id<>' => $this->topic_id())) as $pid)
			if($post = bors_load('forum_post', $pid))
				$result[] = $post;

		return $result;
	}

	function answers_in_this_topic()
	{
		$result = array();
		foreach($this->select_array('id', array('answer_to=' => $this->id(), 'topic_id=' => $this->topic_id())) as $pid)
			if($post = bors_load('forum_post', $pid))
				$result[] = $post;

		return $result;
	}

	function move_tree_to_topic($new_tid)
	{
		$GLOBALS['move_tree_to_topic_changed_topics'] = array();

		$this->__move_tree_to_topic($new_tid, $this->topic_id());

		foreach(array_keys($GLOBALS['move_tree_to_topic_changed_topics']) as $tid)
			bors_load('balancer_board_topic', $tid, array('no_load_cache' => true))->recalculate();

		$this->recalculate();
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

		bors_load('balancer_board_topic', $old_tid)->recalculate();
		$new_topic = bors_load('balancer_board_topic', $new_tid);
		$new_topic->recalculate();

		$this->recalculate($new_topic);

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
			return $this->warning = bors_load('airbase_user_warning', $this->warning_id());

		$warn = objects_first('airbase_user_warning', array(
			'warn_class_id' => $this->extends_class_id(),
			'warn_object_id='=>$this->id(),
			'order' => '-time'));

		$db = new driver_mysql('AB_FORUMS');
		$db->insert_ignore('posts_cached_fields', array('post_id' => $this->id()));
		$db->close();
		$this->set_warning_id($warn ? $warn->id() : -1, true);
		return $this->warning = $warn ? $warn : NULL;
	}

	function cache_children()
	{
		$res = array(
			bors_load('balancer_board_topic', $this->topic_id()),
			bors_load('airbase_user_topics', $this->owner_id()),
			bors_load('balancer_board_blog', $this->id()),
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
	function igo($permanent = true) { return go($this->url_in_container(), $permanent); }

	function has_readed_by_user($user)
	{
		$topic = $this->topic();
		return $topic->last_visit_time_for_user($user) > $topic->last_post_create_time();
	}

	function on_delete_pre() { $this->topic(); }
	function on_delete_post() { $this->topic()->recalculate(); }

	function score_positive($recalculate = false)
	{
		if(!$recalculate && !is_null($this->score_positive_raw()))
			return $this->score_positive_raw();

		return $this->set_score_positive_raw(objects_count('bors_votes_thumb', array(
			'target_class_name' => $this->extends_class_name(),
			'target_object_id' => $this->id(),
			'score' => 1,
		)), true);
	}

	function score_negative($recalculate = false)
	{
		if(!$recalculate && !is_null($this->score_negative_raw()))
			return $this->score_negative_raw();

		return $this->set_score_negative_raw(objects_count('bors_votes_thumb', array(
			'target_class_name' => $this->extends_class_name(),
			'target_object_id' => $this->id(),
			'score' => -1,
		)), true);
	}

	function score_colorized($recalculate = false)
	{
		if(is_null($this->score()) && !$recalculate)
			return "";

		$positives = $this->score_positive($recalculate);
		$negatives = $this->score_negative($recalculate);

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

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), array(
			'blog' => 'balancer_board_blog(id)',
		));
	}

	function recalculate($topic = NULL)
	{
		if(!$topic)
			$topic = $this->topic();

		if($blog = $this->blog())
			$blog->recalculate($this, $topic);
	}
}
