<?php

config_set('lcml_cache_disable', true);

class balancer_page_db extends base_page_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return 'WRK'; }
	function table_name() { return 'pages'; }
	function table_fields()
	{
		return array(
			'id',
			'host',
			'path',
			'full_url',
			'title',
			'description',
			'source',
			'create_time',
			'modify_time',
			'owner_id',
			'render_class',
			'template',
		);
	}

	static function id_prepare($id)
	{
		if(is_numeric($id))
			return $id;

		$host = bors()->server()->host();

		if($host == 'aviaport.wrk.ru')
			$host = 'www.aviaport.ru';

		$page = bors_find_first(__CLASS__, array(
			'host' => $host,
			'path' => $id,
		));

		return $page;
	}
}
