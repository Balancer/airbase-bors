<?php

class balancer_board_rpg_requests_approve extends bors_object
{
	var $auto_map = true;
	function pre_show()
	{
		// Если запрос картинки, то это вызов subaction через img src, т.е. грубый XSS
		if(bors()->request()->is_accept_image())
		{
			bors_debug::syslog('hack-attempt', "Try to hack by img src for ".$this->debug_title());
			header("Content-type: " . image_type_to_mime_type(IMAGETYPE_GIF));
			return file_get_contents(BORS_CORE.'/htdocs/_bors/images/hacker.gif');
		}

		// Если запрашивается не страница, а не пойми чего, то тоже считаем за хак.
		if(!bors()->request()->is_accept_text())
		{
			bors_debug::syslog('hack-attempt', "Try to hack by call as not page for ".$this->debug_title());
			return get_class($this) . ": request error: act not page";
		}

		// Осторожнее с явным разрешением!
		if(!bors()->request()->is_post() && !$this->get('can_action_method_get'))
		{
			bors_debug::syslog('hack-attempt', "Try to hack by call get method for ".$this->debug_title());
			return get_class($this) . ": request error: act get";
		}

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

//		if($prev)
//			return bors_message('Вы уже голосовали за этот запрос отдав '.abs($prev->score()).' баллов за '.($prev->score() > 0 ? 'подтверждение.' : 'отклонение'));

		if($prev)
			$prev->delete();

		$score = bors()->request()->data_parse('int', 'score');

		if($score)
		{
			bors_new('balancer_board_rpg_vote', [
				'request_id' => $request->id(),
				'user_id' => $me->id(),
				'score' => $score * $my_score,
			]);
		}

		$new_score = 0;
		foreach(bors_find_all('balancer_board_rpg_vote', ['request_id' => $request->id()]) as $v)
			$new_score += $v->score();

		if($prev && $score == 0)
		{
			$msg = 'Ваш голос был отозван';
			$type = 'success';
		}

		if(!$prev && $score == 0)
		{
			$msg = 'Вы не голосовали, отзывать нечего';
			$type = 'notice';
		}

		if($score > 0)
		{
			$msg = 'Ваш голос «за» засчитан';
			$type = 'success';
		}

		if($score < 0)
		{
			$msg = 'Ваш голос «против» засчитан';
			$type = 'success';
		}

		if($request->request_class_name())
		{
			if($new_score >= $request->need_score())
			{
				$x = bors_load($request->request_class_name(), $request);
				$x->go();
				$msg = 'Запрос набрал необходимое число баллов и был утверждён';
				$type = 'success';
			}
			else
			{
				$msg = 'Запрос ещё не подтверждён. Набрано '.$new_score.' баллов из '.$request->need_score();
				$type = 'notice';
			}
		}

		$request->set_have_score($new_score);

//		echo 'ok';
//		return true;
		return go_ref_message($msg, ['type' => $type]);
	}
}
