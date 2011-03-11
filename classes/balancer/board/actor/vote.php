<?php

class balancer_board_actor_vote extends bors_page
{
	function public_title()
	{
		return "Пользователь {$this->args('target_user')->title()} получил оценку {$this->arg('object')->score_html()} за {$this->arg('target')->object_titled_vp_link()}";
	}

	function personal_title()
	{
		return "Вы получили оценку {$this->arg('object')->score_html()} за {$this->arg('target')->object_titled_vp_link()}";
	}

	function public_text() { return NULL; }
	function personal_text() { return NULL; }
}
