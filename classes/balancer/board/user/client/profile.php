<?php

class balancer_board_user_client_profile extends balancer_board_object_db
{
	var $access_engine = 'balancer_board_access_owned';

	function db_name() { return 'AB_FORUMS'; }
	function table_name() { return 'user_client_profiles'; }

	function class_title() { return ec('Профиль'); }

	function owner_id() { return $this->user_id(); }

	function table_fields()
	{
		return array(
			'id',
			'user_id' => array('class' => 'balancer_board_user', 'have_null' => true),
			'title',
			'is_default',
			'cookie_hash',
			'modify_time' => 'UNIX_TIMESTAMP(`modify_time`)',
			'create_time' => 'UNIX_TIMESTAMP(`create_time`)',
			'need_trafic_save',
			'textarea_type',
		);
	}

	function admin_url()
	{
		return 'http://forums.balancer.ru/personal/clients/'.$this->id().'/';
	}

	function public_action_delete($args)
	{
		return go($this->admin()->delete_url());
	}

	function can_delete()
	{
		return $this->user_id() == bors()->user_id();
	}

	static function by_cookies()
	{
		if($profile_hash = @$_COOKIE['client_profile_hash'])
			return balancer_board_user_client_profile::find(['cookie_hash' => $profile_hash])->first();

		return false;
	}
}
