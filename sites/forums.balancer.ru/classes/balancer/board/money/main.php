<?php

class balancer_board_money_main extends balancer_board_page
{
	var $title = 'Операции с Вашими накоплениями';
	var $nav_name = 'операции';

	function can_action() { return (bool) bors()->user(); }

	function on_action_move($data)
	{
		extract($data);

		$target_user = bors_load('balancer_board_user', $target_user_id);
		if(!$target_user)
			return bors_message('Пользователь не найден');

		$me = bors()->user();

		if($amount <= 0)
			return bors_message('Сумма должна быть положительной');

		if($amount >= $me->money())
			return bors_message('У Вас недостаточно средств');

		$target_user->add_money($amount);
		$me->add_money(-$amount-1);

		return go_message('Вы успешно перевели ☼'.$amount
				.' пользователю '.$target_user->title()
				.' и его баланс теперь составляет ☼'.$target_user->money(),
			['go' => '/money/', 'type' => 'success']);
	}

	function on_action_award($data)
	{
		extract($data);

		$target_user = bors_load('balancer_board_user', $target_user_id);
		if(!$target_user)
			return bors_message('Пользователь не найден');

		$me = bors()->user();

		if($amount <= 0)
			return bors_message('Сумма баллов должна быть положительной');

		if(500*$amount > $me->money())
			return bors_message('У Вас недостаточно средств: '.$me->money().' при необходимых '.(500*$amount));

		$text = "Поощрительный балл от пользователя ".$me->title();
		if(!empty($comment))
			$text .= ": ".$comment;

		$target_user->set_object_warning(NULL, -$amount, $text, $me);

		$me->add_money(-$amount*500);

		return go_message('Вы успешно выставили пользователю '.$target_user->title().' '
				.$amount.' поощрительных баллов.',
			['go' => '/money/', 'type' => 'success']);
	}
}
