<?php

require_once('inc/clients/geoip-place.php');

class balancer_board_users_ips extends balancer_board_page
{
	function can_be_empty() { return false; }
	function is_loaded() { return $this->user() != NULL && bors()->user() && (bors()->user()->is_watcher() || bors()->user()->is_admin()); }

	function title() { return $this->user()->title().ec(": IP сообщений"); }
	function nav_name() { return 'IP'; }

	function parents()
	{
		return array("http://www.balancer.ru/users/".$this->id().'/');
	}

	function url() { return "http://forums.balancer.ru/users/".$this->id()."/ips/"; }

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), array(
			'user' => 'balancer_board_user(id)',
		));
	}

	function body_data()
	{
		$user = $this->user();
		$user->set_reg_geo_ip(geoip_place($user->registration_ip()), false);

		$last_ips = bors_find_all('balancer_board_posts_pure', [
			'*set' => 'COUNT(*) AS count, MIN(posted) AS first_posted, MAX(posted) AS last_posted',
			'poster_id' => $this->id(),
			'posted>' => time()-30*86400,
			'group' => 'poster_ip',
			'order' => 'MAX(posted) DESC',
		]);

		return array_merge(parent::body_data(), array(
			'user' => $user,
			'last_ips' => $last_ips,
		));
	}
}
