<?php

class balancer_board_actor_vote extends bors_page
{
	function public_title()
	{
		$vote = $this->args('object');
		$user = $vote->target_user();
		$target = $vote->target();
		return "Пользователь {$user->titled_link()} получил оценку {$vote->score_html()} за {$target->object_titled_vp_link()}";
	}

	function personal_title()
	{
		$vote = $this->args('object');
		$user = $vote->target_user();
		$target = $vote->target();
		return "Вы получили оценку {$vote->score_html()} за {$target->object_titled_vp_link()}";
	}

	function public_text() { return NULL; }
	function personal_text() { return NULL; }

	function color()
	{
		$vote = $this->args('object');
		return $vote->score() > 0 ? 'green' : 'red';
	}
}
