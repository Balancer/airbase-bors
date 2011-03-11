<?php

class balancer_board_actor_reputation extends bors_page
{
	function public_title()
	{
		return "Пользователь {$this->args('target_user')->title()} получил репутационный голос {$this->arg('object')->score_html()} от {$this->args('actor_user')->title()} за {$this->arg('target')->object_titled_vp_link()}";
	}

	function personal_title()
	{
		return "Вы получили репутационный голос {$this->arg('object')->score_html()} от {$this->args('actor_user')->title()} за {$this->arg('target')->object_titled_vp_link()}";
	}

	function public_text() { return NULL; }
	function personal_text() { return NULL; }
}
