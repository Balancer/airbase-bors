<?php

class balancer_board_rpg_requests_approve extends bors_object
{
	var $auto_map = true;
	function pre_show()
	{
		$me = bors()->user();
		if(!$me)
			return bors_message('Только для зарегистрированных пользователей');

		$my_score = pow(3, $me->rpg_level());
		if($me->rpg_level() < 1)
			return bors_message('Ваш уровень недостаточен для голосования');

		$request = bors_load('balancer_board_rpg_request', bors()->request()->data_parse('int', 'rid'));

		$prev = bors_find_first('balancer_board_rpg_vote', [
			'request_id' => $request->id(),
			'user_id' => $me->id(),
//			'score',
		]);

		if($prev)
			return bors_message('Вы уже голосовали за этот запрос отдав '.abs($prev->score()).' баллов за '.($prev->score() > 0 ? 'подтверждение.' : 'отклонение'));

		$score = bors()->request()->data_parse('int', 'score');

		bors_new('balancer_board_rpg_vote', [
			'request_id' => $request->id(),
			'user_id' => $me->id(),
			'score' => $score * $my_score,
		]);

		$new_score = 0;
		foreach(bors_find_all('balancer_board_rpg_vote', ['request_id' => $request->id()]) as $v)
			$new_score += $v->score();

		if($new_score >= $request->need_score())
		{
			$x = bors_load($request->request_class_name(), $request);
			$x->go();
			$msg = 'Запрос подтверждён';
			$type = 'success';
		}
		else
		{
			$msg = 'Запрос ещё не подтверждён. Набрано '.$new_score.' баллов из '.$request->need_score();
			$type = 'notice';
		}

		$request->set_have_score($new_score);

//		echo 'ok';
//		return true;
		return go_ref_message($msg, ['type' => $type]);
	}
}
