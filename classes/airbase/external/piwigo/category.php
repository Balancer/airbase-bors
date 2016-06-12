<?php

class airbase_external_piwigo_category extends bors_object_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return 'AB_PIWIGO'; }
	function table_name() { return 'piwigo_categories'; }

	function table_fields()
	{
		return [
			'id',
			'title' => 'name',
			'id_uppercat',
			'description' => ['name' => 'comment', 'type' => 'bbcode'],
			'dir',
			'rank',
			'status',
			'site_id',
			'visible',
			'representative_picture_id',
			'uppercats',
			'commentable',
			'global_rank',
			'image_order',
			'permalink',
			'modify_time' => ['name' => 'UNIX_TIMESTAMP(`lastmodified`)'],
			'community_user',
		];
	}
}
