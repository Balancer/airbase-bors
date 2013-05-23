<?php

class balancer_board_personal_main extends balancer_board_page
{
	var $nav_name_ec = 'личный кабинет';

	function title() { return ec('Персональный раздел'); }
	function is_auto_url_mapped_class() { return true; }
	function nav_name() { return ec('персональное'); }
	function template() { return 'forum/_header.html'; }

	function pre_show()
	{
		if(!bors()->user_id())
			return bors_message('Вы не авторизованы. Этот раздел доступен только для зарегистрированных пользователей');

		return parent::pre_show();
	}

	function body_data()
	{
		$me = bors()->user();
		if(($uid = bors()->request()->data('uid')) && $me->is_admin())
			$me = bors_load('balancer_board_user', $uid);

		return array_merge(parent::body_data(), array(
			'me_id' => bors()->user_id(),
			'events' => bors_find_all('bal_event', array(
				'user_class_id' => $me->class_id(),
				'user_id' => $me->id(),
				'order' => '-create_time',
				'limit' => 20,
			))
		));
	}
}
