<?php

class balancer_ajax_thumb_vote extends bors_object
{
	function object() { return $this->__havec('object') ? $this->__lastc() : $this->__setc(bors_load($this->id())); }

	function content()
	{
		$target = $this->object();
		$me		= bors()->user();
		$me_id	= bors()->user_id();

		if(!$me)
			return "Только для регистрированных пользователей";

		if(!$target)
		{
			bors_debug::syslog('vote_error', "Не найден объект ".$this->id());
			return "Не найден объект ".$this->id();
		}

		$target->score_colorized(true);
		switch($this->args('vote'))
		{
			case 'down':
				$score = '-1';
				break;
			case 'up':
				$score = '+1';
				break;
			default:
				return "Ошибка параметров";
		}

		if(!$target)
			return "Неизвестный объект";

		if(!bors()->request()->is_post())
		{
			if(bors()->request()->is_accept_text())
				return '<img src="http://www.airbase.ru/forum/smilies/hacker.gif" />. Нажмите Ctr-F5. <a href="http://www.balancer.ru/g/p3795989" target="_blank">подробности</a>';

			header("Content-type: " . image_type_to_mime_type(IMAGETYPE_GIF));
			return file_get_contents('http://www.airbase.ru/forum/smilies/hacker.gif');
		}

		if(!$me_id)
			return "Только для зарегистрированных пользователей!";

		if($me->is_banned())
			return "Вы находитесь в режиме «только чтение»";

		if($me->money() <= 0)
			return "У Вас недостаточно средств ☼ для оценки сообщений";

		if($me_id == $target->owner_id())
			return "<small>Нельзя ставить оценку себе!</small>";

		$twinks = max(1, $me->active_twinks_count());

		if($me->create_time() > time() - 86400)
			return "<small>Нельзя ставить оценки в первые сутки после регистрации</small>";

		if($me->tomonth_posted() < 5*$twinks)
			return "<small>У Вас слишком низкая активность на форумах</small>";

		if(intval($score) < 0)
		{
			if($me->tomonth_posted() < 15*$twinks)
				return "<small>У Вас слишком низкая активность на форумах</small>";

			if($target->modify_time() < time() - 86400*14)
				return "<small>Отрицательные оценки можно ставить только для свежих сообщений</small>";

			if($me->warnings() >= 3)
				return '<small>У Вас три или более активных штрафа. Вы можете ставить только положительные оценки</small>';

			$user_limit = $me->messages_daily_limit();

			if(!$user_limit && $me->warnings())
				$user_limit = 10;

			if($user_limit > 0)
			{
				$user_limit = intval($user_limit / ($me->warnings()+1))+1;

				$today_user_negatives = bors_count('bors_votes_thumb', array(
					'score<' => 0,
					'user_id' => $me_id,
					'create_time>' => time() - 86400,
				));

				if($user_limit < $today_user_negatives * $twinks)
				{
					bors_debug::syslog('_vote_limits', "User votes limits stop. user_limit=$user_limit, today_user_negatives=$today_user_negatives", 1);
					return "<small>Вы исчерпали сегодняшний лимит отрицательных оценок ($user_limit)</small>";
				}

				$tomonth_user_negatives = bors_count('bors_votes_thumb', array(
					'score<' => 0,
					'user_id' => $me_id,
					'create_time>' => time() - 86400*30,
				));

				$tomonth_user_positives = bors_count('bors_votes_thumb', array(
					'score>' => 0,
					'user_id' => $me_id,
					'create_time>' => time() - 86400*30,
				));

				bors_debug::syslog('_test_limits', "User votes month limits test. negatives=$tomonth_user_negatives, positives=$tomonth_user_positives", 1);
				if($tomonth_user_negatives > $tomonth_user_positives + 10/$twinks)
				{
					bors_debug::syslog('_vote_limits', "User votes angry limits stop. negatives=$tomonth_user_negatives, positives=$tomonth_user_positives", 1);
					return "<small>Вы слишком озлоблены. Расслабьтесь и будьте добрее.</small>";
				}
			}
		}

		if($topic = $target->get('topic'))
		{
//			if($topic->modify_time() < time() - 86400*90)
//			$topic->set_modify_time(time(), true);
//			$target->set_modify_time(time(), true);
		}

		$prev = bors_find_first('bors_votes_thumb', array(
			'user_id' => $me_id,
			'target_class_name' => $target->new_class_name(),
			'target_object_id' => $target->id(),
			'target_user_id' => $target->owner_id(),
		));

		if($prev)
		{
			if($score != $prev->score())
				$prev->delete();
			else
				return "<small>Вы уже выставили эту оценку</small>";

			$vote = $prev;
		}
		else
		{
			$vote = bors_new('bors_votes_thumb', array(
				'user_id' => $me_id,
				'target_class_name' => $target->new_class_name(),
				'target_class_id' => $target->class_id(),
				'target_object_id' => $target->id(),
				'target_user_id' => $target->owner_id(),
				'score' => $score,
			));

			$vote->store();
		}

		$target->set_warning_id(NULL, true);

		$topic->cache_clean_self($target->topic_page());
		$topic->set_modify_time(time(), true);
		$topic->store();

		$return = $target->score_colorized(true);

		$positives = bors_count('bors_votes_thumb', array(
			'score>' => 0,
			'target_class_name IN' => array('forum_post', 'balancer_board_post'),
			'target_object_id' => $target->id(),
		));

		if(is_null($target->mark_best_date()))
		{
			if($positives >= 5)
			{
				$target->set_mark_best_date($vote->create_time(), true);

				bors()->changed_save();

				$current_page = $target->db()->select('posts_cached_fields', 'MAX(`best_page_num`)', array());//bors_find_first('balancer_board_posts_cached', array('order' => 'MAX(best_page_num)'))->best_page_num();
				$last_count = bors_count('balancer_board_posts_cached', array('best_page_num' => $current_page));
				if($last_count >= 25)
					$current_page++;

				$target->set_best_page_num($current_page);
/*
				$target->db()->query("UPDATE posts_cached_fields AS c
					SET c.best_page_num = FLOOR((SELECT @rn:= @rn + 1 FROM (SELECT @rn:= -1) s)/25)+1
					WHERE mark_best_date IS NOT NULL
					ORDER BY mark_best_date;
				");
*/
			}
		}

		if(is_null($target->best10_ts()) && $positives >= 10)
				$target->set_best10_ts(time(), true);

		$target_score = intval($target->score());
		if($target_score >= 7)
			balancer_balabot::on_thumb_up($target);

		$user = $target->owner();
		$text = "Вам выставлена оценка $score за сообщение #{$target->id()} {$target->url_for_igo()} в теме «{$target->topic()->title()}»";
		$user->notify_text($text);

		bal_event::add('balancer_board_actor_vote', $user, $vote);

		$old_warning = bors_find_first('airbase_user_warning', array(
			'warn_class_id' => $target->class_id(),
			'warn_object_id' => $target->id(),
		));

		if(!$old_warning)
		{
			// Проверку на время не делаем, так как минусы итак только за две недели ставятся.
			if($score < 0 && $target_score <= -7)
			{
				balancer_board_rpg_request::factory('balancer_board_rpg_requests_warning')
					->set_user($user)
					->set_target($target)
					->set_title('Коллективный штраф за слишком низкий рейтинг сообщения')
					->set_level($user->rpg_level()+1)
					->add(intval(-$target_score/7));

				bors_debug::syslog('rpg-requests',
					"target score for warning =".$target_score."; "
					."target-data=".print_r($target->data, true));

				if(intval($target_score/7) >= 0)
					bors_debug::syslog('000-rpg-score-error',
						"target score for warning =".$target_score."; "
						."target-data=".print_r($target->data, true));
			}

			// Только для свежих сообщений, которым менее двух недель
			if($score > 0 && $target->create_time() > time() - 86400*14 && $target_score >= 15)
			{
				balancer_board_rpg_request::factory('balancer_board_rpg_requests_warning')
					->set_user($user)
					->set_target($target)
					->set_title('Коллективный поощрительный балл за высоко оценённое сообщение')
					->set_level(7) // 3×level6, 2187 баллов
					->add(intval(-$target_score/15));

				bors_debug::syslog('rpg-requests',
					"target score for award =".$target_score."; "
					."target-data=".print_r($target->data, true));

				if(intval($target_score/15) <= 0)
					bors_debug::syslog('000-rpg-score-error',
						"target score for award =".$target_score."; "
						."target-data=".print_r($target->data, true));
			}
		}

		if($score>0)
		{
		//	function add_money($amount, $action=NULL, $comment=NULL, $object=NULL, $source=NULL)
			$user->add_money(2, 'score_target', "Плюс за сообщение", $target, $me);
			$me->add_money(-1, 'score_pay', "Выставление оценки", $target, $user);
		}
		elseif($score<0)
		{
			$user->add_money(-1, 'score_target', "Минус за сообщение", $target, $me);
			$me->add_money(-2, 'score_pay', "Выставление оценки", $target, $user);
		}

		return $return;
	}
}
