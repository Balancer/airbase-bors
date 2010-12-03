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
				$score = -1;
				break;
			case 'up':
				$score = +1;
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

		if($me->tomonth_posted() < 10)
			return "<small>У Вас слишком низкая активность на форумах</small>";

		if($score < 0 && $target->modify_time() < time() - 86400*14)
			return "<small>Отрицательные оценки можно ставить только для свежих сообщений</small>";

		$topic = $target->topic();
//		if($topic->modify_time() < time() - 86400*90)
		$topic->set_modify_time(time(), true);

		$vote = object_new_instance('bors_votes_thumb', array(
			'user_id' => $me_id,
			'target_class_name' => $target->class_name(),
			'target_object_id' => $target->id(),
			'target_user_id' => $target->owner_id(),
			'score' => $score,
		));

		$vote->store();
		$topic->cache_clean_self($target->topic_page());

		return $target->score_colorized(true);
	}
}
