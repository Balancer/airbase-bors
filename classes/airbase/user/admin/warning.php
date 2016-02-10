<?php

class airbase_user_admin_warning extends airbase_user_warning
{
	function access_engine() { return 'airbase_user_admin_access'; }

	function check_data(&$data)
	{
		$user = bors_load('bors_user', $data['user_id']);
		if(bors()->user()->warnings() > 0)
			return bors_message(ec('При наличии активных штрафов нельзя штрафовать других пользователей'));

//		if(in_array($user->group_id(), array(1,2,5,21)))
//			return bors_message(ec('Нельзя выставлять штрафы координаторам и модераторам'));

		$object = bors_load_uri($data['object']);
		if(!$object)
			return bors_message(ec('Неизвестный объект ').$data['object']);

		if($data['user_id'] != $object->owner_id())
			return bors_message("Попытка выставить штраф пользователю [{$data['user_id']}], не являющемуся автором сообщения [{$object->owner_id()}]");

		$previous_warning = bors_find_first('airbase_user_warning', array(
			'moderator_id>' => 0,
			'user_id' => $data['user_id'],
			'warn_class_id' => $object->class_id(),
			'warn_object_id' => $object->id(),
		));

		if($previous_warning)
			return bors_message(ec('Пользователь уже получил предупреждение за это сообщение'));

		$data['score'] = airbase_user_warning_type::score($data['type_id']);
		$data['moderator_id'] = bors()->user()->id();
		$data['moderator_name'] = bors()->user()->title();
		$data['warn_class_id'] = $object->class_id();
		$data['warn_object_id'] = $object->id();
		$data['create_time'] = time();
		$data['expire_time'] = $object->create_time() + WARNING_DAYS*86400;

		return parent::check_data($data);
	}

	function check_value_conditions()
	{
		return array(
			'warn_class_id'         => ec("!=0|Неизвестное сообщение"),
			'type_id'     => ec("!=0|Не указана причина предупреждения"),
		);
	}

	function on_new_instance(&$data)
	{
		$uid = $data['user_id'];
		$user = bors_load('balancer_board_user', $uid);
		$user->_warnings_update();
		$user->cache_clean();
		bors_load('users_topwarnings')->cache_clean();

		$object = bors_load($data['object']);
		$object->set_warning_id(NULL, true);
		$object->set_modify_time(time());

		$score = $data['score'];

		if($score > 0)
		{
			$moderator = $this->moderator();
			$user->add_money(-100*$score,
				'warnings',
				"Списание за штраф ($score от {$moderator->title()})",
				$object,
				$moderator);
		}

		@unlink('/var/www/balancer.ru/htdocs/user/'.$uid.'/warnings.gif');
		if($object->class_name() == 'forum_post' || $object->class_name() == 'balancer_board_post')
		{
			$topic = $object->topic();
			balancer_board_action::add($topic, "Предупреждение пользователю: {$object->nav_named_link()}", true);
		}

		$object->cache_clean();
		$object->call('recalculate');

		if($topic = $object->get('topic'))
		{
			$topic->cache_clean();
			$topic->set_modify_time(time());
			$topic->store();
		}
	}

	function delete()
	{
		$user = bors_load('balancer_board_user', $this->user_id());
		$ret = parent::delete();
		bors()->changed_save();

		if($object->extends_class_name() == 'forum_post')
		{
			$topic = $object->topic();
			balancer_board_action::add($topic, "Отмена предупреждения пользователю {$user->title()}: {$object->nav_named_link()}", true);
		}

		$warnings = $this->db()->select('warnings', 'SUM(score)', array('user_id=' => $uid, '`expire_timestamp` > NOW()'));
		$warnings_total = $this->db()->select('warnings', 'SUM(score)', array('user_id=' => $uid));
		$user->set_warnings($warnings - $data['score'], true);
		$user->set_warnings_total($warnings_total - $data['score'], true);
		$user->cache_clean();
		bors_load('users_topwarnings')->cache_clean();

		$object = bors_load($this->warn_class_id(), $this->warn_object_id());
		$object->set_warning_id($this->id(), true);

		$object->set_modify_time(time());
		$object->call('recalculate');
		@unlink('/var/www/balancer.ru/htdocs/user/'.$uid.'/warnings.gif');
		return $ret;
	}
}
