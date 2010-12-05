<?php

class balancer_board_avatar extends base_object_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return 'punbb'; }
	function table_name() { return 'avatars'; }
	function table_fields()
	{
		return array(
			'id',
			'owner_id' => 'user_id',
			'image_class_name',
			'image_id',
			'image_original_url',
			'image_file',
			'image_html',
			'title',
			'signature',
			'signature_html',
			'create_time',
		);
	}

	function auto_targets()
	{
		return array_merge(parent::auto_targets(), array(
			'image' => 'image_class_name(image_id)',
		));
	}
}
