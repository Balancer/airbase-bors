<?php

use Symfony\Component\Yaml\Dumper;

class balancer_board_post extends forum_post
{
	function extends_class_name() { return 'forum_post'; }

	function is_public_access() { return $this->topic() && $this->topic()->forum_id() && $this->topic()->forum()->is_public_access(); }

	static function is_post($object)
	{
		return is_object($object) && in_array($object->class_name(), array('forum_post', 'balancer_board_post'));
	}

	function html($data = array())
	{
		$data = array_merge(array(
			'p' => $this,	//	сам постинг
			'forum' => object_property($this->topic(), 'forum'), // форум, если нужно показать ссылку на него
//			'$no_show_answers = true, если не показывать ответы.
//			'show_title' => true, // если нужна печать заголовка.
//$skip_author_name
//	$skip_avatar_block
//	$skip_date
//	$skip_forums - не отображать название форума
			'skip_votes' => true, //  - не рисовать иконки в заголовке
			'skip_message_footer' => true,
			'strip' => 1024, // - сколько резать символов.
		), $data);

		return bors_templates_smarty::fetch('xfile:/var/www/bors/bors-airbase/templates/forum/post.html', $data);
	}

	function html2()
	{
		$data = array(
			'this' => $this,
		);

		return bors_templates_smarty::render_data('xfile:'.str_replace('.php', '.html', $this->class_file()), $data);
	}

