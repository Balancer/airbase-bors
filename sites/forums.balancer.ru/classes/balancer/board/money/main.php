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

		$target_user->add_money($amount,
			'move_from',
			"Перевод средств от пользователя ".$target_user->title(),
			NULL /*object*/,
			$me);

		$me->add_money(-$amount-1,
			'move_to',
			"Перевод средств пользователю ".$target_user->title(),
			NULL /*object*/,
			$target_user);


		return go_message('Вы успешно перевели ☼'.$amount
				.' пользователю '.$target_user->title()
				.' и его баланс теперь составляет ☼'.$target_user->money(),
			['go' => '/money/', 'type' => 'success']);
	}

	function on_action_award($data)
	{
		$price = 500;

		extract($data);

		$target_user = bors_load('balancer_board_user', $target_user_id);
		if(!$target_user)
			return bors_message('Пользователь не найден');

		$me = bors()->user();

		if($amount <= 0)
			return bors_message('Сумма баллов должна быть положительной');

		if($amount > 1000000)
			return bors_message('Да ну вас, хакеров...');

		if($price*$amount > $me->money())
			return bors_message('У Вас недостаточно средств: '.$me->money().' при необходимых '.($price*$amount));

		$text = "Поощрительный балл от пользователя ".$me->title();
		if(!empty($comment))
			$text .= ": ".$comment;

		$target_user->set_object_warning(NULL, -$amount, $text, $me);
		$me->add_money(-$amount*$price,
			'award_to',
			"Конвертация средств в поощрительные баллы пользователя ".$target_user->title(),
			NULL /*object*/,
			$target_user);

		bors()->changed_save();

		return go_message('Вы успешно выставили пользователю '.$target_user->title().' '
				.$amount.' поощрительных баллов.',
			['go' => '/money/', 'type' => 'success']);
	}
}
