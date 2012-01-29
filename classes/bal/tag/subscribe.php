<?php

class bal_tag_subscribe extends bal_object_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return 'BALANCER'; }
	function table_name() { return 'tag_subscribes'; }

	function class_title() { return ec('Объект tag_subscribe'); }
	function class_title_rp() { return ec('объекта tag_subscribe'); }
	function class_title_vp() { return ec('объект tag_subscribe'); }
	function class_title_m() { return ec('объекты tag_subscribe'); }
	function class_title_tpm() { return ec('объектами tag_subscribe'); }

	function access_name() { return 'subscribes'; }

	function table_fields()
	{
		return array(
			'id',
			'user_id',
			'tag_id',
			'subscribe_type',
			'create_time',
		);
	}

	function url() { return config('main_site_url').'/subscribes/'.$this->id().'/'; }

	function admin_url() { return config('admin_site_url').'/subscribes/'.$this->id().'/'; }
}
