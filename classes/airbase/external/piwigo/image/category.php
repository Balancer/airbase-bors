<?php

class airbase_external_piwigo_image_category extends bors_object_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return 'AB_PIWIGO'; }
	function table_name() { return 'piwigo_image_category'; }

	function table_fields()
	{
		return [
			'id' => 'image_id,category_id',
			'image_id',
			'category_id',
			'rank',
		];
	}
}
