<?php

class airbase_pages_db extends bors_pages_db
{
	var $config_class = 'airbase_config';

	function db_name() { return 'AB_RESOURCES'; }
	var $table_name = 'pages';

	function table_fields()
	{
		return array(
			'id',
			'url',
			'title' => array('type' => 'bbcode'),
			'source',
			'access_level',
			'color',
			'copyright' => array('type' => 'bbcode'),
			'create_time',
			'author',
			'author_names',
			'autolink',
			'child',
			'compile_time',
			'cr_type',
			'description',
			'description_source',
			'flags',
			'forum_id',
			'height',
			'keyword',
			'local_path',
			'logdir',
			'modify_time',
			'nav_name',
			'origin_uri',
			'order',
			'parent',
			'positions',
			'rcolumn',
			'referer',
			'right_column',
			'size',
			'split_type',
			'style',
			'template',
			'type',
			'version',
			'views',
			'views_first',
			'views_last',
			'width',
		);
	}

	static function __dev()
	{
		$x = bors_foo(__CLASS__);
		echo $x;
	}
}
