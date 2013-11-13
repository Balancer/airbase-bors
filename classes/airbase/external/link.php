<?php

class airbase_external_link extends balancer_board_object_db
{
	function db_name() { return 'AB_RESOURCES'; }
	function table_name() { return 'links'; }

	function table_fields()
	{
		return array(
			'id',
			'url_index',
			'url_real' => array('type' => 'bbcode'),
			'title',
			'tags' => array('type' => 'bbcode'),
			'bbshort' => array('type' => 'bbcode'),
			'description' => array('type' => 'bbcode'),
			'image_id',
			'owner_id',
			'html_source' => array('type' => 'bbcode'),
			'content_type',
			'last_error',
			'last_check_time' => array('name' => 'UNIX_TIMESTAMP(`last_check_time`)'),
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

	static function register($url, $params = array())
	{
		$max_length = defval($params, 'max_length')

		$req = self::get_ex($url, array('is_raw' => false));
		$content = $req['content'];

		// Запоминаем не более одного мегабайта, а то по max_allowed_packet можно влететь.
		if(strlen($content) > $max_length)
			$content = substr($content, 0, $max_length);

		$data = bors_external_common::content_extract($url, array('html' => $source));

//			$html = lcml($data['bbshort']);

		var_dump($req, $data);

		return bors_new(__CLASS__, array(
			'url_index' => self::normalize($url),
			'url_real' => $url,

			'title' => @$data['title'],
			'tags' => @$data['tags'],
			'bbshort' => @$data['bbshort'],
//			'description' => array('type' => 'bbcode'),
//			'image_id',
//			'owner_id',
			'html_source' => $content,
			'content_type' => $req['content_type'],
			'last_error' => $req['error'],
			'last_check_time' => time(),
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
		echo self::find_or_register('http://www.freeupload.cn');
	}

	static function __unit_test($suite)
	{
//		$suite->asserEquals('', 'http://www.balancer.ru:8080/test%20test.jpg');
	}
}
