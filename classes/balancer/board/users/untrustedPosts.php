<?php

class balancer_board_users_untrustedPosts extends balancer_board_posts_list
{
	function title() { return ec('Сообщения потенциально опасных пользователей'); }
	function config_class() { return 'balancer_board_admin_config'; }
	function can_read() { return ($me=bors()->user()) ? $me->is_coordinator() : false; }

	function order() { return '-create_time'; }

	function where()
	{
		$newby = array_keys(bors_find_all('balancer_board_user', array('create_time>' => time()-86400*7, 'num_posts>' => 0,'by_id' => true)));
		$act_warns = array_keys(bors_find_all('balancer_board_user', array('warnings>' => 1, 'by_id' => true)));
//		$tot_warns = array_keys(bors_find_all('balancer_board_user', array('warnings_total>' => 30, 'by_id' => true)));

		$users = array_unique(array_merge($newby, $act_warns));

		return array_merge(parent::where(), array(
			'poster_id IN' => $users,
			'create_time>' => time()-86400,
			'warning_id<=' => 0,
		));
	}

	function pre_show()
	{
		template_noindex();
		return false;
	}

	function body_data()
	{
		return array_merge(parent::body_data(), array(
//			'show_title' => true,
		));
	}
}
