<?php

class balancer_ajax_thumb_vote extends base_object
{
	function object() { return $this->__havec('object') ? $this->__lastc() : $this->setc(object_load($this->id())); }

	function content()
	{
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

		if(!$this->object())
			return "Неизвестный объект";

		if(!bors()->user_id())
			return "Только для зарегистрированных пользователей!";

		if(bors()->user()->is_banned())
			return "Вы находитесь в режиме «только чтение»";

		if(bors()->user_id() == $this->object()->owner_id())
			return "<small>Нельзя ставить оценку себе!</small>";

		$vote = object_new_instance('bors_votes_thumb', array(
			'user_id' => bors()->user_id(),
			'target_class_name' => $this->object()->class_name(),
			'target_object_id' => $this->object()->id(),
			'target_user_id' => $this->object()->owner_id(),
			'score' => $score,
		));
		
		$vote->store();

		return $this->object()->score_colorized(true);
	}
}
