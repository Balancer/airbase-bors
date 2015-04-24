<?php

require_once('inc/clients/geoip-place.php');

class balancer_board_users_interlocutors extends balancer_board_page
{
	function can_be_empty() { return false; }
	function is_loaded() { return $this->user() != NULL && bors()->user() && (bors()->user()->is_watcher() || bors()->user()->is_admin()); }

	function title() { return $this->user()->title().ec(": дополнительная информация"); }
	function nav_name() { return 'дополнительно'; }

	function parents()
	{
		return array("http://www.balancer.ru/users/".$this->id().'/');
	}

	function url() { return "http://forums.balancer.ru/users/".$this->id()."/interlocutors/"; }

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

		$db = new driver_mysql(config('punbb.database'));

		$is_watcher = bors()->user()->is_watcher() || bors()->user()->is_admin();
		if(bors()->user() && $is_watcher)
		{
			$interlocutors = $db->select_array('posts', 'poster_id, COUNT(*) as answers_count', array(
				'is_deleted' => false,
				'posted>' => time() - 365*86400,
				'answer_to_user_id' => $this->id(),
				'group' => 'poster_id',
//				'order' => 'COUNT(*) DESC',
			));
			$interlocutor_stats = array();
			foreach($interlocutors as $x)
				$interlocutor_stats[$x['poster_id']] = $x['answers_count'];
//var_dump($interlocutor_stats);
			$interlocutors = bors_find_all('balancer_board_user', array('id IN' => array_keys($interlocutor_stats)));

			foreach($interlocutors as $x)
			{
				$x->set_answers($interlocutor_stats[$x->id()], false);
			}

			usort($interlocutors, create_function('$x, $y', 'return $y->answers() - $x->answers();'));

			$last_ips = $db->select_array('posts', 'poster_ip, COUNT(*) AS count', array(
				'poster_id' => $this->id(),
				'posted>' => time()-30*86400,
				'group' => 'poster_ip',
				'order' => 'MAX(posted) DESC',
			));
		}
		else
		{
			$last_ips = false;
			$interlocutors = false;
			$interlocutor_stats = false;
		}

		return array_merge(parent::body_data(), array(
			'user' => $user,
		),  compact(
			'is_watcher',
			'interlocutors',
			'interlocutor_stats',
			'last_ips'
		));
	}
}
