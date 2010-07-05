<?php

class balancer_board_users_untrustedPosts extends balancer_board_posts_list
{
	function title() { return ec('Сообщения потенциально опасных пользователей'); }
	function config_class() { return 'balancer_board_admin_config'; }
	function can_read() { return ($me=bors()->user()) ? $me->is_coordinator() : false; }

	function order() { return '-create_time'; }

	function where()
	{
		$newby = array_keys(objects_array('forum_user', array('create_time>' => time()-86400*7, 'num_posts>' => 0,'by_id' => true)));
		$act_warns = array_keys(objects_array('forum_user', array('warnings>' => 1, 'by_id' => true)));
//		$tot_warns = array_keys(objects_array('forum_user', array('warnings_total>' => 30, 'by_id' => true)));

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

	function local_data()
	{
		return array_merge(parent::local_data(), array(
//			'show_title' => true,
		));
	}
}
