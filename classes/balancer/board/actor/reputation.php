<?php

class balancer_board_actor_reputation extends bors_page
{
	function public_title()
	{
		$rep = $this->args('object');
		$target_user = $rep->target_user();
		$owner = $rep->owner();
		$target = $rep->target();
		return "Пользователь {$target_user->titled_link()} получил репутационный голос {$rep->score_html()} от {$owner->titled_link()} за {$target->object_titled_vp_link()}";
	}

	function personal_title()
	{
		$rep = $this->args('object');
		$target_user = $rep->target_user();
		$owner = $rep->owner();
		$target = $rep->target();
		$text = "Вы получили репутационный голос {$rep->score_html()} от {$owner->titled_link()}";
		if($target)
			$text .= " за {$target->object_titled_vp_link()}";

		return $text;
	}

	function public_text() { return NULL; }
	function personal_text() { return NULL; }

	function color()
	{
		$rep = $this->args('object');
		return $rep->score() > 0 ? 'reputation_up' : 'reputation_down';
	}
}
