<?php

class airbase_user_admin_warning extends airbase_user_warning
{
	var $stb_access_engine = 'airbase_user_admin_access';

	function check_data(&$data)
	{
		$user = object_load('bors_user', $data['user_id']);
		if(in_array($user->group_id(), array(1,2,5,21)))
			return bors_message(ec('Нельзя выставлять штрафы координаторам и модераторам'));

		$object = object_load($data['object']);
		if(!$object)
			return bors_message(ec('Неизвестный объект ').$data['object']);

		if($data['user_id'] != $object->owner_id())
			return bors_message(ec('Попытка выставить штраф пользователю, не являющемуся автором сообщения'));

		$previous_warning = objects_first('airbase_user_warning', array(
			'user_id=' => $data['user_id'],
			'warn_class_id=' => $object->class_id(),
			'warn_object_id=' => $object->id(),
		));

		if($previous_warning)
			return bors_message(ec('Пользователь уже получил предупреждение за это сообщение'));

		$data['create_time'] = time();
		$data['score'] = airbase_user_warning_type::score($data['type_id']);
		$data['moderator_id'] = bors()->user()->id();
		$data['moderator_name'] = bors()->user()->title();
		$data['warn_class_id'] = $object->class_id();
		$data['warn_object_id'] = $object->id();

		return parent::check_data($data);
	}

	function check_value_conditions()
	{
		return array(
			'warn_class_id'         => ec("!=0|Неизвестное сообщение"),
			'type_id'     => ec("!=0|Не указана причина предупреждения"),
		);
	}

	function post_set(&$data)
	{
		$uid = $data['user_id'];
		$user = object_load('bors_user', $uid);
		$warnings = $this->db()->select('warnings', 'SUM(score)', array('user_id=' => $uid, 'time>' => time()-WARNING_DAYS*86400));
		$warnings_total = $this->db()->select('warnings', 'SUM(score)', array('user_id=' => $uid));
		$user->set_warnings($warnings, true);
		$user->set_warnings_total($warnings_total, true);
		$user->cache_clean();
		object_load('users_topwarnings')->cache_clean();

		$object = object_load($data['object']);
		$object->set_warning_id($this->id(), true);
		$object->cache_clean();
		
		unlink('/var/www/balancer.ru/htdocs/user/'.$uid.'/warnings.gif');
	}
}
