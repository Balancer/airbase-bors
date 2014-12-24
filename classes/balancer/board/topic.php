<?php

class balancer_board_topic extends forum_topic
{
	function browser_title()
	{
		if($this->total_pages() <= 1)
			return $this->title();

		return $this->title() . " ({$this->page()}/{$this->total_pages()})";
	}

	function browser_description()
	{
		if($this->total_pages() <= 1)
			return $this->description();

		return $this->description() . " (страница {$this->page()} из {$this->total_pages()})";
	}

	function cache_static_can_be_dropped()
	{
		bors_debug::syslog('__pages_clean', "Clean {$this->static_file()}[{$this->page()}/{$this->total_pages()}]: ".($this->page() > $this->total_pages() - 3));
		return 1 || $this->page() > $this->total_pages() - 3;
	}

	function cache_static()
	{
		if(!$this->is_public_access())
			return 0;

		if($this->modify_time() < time() - 86400*365)
			return 86400*rand(300, 900);

		if($this->modify_time() < time() - 86400*30)
			return 86400*rand(7, 30);

		if($this->modify_time() < time() - 86400*7)
			return rand(3600, 86400);

		if($this->modify_time() < time() - 86400)
			return rand(600, 1200);

		return rand(60, 300);
	}

	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return config('punbb.database'); }
	function table_name() { return 'topics'; }

	function table_fields()
	{
		return array(
			'id',
			'forum_id_raw' => 'forum_id',
			'title'	=> 'subject',
			'description',
			'answer_notice',
			'admin_notice',
			'image_id',
			'image_time' => 'UNIX_TIMESTAMP(`image_ts`)',
			'create_time'	=> 'posted',
			'last_post_create_time'=> 'last_post',
			'modify_time',
			'is_public',
			'owner_id'=> 'poster_id',
			'last_poster_name' => 'last_poster',
			'author_name' => 'poster',
			'num_replies',
			'is_repaged',
			'visits' => 'num_views',
			'first_post_id' => 'first_pid',
			'last_post_id' => 'last_post_id',
			'first_visit_time' => 'first_visit',
			'last_visit_time' => 'last_visit',
			'last_edit_time' => 'last_edit',
			'sticky',
			'moved_to',
			'joined_to_topic_id', // id темы, к которой была присоединена данная.
			'closed',
			'keywords_string_db' => 'keywords_string',
			'bot_note',
		);
	}

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), array(
			'folder' => 'balancer_board_forum(forum_id)',
		));
	}

	function find_first_unvisited_post($user)
	{
		$uid = $user->id();

		$visit = bors_find_first('balancer_board_topics_visit', [
			'user_id' => $uid,
			'target_object_id' => $this->id()
		]);

		if($visit)
			$last_visit = $visit->last_visit();
		else
			// Если отметки о чтении топика нет, то считаем за дату последнего посещения
			// дату модификации самой старой записи в таблице посещений.
			$last_visit = bors_find_first('balancer_board_topics_visit', ['last_visit>' => 0])->modify_time();

		// Первое нечитанное сообщение темы
		$first_new_post = bors_find_first('balancer_board_post', [
			'topic_id' => $this->id(),
			'posted>' => $last_visit,
			'order' => 'sort_order,id',
		]);

		return $first_new_post;
	}

	static function _forum_ids($domain)
	{
		static $fcache = array();
		if(empty($fcache[$domain]))
		{
			$cat_ids = array_keys(objects_array('balancer_board_category', array("base_uri LIKE 'http://".addslashes($domain)."/%'", 'by_id' => true)));
			if(!$cat_ids)
				return $fcache[$domain] = array(-1);
			$sub_cat_ids = array_keys(objects_array('balancer_board_category', array('parent IN' => $cat_ids, 'by_id' => true)));
			$cat_ids = array_merge($cat_ids, $sub_cat_ids);

			$fcache[$domain]  = array_keys(objects_array('balancer_board_forum', array(
				'cat_id IN' => $cat_ids, 
				'is_public' => 1,
				'by_id' => true,
			)));
		}

		if(empty($fcache[$domain]))
			return array(-1);

		return $fcache[$domain];
	}

	static function sitemap_index($domain, $page, $per_page)
	{
		return array_reverse(objects_array('balancer_board_topic', array(
			'forum_id IN' => self::_forum_ids($domain),
			'page' => $page,
			'per_page' => $per_page,
			'order' => 'modify_time',
		)));
	}

	static function sitemap_last_modify_time($domain, $page, $per_page)
	{
		$dbh = new driver_mysql(config('punbb.database'));
		$dates = $dbh->select_array('topics', 'last_post', array(
			'forum_id IN' => self::_forum_ids($domain),
			'page' => $page,
			'per_page' => $per_page,
			'order' => 'last_post',
		));

		return $dates[count($dates)-1];
	}

	static function sitemap_total($domain)
	{
		return objects_count('balancer_board_topic', array(
			'forum_id IN' => self::_forum_ids($domain),
		));
	}

	static function create($forum, $title, $message, $user = NULL, $keywords_string = NULL, $as_blog = true, $data = array())
	{
//		echo "Pass new topic to {$forum->debug_title()}\n";

		$is_public = defval($data, 'is_public', true);

		$data = array_merge(array(
			'forum_id' => $forum->id(),
			'title'	=> $title,
			'last_post_create_time' => time(),
			'is_public' => $is_public,
			'owner_id' => $user->id(),
			'author_name' => $user->title(),
			'last_poster_name' => $user->title(),
			'num_replies' => 0,
			'is_repaged' => false,
			'visits' => 0,
			'keywords_string' => $keywords_string,
		), $data);

		$topic = object_new_instance(__CLASS__, $data);

		if(is_object($message)) // Если это объект — то постинг
			$post = $message;
		else // Если не объект — то текст первого постинга
			$post = balancer_board_post::create($topic, $message, $user, $keywords_string, $as_blog, $data);

		$topic->set_first_post_id($post->id(), true);
		$topic->set_last_post_id($post->id(), true);

		return $topic;
	}

	function topic_updated($post, $notifyed_user = NULL)
	{
		$user = $post->owner();
		$text = "{$post->topic()->title()}:\n"
			.trim(html_entity_decode(make_quote($user->title(), htmlspecialchars($post->source()), false), ENT_COMPAT, 'UTF-8'))
			."\n\n// #{$post->id()} {$post->url_for_igo()} «{$post->topic()->title()}», подписка";

		bors()->do_task('balancer_balabot_notify_post', array(
			'post_id' => $post->id(),
			'text' => $text,
			'notifyed_user' => $notifyed_user,
		));
	}

	function titled_link_new()
	{
		return $this->titled_link_ex(array('page' => 'new'));
	}

	function pre_show()
	{
		bors_lib_html::set_og_meta($this);
		balancer_board_posts_view::container_init();
		template_jquery_ui();

		jquery::on_ready(__DIR__.'/topics/view.inc.ready.js');

		if($this->answer_notice())
		{
			bors_use('/_bal/opt/sweet-alert.js');
			bors_use('/_bal/opt/sweet-alert.css');
		}

		if($this->page() == $this->total_pages())
			header("X-Accel-Expires: 30");
		elseif($this->page() >= $this->total_pages() - 2)
			header("X-Accel-Expires: 600");
		else
			header("X-Accel-Expires: 86400");

//		jquery_cloudZoom::load();
		jquery_fancybox::appear_all();
		return parent::pre_show();
	}

	function move_to_forum($forum_id)
	{
		// Перекинуть потом подготовку из moderate.php:320
		// А тут пока только перенос связанных forum_id
		// В блогах:
		foreach(bors_find_all('balancer_board_blog', array('topic_id' => $this->id())) as $blog)
			$blog->set_forum_id($forum_id);
	}

	function sort_title()
	{
		return bors_lower(preg_replace('/[^\wа-яА-ЯёЁ]/u', '', $this->title()));
	}

	function forum_title() { return $this->forum()->title(); }
	function title_with_forum() { return $this->title().' ['.$this->forum()->title().'] ('.$this->num_replies().')'; }

	function list_fields_format() { return '%title% [%forum_title%]'; }

	function last_post_snip()
	{
		$lp = $this->last_post();
		return object_property($lp, 'author_name');
	}

	function last_post_ctime() { return bors_time::factory($this->last_post_create_time()); }

	function _image_def()
	{
		if(!is_null($this->data['image_id']))
		{
			// Если указан image_id явно, то возвращаем его.
			if($this->data['image_id'])
			{
				$image = bors_load('airbase_image', $this->data['image_id']);

				if(airbase_image::is_logo_valid($image) && !preg_match('!/cache/!', $image->url()))
				{
					if(config('is_debug') && in_array($this->id(), array(27040, 89357)))
					{
						echo '<xmp>';
						var_dump($image->data, $image->url());
						exit();
					}

					return $image;
				}
			}

			// Иначе у нас там 0 — значит, что image берём с форума. Раз в час проверяем,
			// не появилась ли картинка в теме
			if(($this->data['image_time'] > time() - 600) && !config('is_debug'))
				return $this->forum()->image();
		}

		$obj = bors_find_first('balancer_board_posts_object', array(
			'inner_join' => array(
				'balancer_board_post ON balancer_board_post.id = balancer_board_posts_object.post_id',
				'`AB_BORS`.`bors_images` i ON balancer_board_posts_object.target_object_id = i.id',
			),
			'topic_id' => $this->id(),
			'width>=' => 100,
			'height>=' => 100,
			'target_class_id' => 202, // airbase_image
			'`i`.extension IN ("jpg", "jpeg", "png")',
			'full_file_name NOT LIKE "%/_cg/%"',
			'order' => '-post_id',
		));
/*
		if(config('is_debug'))
		{
			echo '<xmp>';
			var_dump($obj);
			exit();
		}
*/
		$this->set_image_time(time());

		if($obj)
		{
			$image = $obj->target();
			if($image
				&& ($th = $image->thumbnail('96x96(up,crop)'))
				&& airbase_image::is_logo_valid($image)
			)
			{
				if(config('is_debug') && preg_match('!1358612-thumbnail.jpg!', $image->full_file_name()))
				{
					echo '<xmp>';
					var_dump($obj->data);
					exit('zz');
				}

				$this->set('image_id', $image->id());
				return $image;
			}
			elseif(config('is_debug'))
			{
//				echo '<xmp>';
//				var_dump($obj->data, $image->data);
//				exit('yy');
			}
		}

		if(0 && config('is_debug') && in_array($this->id(), array(89274, 89354, 27040, 89357)))
		{
			echo '<xmp>';
//			var_dump($obj);

			foreach(bors_find_all('balancer_board_posts_object', array(
				'inner_join' => array(
					'balancer_board_post ON balancer_board_post.id = balancer_board_posts_object.post_id',
					'`AB_BORS`.`bors_images` i ON balancer_board_posts_object.target_object_id = i.id',
				),
				'topic_id' => $this->id(),
/*
				'target_class_id' => 202, // airbase_image
				'width>=' => 100,
				'height>=' => 100,
				'`i`.extension IN ("jpg", "jpeg", "png")',
				'full_file_name NOT LIKE "%/_cg/%"',
*/
				'order' => '-post_id',
			)) as $x)
				var_dump($x->data, $x->target()->data);

			exit('?'. $this->id());
		}

		$this->set('image_id', 0);

		if($image = $this->forum()->image())
			return $image;

		return NULL;
	}

	function image_thumbnail_64()
	{
		if(!($i = $this->image()))
			return NULL;

		return "<a href=\"{$this->url()}\">{$i->thumbnail('100x64(up,crop)')->html_code()}</a>";
	}

	private $first_post_time = NULL;
	private $last_post_time  = NULL;

	private function _calculate_times()
	{
		$posts = $this->posts();

		$first_post_time = time()+1;
		$last_post_time = -1;
		foreach($posts as $p)
		{
			if($p->create_time() > $last_post_time)
				$last_post_time = $p->create_time();

			if($p->create_time() < $first_post_time)
				$first_post_time = $p->create_time();
		}

		$this->first_post_time = $first_post_time;
		$this->last_post_time  = $last_post_time;
	}

	function first_post_time()
	{
		if(is_null($this->first_post_time))
		{
			$ch = new bors_cache_fast();
			if($ch->get('topic-page-first-post-time', $this->id().':'.$this->page()))
				return $this->first_post_time = $ch->last();
			else
			{
				$this->_calculate_times();
				$ch->set($this->first_post_time, 3600);
			}
		}

		return $this->first_post_time;
	}

	function last_post_time()
	{
		if(is_null($this->last_post_time))
			$this->_calculate_times();

		return $this->last_post_time;
	}

	function can_yandex_direct() { return config('ad.yandex.enabled') && preg_match('/balancer\.ru/', $_SERVER['HTTP_HOST']); }

	function can_adsense()
	{
		if(!$this->is_public_access())
			return false;

		if(preg_match('/(airbase\.ru)/', @$_SERVER['HTTP_HOST']))
			return true;

		return $this->last_post_create_time() > time() - 86400*7;
	}

	function tpl_ad_top()
	{
		//	{* include file="xfile:forum/ads/top-ad-podarini.html" *}
		//	{*  && !preg_match(config('ads.disabled_regexp'), $smarty.server.HTTP_HOST) *}
		if($this->get('can_adsense'))
			return "xfile:forum/ads/top-ad-google.html";

		// {* include file="xfile:forum/ads/top-ad-balancer.html" *}
		// {*  include file="xfile:forum/ads/begun-forums.airbase.ru.html" *}
		// {include file="xfile:forum/ads/begun-top-auto.html"}
		return 'xfile:forum/ads/yandex-direct-h4.html';
	}
}
