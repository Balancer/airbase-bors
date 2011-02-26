<?php

class balancer_board_actor_user_vote extends bors_page
{
	function title()
	{
		return "Вы получили оценку {$this->arg('object')->score_html()} за {$this->arg('target')->object_titled_vp_link()}";
	}

	function text() { return NULL; }
}