	function text($data = array())
	{
		$data = array_merge(array(
			'p' => $this,	//	сам постинг
			'forum' => $this->topic()->forum(), // форум, если нужно показать ссылку на него
			'show_title' => true, // если нужна печать заголовка.
			'skip_votes' => true, //  - не рисовать иконки в заголовке
			'skip_message_footer' => true,
			'strip' => 1024, // - сколько резать символов.
		), $data);

		return bors_templates_smarty::render_data('xfile:/var/www/bors/bors-airbase/templates/forum/post.text', $data);
	}

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), array(
			'avatar_raw' => 'balancer_board_avatar(avatar_raw_id)',
			'container' => 'balancer_board_topic(topic_id)',
		));
	}

	function folder() { return $this->container()->folder(); }

	function avatar()
	{
		$avatar_raw = $this->avatar_raw();
		if($avatar_raw)
			return $avatar_raw;

		// К постингу не был приписан ни один аватар.
		// Попробуем найти уже зарегистрированный аватар пользователя.
		$owner_avatar = bors_find_first('balancer_board_avatar', array(
			'owner_id' => $this->owner_id(),
			'order' => '-create_time',
		));

		if($owner_avatar)
			return $owner_avatar;

		$owner = $this->owner();
		if(!$owner)
			return NULL; //TODO: вот тут и нужно приделывать граватары всякие

		$avatar_file = $owner->use_avatar();
		if(!$avatar_file)
			return NULL; //TODO: и тут тоже нужно приделывать граватары всякие

		//FIXME: хардкодный путь к аватарам
		$avatar_file_full_path = '/var/www/balancer.ru/htdocs/forum/punbb/img/avatars/'.$avatar_file;

		if(!file_exists($avatar_file_full_path))
			return NULL; //TODO: и тут тоже нужно приделывать граватары всякие

		$image = bors_image::register_file($avatar_file_full_path);
		// Ссылка у нас единая. Кстати...
		//FIXME: тут тоже подумать на тему настроек
		$image->set_full_url('http://s.wrk.ru/a/'.$avatar_file, true);
		$image->set_relative_path(NULL, true);

		// Всё, картинка есть, можно регистровать новый аватар
		return object_new_instance('balancer_board_avatar', array(
			'owner_id' => $this->owner_id(),
			'image_class_name' => $image->class_name(),
			'image_id' => $image->id(),
			'image_original_url' => $image->full_url(),
			'image_file' => $image->full_file_name(),
//			'image_html',
			'title' => $owner->title(),
			'signature' => $owner->signature(),
//			'signature_html',
//			'create_time',
		));
	}

	function direct_answers()
	{
		return bors_find_all(__CLASS__, array(
			'answer_to_id' => $this->id(),
		));
	}

	function answers_count($recount = false, $recount_child = false)
	{
		if(!$recount && !is_null($this->answers_count_raw()))
			return $this->answers_count_raw();

		$summ = 0;
		foreach($this->direct_answers() as $a)
			$summ += $a->answers_count($recount_child) + 1;

//		debug_hidden_log('__answers', "{$this->debug_title}=$summ");
		return $this->set_answers_count_raw($summ, true);
	}

	function parents_answers_recount($set = false)
	{
//		echo "par({$this})\n";
		if($set !== false)
			$this->set_answers_count_raw($set);

		if($parent = bors_load('balancer_board_post', $this->answer_to_id()))
		{
//			echo "par={$parent} from ".$parent->answers_count(false)." to \n";
			$parent->answers_count(true);
			$parent->parents_answers_recount();
//			echo "\t".$parent->answers_count(false)."\n";
		}
	}

	static function create($data)
	{
//		return $this->create_post$topic, $message, $user, $keywords_string, $as_blog, $data);
		throw new Exception("Try to create post by broken legacy");
	}

	// Нигде не используется? Выше ловушка. Если сработает — переводить сюда. Нет — сносить этот метод.
	static function create_post($topic, $message, $user, $keywords_string = NULL, $as_blog = NULL, $data = [])
	{
		if(is_numeric($topic))
			$topic = bors_load('balancer_board_topic', $topic);

//		echo "Pass post to $topic_id\n";
		$data = array_merge($data, array(
			'author_name' => $user->title(),
			'owner_id' => $user->id(),
			'poster_email' => NULL, //($pun_config['p_force_guest_email'] == '1' || $email != '') ? $email : '',
			'hide_smilies' => false, //$hide_smilies, 
			'topic_id' => $topic->id(),
			'source' => $message,
		));

		if(empty($data['poster_ip']))
			$data['poster_ip'] = bors()->client()->ip();

		if(empty($data['poster_ua']))
			$data['poster_ua'] = bors()->client()->agent();

		$post = object_new_instance(__CLASS__, $data);

		if(!$as_blog && $keywords_string)
			$as_blog = true;

		if($as_blog)
			balancer_board_blog::create($post, $keywords_string, $data);

		$topic->recalculate();

		return $post;
	}

	function blog_entry() { return bors_load('balancer_board_blog', $this->id()); }

	function titled_link_in_container($base_container = NULL)
	{
		if($base_container && $base_container->title() == $this->container()->title())
			$title = $this->nav_name();
		else
			$title = $this->title_in_container();

		return "<a href=\"{$this->url_in_container()}\">{$title}</a>";
	}

	function pure_titled_link()
	{
		$title = @$this->data['title'];
		if(empty($title))
			$title = $this->topic()->title();

		return "<a href=\"{$this->url_in_container()}\">{$title}</a>";
	}

	static function posts_preload($posts)
	{
		bors_objects_preload($posts, 'owner_id', 'balancer_board_user', 'owner');
	}

	function real_title()
	{
		if($this->title_raw())
			return $this->title_raw();

		return $this->topic()->title();
	}

	function _image_def()
	{
		return $this->__image(true);
	}

	function __image($use_topic_logo_if_not_found)
	{
		foreach($this->attaches() as $a)
			if(preg_match("!(jpe?g|png|gif)!i", $a->extension()))
				return $a->image();

		$obj = bors_find_first('balancer_board_posts_object', array(
			'inner_join' => array(
				'`AB_BORS`.`bors_images` i ON balancer_board_posts_object.target_object_id = i.id',
			),
			'target_class_id IN' => array(
				bors_foo('balancer_board_attach')->class_id(),
				202, // airbase_image
				bors_foo('balancer_board_image')->class_id(),
				bors_foo('airbase_image')->class_id(),
			),
			'post_id' => $this->id(),
			'`i`.extension<>"gif"',
			'order' => '-post_id,-modify_time',
		));

		if($obj)
		{
			$image = $obj->target();
			return $image;
		}

		return $use_topic_logo_if_not_found ? $this->topic()->image() : NULL;
	}

	function _image_url_def()
	{
		foreach($this->attaches() as $a)
			if(preg_match("!(jpe?g|png|gif)!i", $a->extension()))
				return $a->image_url();

		$obj = bors_find_first('balancer_board_posts_object', array(
//			'inner_join' => array(
//				'`AB_BORS`.`bors_images` i ON balancer_board_posts_object.target_object_id = i.id',
//			),
			'target_class_id IN' => array(
				bors_foo('balancer_board_attach')->class_id(),
				202, // airbase_image
				bors_foo('balancer_board_image')->class_id(),
				bors_foo('airbase_image')->class_id(),
				106, // bors_external_youtube
			),
			'post_id' => $this->id(),
//			'`i`.extension<>"gif"',
			'order' => 'post_id',
		));

		if($obj)
			if($image = $obj->target())
			{
				if($image_url = $image->get('image_url'))
					return $image_url;
				if($image_url = $image->get('url'))
					return $image_url;
			}

		return object_property($this->topic()->image(), 'url');
	}

	function full_recalculate_and_clean()
	{
		$this->set_modify_time(time(), true);

		config_set('lcml_cache_disable_full', true);
		$this->do_lcml_full_compile();
		$this->set_warning_id(NULL, true);
		$this->set_flag_db(NULL, true);
		if($owner = $this->owner())
			$owner->set_signature_html(NULL);

		$this->recalculate();
		$this->cache_clean();
		$this->store();
		$this->body();

		$topic = $this->topic();
		$topic->cache_clean();
		$topic->set_modify_time(time(), true);
		$topic->store();
	}

	function attaches_html()
	{
		if(!($attaches = $this->get('attaches')))
			return '';

		$html = "<div style=\"margin-top: 10px; clear: both; border-top: 1px dotted #ccc;\">";
		if($this->get('is_hidden'))
			$html .= lcml_tag_pair_spoiler::make(balancer_board_attach::show_attaches($this));
		else
			$html .= balancer_board_attach::show_attaches($this);

		$html .= "</div>";

		return $html;
	}

	function infonesy_uuid()
	{
		return 'ru.balancer.board.post.' . $this->id();
	}

	function infonesy_push()
	{
		if(!$this->is_public_access())
			return NULL;

		$this->topic()->infonesy_push();
		$this->owner()->infonesy_push();

		require_once 'inc/functions/fs/file_put_contents_lock.php';
		$storage = '/var/www/sync/airbase-forums-push';
//		$file = $storage.'/'.date('Y-m-d-H-i-s').'--post-'.$this->id().'.md';
		$file = $storage.'/'.$this->infonesy_uuid().'.md';

		$meta = [
			'UUID'		=> $this->infonesy_uuid(),
			'Node'		=> 'ru.balancer.board',
			'TopicUUID'	=> 'ru.balancer.board.topic.'.$this->topic_id(),
		];

		if($t = $this->title())
			$meta['Title'] = $t;

		$meta = array_merge($meta, [
			'Author'	=> $this->owner()->title(),
			'AuthorMD'	=> md5($this->owner()->email()),
			'AuthorEmailMD5'	=> md5($this->owner()->email()),
			'AuthorUUID'=> 'ru.balancer.board.user.'.$this->owner()->id(),
			'Date'		=> date('r', $this->create_time()),
			'Modify'	=> date('r', $this->modify_time()),
			'Type'		=> 'Post',
			'Markup'	=> 'lcml',
		]);

		if($a = $this->answer_to_id())
		{
			$meta['AnswerTo'] = 'ru.balancer.board.post.'.$a;
		}

		$attach_list = [];
		if($attaches = $this->attaches())
		{
			foreach($attaches as $a)
			{
				$hash = $a->infonesy_push();
				$attach_list[] = [
					'AttachUUID' => $a->infonesy_uuid(),
					'AttachIpfsHash' => $hash,
				];
			}

			$meta['Attaches'] = $attach_list;
		}

		$dumper = new Dumper();

		$typo = new \EMT\EMTypograph;

		$options = array(
			'Text.paragraphs'		=> 'off',
			'Text.breakline'		=> 'off',
			'Text.auto_links'		=> 'off',
			'Etc.unicode_convert'	=> 'on',
			'OptAlign.oa_oquote'	=> 'off',
			'OptAlign.oa_oquote_extra'	=> 'off',
			'OptAlign.oa_obracket_coma'	=> 'off',
		);

		$typo->setup($options);

		$typo->set_text($this->source());

		$md = "---\n";
		$md .= $dumper->dump(array_filter($meta), 2);
		$md .= "---\n\n";

//		$md .= $typo->apply()."\n";
//		$md .= "\n\n";

		$md .= trim($this->source())."\n";

		@file_put_contents_lock($file, $md);
		@chmod($file, 0666);

		return $file;
	}

	function infonesy_notify()
	{
		if(!$this->is_public_access())
			return NULL;

		$storage = '/var/www/sync/infonesy-common';

		$message = '*Тема: '.$this->topic()->title()."*\n\n"
			.\B2\Lcml::Lcml2Markdown($this->source())."\n\n"
			.$this->url_for_igo();

		$file = $storage.'/'.$this->infonesy_uuid().'.json';

		$data = [
			'UUID'		=> $this->infonesy_uuid(),
			'Node'		=> 'ru.balancer.board',
			'TopicUUID'	=> 'ru.balancer.board.topic.'.$this->topic_id(),
			'Author' => [
				'Title' => $this->owner()->title(),
			],
			'Message' => $message,
		];

		file_put_contents_lock($file, json_encode($data, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
		chmod($file, 0666);
	}

	function do_work_infonesy_push()
	{
		if($file = $this->infonesy_push())
			echo "{$this}: pushed to Infonesy as $file\n";
		else
			echo "{$this}: skip to pushed to Infonesy\n";

		$this->infonesy_notify();
	}

	function is_news()
	{
		return !$this->answer_to_id() && $this->topic()->is_news();
	}
}
