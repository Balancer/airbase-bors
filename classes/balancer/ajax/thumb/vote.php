<?php

class balancer_ajax_thumb_vote extends base_object
{
	function object() { return $this->__havec('object') ? $this->__lastc() : $this->__setc(object_load($this->id())); }

	function content()
	{
		$target = $this->object();
		$me		= bors()->user();
		$me_id	= bors()->user_id();

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

		if(!$me_id)
			return "Только для зарегистрированных пользователей!";

		if($me->is_banned())
			return "Вы находитесь в режиме «только чтение»";

		if($me_id == $target->owner_id())
			return "<small>Нельзя ставить оценку себе!</small>";

		if(intval($score) < 0)
		{
			if($me->tomonth_posted() < 15)
				return "<small>У Вас слишком низкая активность на форумах</small>";

			if($target->modify_time() < time() - 86400*14)
				return "<small>Отрицательные оценки можно ставить только для свежих сообщений</small>";

			if($me->warnings() > 3)
				return bors_message(ec('У Вас более трёх активных штрафов. Вы можете ставить только положительные оценки'));

			$user_limit = $me->messages_daily_limit();
			if($user_limit > 0)
			{
				$user_limit = intval($user_limit / ($me->warnings()+1))+1;

				$today_user_negatives = bors_count('bors_votes_thumb', array(
					'score<' => 0,
					'user_id' => $me_id,
					'create_time>' => time() - 86400,
				));


				if($user_limit < $today_user_negatives)
				{
					debug_hidden_log('_vote_limits', "User votes limits stop. user_limit=$user_limit, today_user_negatives=$today_user_negatives", 1);
					return "<small>Вы исчерпали сегодняшний лимит отрицательных оценок [$user_limit]</small>";
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


//				debug_hidden_log('_test_limits', "User votes limits test. negatives=$tomonth_user_negatives, positives=$tomonth_user_positives", 1);
				if($tomonth_user_negatives > $tomonth_user_positives + 10)
				{
//					debug_hidden_log('_vote_limits', "User votes limits stop. negatives=$tomonth_user_negatives, positives=$tomonth_user_positives", 1);
					return "<small>Вы слишком озлоблены. Расслабьтесь и будьте добрее.</small>";
				}

			}
		}

		if($topic = $target->get('topic'))
		{
//			if($topic->modify_time() < time() - 86400*90)
			$topic->set_modify_time(time(), true);
//			$target->set_modify_time(time(), true);
		}

		$vote = bors_new('bors_votes_thumb', array(
			'user_id' => $me_id,
			'target_class_name' => $target->class_name(),
			'target_class_id' => $target->class_id(),
			'target_object_id' => $target->id(),
			'target_user_id' => $target->owner_id(),
			'score' => $score,
		));

		$vote->store();
		$topic->cache_clean_self($target->topic_page());

		$return = $target->score_colorized(true);

		$positives = objects_count('bors_votes_thumb', array(
			'score>' => 0,
			'target_class_name' => 'forum_post',
			'target_object_id' => $target->id(),
		));

		if(is_null($target->mark_best_date()))
		{
			if($positives >= 5)
			{
				$target->set_mark_best_date($vote->create_time(), true);
			}
		}

		if($target->score() >= 7)
			balancer_balabot::on_thumb_up($target);

		$user = $target->owner();
		$text = "Вам выставлена оценка $score за сообщение #{$target->id()} {$target->url_for_igo()} в теме «{$target->topic()->title()}»";

		$user->notify_text($text);

		bal_event::add('balancer_board_actor_vote', $user, $vote);

		return $return;
	}
}
