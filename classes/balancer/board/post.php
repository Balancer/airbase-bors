<?php

class balancer_board_post extends forum_post
{
	function extends_class() { return 'forum_post'; }

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
		));
	}

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
//		echo "answers_count({$this})\n";
		if(!$recount && !is_null($this->answers_count_raw()))
			return $this->answers_count_raw();

		$summ = 0;
		foreach($this->direct_answers() as $a)
		{
//			echo "sum{$a}\n";
			$summ += $a->answers_count($recount) + 1;
		}

		return $this->set_answers_count_raw($summ, true);
	}

	function parents_answers_recount($set = false)
	{
//		echo "par({$this})\n";
		if($set !== false)
			$this->set_answers_count_raw($set, true);

		if($parent = bors_load('balancer_board_post', $this->answer_to_id()))
		{
//			echo "par={$parent}\n";
			$parent->answers_count(false);
			$parent->parents_answers_recount();
		}
	}

	function create($topic_id, $message, $user, $keywords_string = NULL, $as_blog = NULL)
	{
//		echo "Pass post to $topic_id\n";
		$post = object_new_instance(__CLASS__, array(
			'author_name' => $user->title(),
			'owner_id' => $user->id(),
			'poster_ip' => bors()->client()->ip(),
			'poster_ua' => bors()->client()->agent(),
			'poster_email' => NULL, //($pun_config['p_force_guest_email'] == '1' || $email != '') ? $email : '',
			'hide_smilies' => false, //$hide_smilies, 
			'topic_id' => $topic_id,
			'answer_to_id' => NULL,
			'answer_to_user_id' => NULL,
			'source' => $message,
		));

		if(!$as_blog && $keywords_string)
			$as_blog = true;

		$topic = $post->topic();

		if($as_blog)
		{
			$blog = object_new_instance('balancer_board_blog', array(
				'id' => $post->id(),
				'owner_id' => $post->owner_id(),
				'topic_id' => $topic_id,
				'forum_id' => $topic->forum_id(),
				'is_public' => $topic->is_public(),
			));
			if($keywords_string)
				$blog->set_keywords_string($keywords_string, true);

			common_keyword_bind::add($blog);
		}

		$topic->recalculate();

		return $post;
	}
}
