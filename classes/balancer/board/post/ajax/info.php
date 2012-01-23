<?php

require_once('inc/clients/geoip-place.php');

class balancer_board_post_ajax_info extends balancer_board_ajax
{
	function post() { return $this->__havefc() ? $this->__lastc() : $this->__setc(bors_load('balancer_board_post', $this->id())); }

	function pre_show()
	{
		if(!bors()->user_id())
			return "Только для зарегистрированных пользователей!";

		return false;
	}

	function body_data()
	{
		$p = $this->post();

		return array(
			'post' => $p,
		);
	}
}
