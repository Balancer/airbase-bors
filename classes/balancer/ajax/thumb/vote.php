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
			$twinks = max(1, $me->active_twinks_count());
			if($me->tomonth_posted() < 15*$twinks)
				return "<small>У Вас слишком низкая активность на форумах</small>";

			if($me->create_time() > time() - 86400)
				return "<small>Нельзя ставить отрицательные оценки в первые сутки после регистрации</small>";

			if($target->modify_time() < time() - 86400*14)
				return "<small>Отрицательные оценки можно ставить только для свежих сообщений</small>";

			if($me->warnings() > 3)
				return bors_message(ec('У Вас более трёх активных штрафов. Вы можете ставить только положительные оценки'));

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
					debug_hidden_log('_vote_limits', "User votes limits stop. user_limit=$user_limit, today_user_negatives=$today_user_negatives", 1);
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

				debug_hidden_log('_test_limits', "User votes month limits test. negatives=$tomonth_user_negatives, positives=$tomonth_user_positives", 1);
				if($tomonth_user_negatives > $tomonth_user_positives + 10/$twinks)
				{
					debug_hidden_log('_vote_limits', "User votes angry limits stop. negatives=$tomonth_user_negatives, positives=$tomonth_user_positives", 1);
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
				if($count >= 25)
					$current_page++;

//				if(config('is_developer')) var_dump($current_page, $last_count);

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

		$target_score = $target->score();
		if($target_score >= 7)
			balancer_balabot::on_thumb_up($target);

		$user = $target->owner();
		$text = "Вам выставлена оценка $score за сообщение #{$target->id()} {$target->url_for_igo()} в теме «{$target->topic()->title()}»";
		$user->notify_text($text);

		bal_event::add('balancer_board_actor_vote', $user, $vote);

//		if(config('is_developer')) var_dump($score, $target_score);

		if($score < 0 && $target_score <= -5)
			$user->set_object_warning($target, intval(-$target_score/5), 'Автоматический штраф за слишком низкий рейтинг сообщения.');

		if($score > 0 && $target_score >= 15 && $target->create_time() > time() - 86400*14)
			$user->set_object_warning($target, intval(-$target_score/15), 'Автоматический поощрительный балл за высоко оценённое сообщение.');

		return $return;
	}
}
