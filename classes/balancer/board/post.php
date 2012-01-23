<?php

class balancer_board_post extends forum_post
{
	function extends_class_name() { return 'forum_post'; }

	function is_public_access() { return $this->topic() && $this->topic()->forum_id() && $this->topic()->forum()->is_public_access(); }

	function html($data = array())
	{
		$data = array_merge(array(
			'p' => $this,	//	сам постинг
			'forum' => $this->topic()->forum(), // форум, если нужно показать ссылку на него
//			'$no_show_answers = true, если не показывать ответы.
			'show_title' => true, // если нужна печать заголовка.
//$skip_author_name
//	$skip_avatar_block
//	$skip_date
//	$skip_forums - не отображать название форума
			'skip_votes' => true, //  - не рисовать иконки в заголовке
			'skip_message_footer' => true,
			'strip' => 1024, // - сколько резать символов.
		), $data);

		return bors_templates_smarty::render_data('xfile:/var/www/bors/bors-airbase/templates/forum/post.html', $data);
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

	function answers_count($recount = false)
	{
		if(!$recount && !is_null($this->answers_count_raw()))
			return $this->answers_count_raw();

		$summ = 0;
		foreach($this->direct_answers() as $a)
			$summ += $a->answers_count($recount) + 1;

		debug_hidden_log('__answers', "{$this->debug_title}=$summ");
		return $this->set_answers_count_raw($summ);
	}

	function parents_answers_recount($set = false)
	{
//		echo "par({$this})\n";
		if($set !== false)
			$this->set_answers_count_raw($set);

		if($parent = bors_load('balancer_board_post', $this->answer_to_id()))
		{
//			echo "par={$parent}\n";
			$parent->answers_count(false);
			$parent->parents_answers_recount();
		}
	}

	static function create($topic, $message, $user, $keywords_string = NULL, $as_blog = NULL, $data = array())
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

	function titled_url_in_container($base_container = NULL)
	{
		if($base_container && $base_container->title() == $this->container()->title())
			$title = $this->nav_name();
		else
			$title = $this->title_in_container();

		return "<a href=\"{$this->url_in_container()}\">{$title}</a>";
	}
}
