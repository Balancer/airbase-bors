<?php

class airbase_external_url extends bors_object_db
{
	function db_name() { return 'AB_RESOURCES'; }
	function table_name() { return 'external_urls'; }

	function table_fields()
	{
		return array(
			'id',
			'url_index',
			'local_file',
			'url_real',
		);
	}

	function ignore_on_new_instance() { return true; }

	static function normalize($url)
	{
		// Декодируем.
		$url = urldecode($url);
		// Долой протокол
		$url = preg_replace('!^https?://!', '', $url);
		// Нафиг www.
		$url = preg_replace('!^www\.!', '', $url);
		// И удаляем порт из хоста
		$url = preg_replace('!^([^/:]+):\d+!', '$1', $url);

		return $url;
	}

	static function find($original_url)
	{
		$url_index = self::normalize($original_url);
		if($x = bors_find_first(__CLASS__, array('url_index' => $url_index)))
			return $x;

		return NULL;

	}

	static function register($original_url)
	{
		return bors_new(__CLASS__, array(
			'url_index' => self::normalize($original_url),
			'url_real' => $original_url,
		));
	}

	static function find_or_register($url)
	{
		if($x = self::find($url))
			return $x;

		return self::register($url);
	}

	static function __dev()
	{
//		echo self::find_or_register('http://www.balancer.ru:8080/test%20test.jpg');
	}

	static function __unit_test($suite)
	{
//		$suite->asserEquals('', 'http://www.balancer.ru:8080/test%20test.jpg');
	}
}
