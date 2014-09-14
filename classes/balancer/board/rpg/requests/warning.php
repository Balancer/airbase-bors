<?php

class balancer_board_rpg_requests_warning extends bors_object
{
	function go()
	{
		$r = $this->id(); // request
		$warn_score = $r->request_id();
		$user = $r->target_user();
		$target = $r->target();

		$user->set_object_warning($target, $warn_score, $r->title());
//		r($user, $target->url_for_igo(), $warn_score, $r->title());
	}
}
