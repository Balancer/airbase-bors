<?php

class balancer_board_user extends forum_user
{
	function extends_class_name() { return 'forum_user'; }

	/**
		Расчёт репутации в диапазоне -100 .. 100
	*/

	function reputation_percents()
	{
		$reputation_value = $this->reputation(); // абсолютное значение репутации, от -∞ до +∞

		// Репутация в диапазоне -100..100
		$rep = abs(200*atan($reputation_value*$reputation_value/($reputation_value >= 0 ? 300 : 100))/pi());
		if($reputation_value < 0)
			$rep = -$rep;
		return $rep;
	}

	function reputation_html()
	{
//		return "<img src=\"http://www.balancer.ru/user/{$this->id()}/rep.gif\" class=\"rep\" alt=\"\" />";

		$reputation = $this->reputation_percents();

		// Нормируем в диапазоне [0,5] с шагом 0,5
		$stars_count = 0.5+round(abs($reputation)*9.5/100)/2;

		if(!$stars_count)
			return '';

//		☆★
		$stars = str_repeat('★', $full_stars = intval($stars_count));
		if($full_stars != $stars_count)
		{
			if($reputation >= 0)
				$stars .= '☆';
			else
				$stars = '☆'.$stars;
		}

		if(!$stars)
			return '';

		if($reputation >= 0)
			return "<span class=\"rep\" style=\"color: gold\">{$stars}</span>";
		else
			return "<span class=\"rep rot180\" style=\"color: gray\">{$stars}</span>";
	}

	function warnings_html()
	{
		$warnings = $this->warnings();

		if(is_object($this->is_banned()))
			return "<span style=\"color: red; font-size: 7pt\">админ. бан</span>";

		if(!$warnings)
			return '';

		if($warnings >= 10)
		{
			$db = new driver_mysql('AB_FORUMS');
			$total = 0;
			$time  = 0;
			foreach($db->get_array("SELECT score, time FROM warnings WHERE user_id = {$this->id()} ORDER BY time DESC LIMIT 20") as $w)
			{
				$total += $w['score'];
				if($total >= 10)
				{
					$time = $w['time'];
					break;
				}
			}

			return "<span style=\"color: red; font-size: 7pt\">бан до ".strftime("%d.%m.%Y", $this->expired = $time+WARNING_DAYS*86400)."</span>";
		}

//		☠😸🐱❤
		$skulls = str_repeat('☠', $full_skulls = intval($warnings/2));
		if($full_skulls*2 != $warnings)
			$skulls .= '<span style="color:#999">☠</span>';

		if(!$skulls)
			return '';

		return "<span class=\"warn\" style=\"margin:0; padding:0; color: black\">{$skulls}</span>";
	}

	function avatar() { return balancer_board_avatar::make($this->id()); }

	function old_avatar_image()
	{
		$file = $this->use_avatar();
		if(!$file)
			return NULL; //TODO: и тут тоже нужно приделывать граватары всякие

		//FIXME: хардкодный путь к аватарам
		$full_path = '/var/www/balancer.ru/htdocs/forum/punbb/img/avatars/'.$file;

		if(!file_exists($full_path))
			return NULL; //TODO: и тут тоже нужно приделывать граватары всякие

		$image = bors_image::register_file($full_path);
		// Ссылка у нас единая. Кстати...
		//FIXME: тут тоже подумать на тему настроек
		$image->set_full_url('http://s.wrk.ru/a/'.$file, true);
		$image->set_relative_path(NULL, true);
		$image->wxh();
		bors()->changed_save();

		return $image;
	}


	static function find_by_all_names($name)
	{
		if($user = bors_find_first(__CLASS__, array('title' => $name)))
			return $user;

		if($user = bors_find_first(__CLASS__, array('realname' => $name)))
			return $user;

		return NULL;
	}

	function notify_text($text)
	{
		$client= new GearmanClient();
		$client->addServer();
		if($this->jabber())
		{
//			debug_hidden_log('balabot_talks', "Notify to {$this->jabber()} <= $text", false);
			$client->doBackground('balabot.jabber.send', serialize(array('to' => $this->jabber(), 'message' => htmlspecialchars($text))));
		}
	}

	function email_text($text)
	{
		if($this->last_mailing() > time() - 3*24*3600)
		{
//			debug_hidden_log('_answer_test_mailing', "Skip notify to {$this->debug_title()} ({$this->email()}) as |$text|", false);
			return;
		}

		if($this->has_invalid_email())
			return;

		$client= new GearmanClient();
		$client->addServer();

		$this->set_last_mailing(time(), true);

		$client->doBackground('balabot.work', serialize(array(
			'to' => $this->email(),
			'text' => $text,
			'worker_class_name' => 'balancer_board_tasks',
			'worker_method'     => 'send_email',
		)));
	}

	function friend_action_notify($user_id, $text, $html = NULL)
	{
		$friend_binds = bors_find_all('balancer_board_users_friend', array('friend_id' => $user_id));
		if(!$friend_binds)
			return;

		foreach(bors_find_all(__CLASS__, array('id IN' => bors_field_array_extract($friend_binds, 'user_id'))) as $u)
			$u->notify_text($text);
	}

	function _warnings_update()
	{
		// SET @t = UNIX_TIMESTAMP()-14*86400;
		// UPDATE users u SET u.warnings = (SELECT sum(score) FROM warnings w WHERE w.user_id = u.id AND `time` > @t);
		$warnings_active = bors_find_first('airbase_user_warning', array(
			'*set' => 'SUM(score) as total',
			'user_id' => $this->id(),
			'`expired_timestamp` > NOW()',
		));

		$warnings_total = bors_find_first('airbase_user_warning', array(
			'*set' => 'SUM(score) as total',
			'user_id' => $this->id(),
		));

		$this->set_warnings($warnings_active->total(), true);
		$this->set_warnings_total($warnings_total->total(), true);
	}

	static function me_group()
	{
		$me = bors()->user();
		if($me)
			return $me->group();

		return bors_load('balancer_board_group', PUN_GUEST);
	}

	function set_object_warning($object, $score, $message, $moderator = NULL, $type = 0, $referer = NULL)
	{
		$warn = bors_find_first('airbase_user_warning', array(
			'user_id' => $this->id(),
			'warn_class_id' => $object->class_id(),
			'warn_object_id' => $object->id(),
		));

		if($warn)
		{
			if($warn->moderator_id() < 1 && $warn->score() < $score)
			{
				$warn->set_score($score);
				$warn->set_source($message);
			}

			return;
		}

		$data = array(
			'user_id' => $this->id(),
			'expire_time' => time() + WARNING_DAYS*86400,
			'score_db' => $score,
			'type_id' => $type,
			'referer' => $referer,
			'source' => $message,
		);

		if($object)
		{
			$data['warn_class_id'] = $object->class_id();
			$data['warn_object_id'] = $object->id();
			$data['expire_time'] = $object->create_time() + WARNING_DAYS*86400;
		}

		if($moderator)
		{
			$data['moderator_id'] = $moderator->id();
			$data['moderator_name'] = $moderator->title();
		}
		else
		{
			$data['moderator_id'] = 0;
			$data['moderator_name'] = 'БалаБОТ';
		}
/*
		if(config('is_developer'))
		{
			var_dump($data);
			exit();
		}
*/
		$warn = bors_new('airbase_user_warning', $data);
	}
}
