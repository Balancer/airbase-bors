<?php

class airbase_external_piwigo_image extends bors_object_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return 'AB_PIWIGO'; }
	function table_name() { return 'piwigo_images'; }

	function table_fields()
	{
		return [
			'id',
			'file',
			'date_available',
			'date_creation',
			'title' => 'name',
			'description' => ['name' => 'comment', 'type' => 'bbcode'],
			'author',
			'hit',
			'filesize',
			'width',
			'height',
			'coi' => ['title' => 'center of interest'],
			'representative_ext',
			'date_metadata_update',
			'rating_score',
			'path',
			'storage_category_id',
			'level',
			'md5sum',
			'added_by',
			'rotation',
			'latitude',
			'longitude',
			'modify_time' => 'UNIX_TIMESTAMP(`lastmodified`)',
			'is_gvideo',
		];
	}

	function url() { return 'http://photos.wrk.ru/picture.php?/'.$this->id().'/'; }

	function image_xs_url()
	{
		// ./upload/2016/06/11/20160611013154-9f434e85.jpg
		// ./upload/2016/06/11/20160611012801-3fd68399.jpg
		return 'http://photos.wrk.ru/i.php?'.preg_replace('!^\.(/upload/\d{4}/\d\d/\d\d/.+)\.(\w+)$!', '$1-xs.$2', $this->path());
	}

	function _categories_def()
	{
		$cat_ids = airbase_external_piwigo_image_category::find(['image_id' => $this->id()])
			->all()
			->extract('category_id')
			->value();

		return airbase_external_piwigo_category::find(['id IN' => $cat_ids])->all();
	}
}
