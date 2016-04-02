<?php

class balancer_board_personal_main extends balancer_board_page
{
	var $nav_name_ec = 'личный кабинет';
	var $can_action_method_get = true;

	function title() { return _('Персональный раздел'); }
	function auto_map() { return true; }
	function nav_name() { return _('персональное'); }
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

	function can_action($action) { return $action == 'delete' || $action == 'undelete'; }

	function on_action_delete($data)
	{
//		var_dump(bors()->user_id(), $data['uid']); exit();
		balancer_board_users_relation::set_ignore(bors()->user_id(), $data['uid']);
		return go_ref_message("Все сообщения пользователя удалены");
	}

	function on_action_undelete($data)
	{
		balancer_board_users_relation::unset_ignore(bors()->user_id(), $data['uid']);
		return go_ref_message("Сообщения пользователя восстановлены из архива");
	}
}
