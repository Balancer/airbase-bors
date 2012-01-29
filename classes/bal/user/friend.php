<?php

class bal_user_friend extends bal_object_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return 'AIRBASE'; }
	function table_name() { return 'user_friends'; }

	function class_title() { return ec('Объект user_friend'); }
	function class_title_rp() { return ec('объекта user_friend'); }
	function class_title_vp() { return ec('объект user_friend'); }
	function class_title_m() { return ec('объекты user_friend'); }
	function class_title_tpm() { return ec('объектами user_friend'); }

	function access_name() { return 'friends'; }

	function table_fields()
	{
		return array(
			'id',
			'user_id',
			'friend_id',
			'create_time',
		);
	}

	function url() { return config('main_site_url').'/friends/'.$this->id().'/'; }

	function admin_url() { return config('admin_site_url').'/friends/'.$this->id().'/'; }
}
