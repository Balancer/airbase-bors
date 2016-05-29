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

	// Формат для совместимости с bors_object::find($where)
	static function find($original_url = [])
	{
		$url_index = self::normalize($original_url);
		try
		{
			if($x = bors_find_first(__CLASS__, array('url_index' => $url_index)))
				return $x;
		}
		catch(Exception $e)
		{
			bors_debug::syslog('link-load-error', blib_exception::factory($e));
		}

		return NULL;

	}

	static function register($url, $params = array(), $test=false)
	{
		$max_length = defval($params, 'max_length', 1000000);

		$req = blib_http::get_ex($url, ['is_raw' => false]);
		$content = $req['content'];

		// Запоминаем не более одного мегабайта, а то по max_allowed_packet можно влететь.
		if(strlen($content) > $max_length)
			$content = substr($content, 0, $max_length);

		if($content)
			$data = bors_external_common::content_extract($url, ['html' => $content]);
		else
		{
			$data['title'] = self::normalize(blib_urls::host($url));
			$data['bbshort'] = "[round_box][h][a href=\"{$url}\"]{$data['title']}[/a][/h]
{$req['error']}

[span class=\"transgray\"][reference]".bors_external_feeds_entry::url_host_link($url)."[/reference][/span][/round_box]";

		}

//		$html = lcml($data['bbshort']);
//		var_dump($url, $data);
		if($test)
			return $data;

		$data = array(
			'url_index' => self::normalize($url),
			'url_real' => $url,

			'title' => @$data['title'],
			'tags' => join(',', defval($data, 'tags', array())),
			'bbshort' => @$data['bbshort'],
//			'description' => array('type' => 'bbcode'),
//			'image_id',
//			'owner_id',
			'html_source' => preg_match('!^text/!', $req['content_type']) ? $content : NULL,
			'content_type' => $req['content_type'],
			'last_error' => $req['error'],
			'last_check_time' => time(),
		);

//		bors_debug::syslog('__test-check-no-arrays-in-data', print_r($data, true));

		$link = bors_new(__CLASS__, $data);
		return $link;
	}

	static function default_bbshort($url, &$data=array())
	{
		// Ставим герерацию превьюшки
		// Сперва декодируем URL (urldecode + кодировка)
		$url = blib_urls::decode($url);

		$url_data = parse_url($url);
		$host = preg_replace('/^www\./', '', $url_data['host']);
		$host_parts = array_reverse(explode('.', $host));

		$id = blib_string::base64_encode2($url);

		$thumb_url = "http://200x150.st.cg.a0z.ru/".base64_encode($url).".jpg";
		$img = "<div class=\"rs_box_nd float_left mtop8\" style=\"width:200px; height:150px;\">"
			."<img src=\"{$thumb_url}\" width=\"200\" height=\"150\" alt=\"\" class=\"main\"></div>";

		$data['title'] = self::normalize(blib_urls::host($url));
		$data['bbshort'] = "[round_box]{$img}[h][a href=\"{$url}\"]{$data['title']}[/a][/h]

[span class=\"transgray\"][reference]{$url}[/reference][/span][/round_box]";

		return $data;
	}

	static function find_or_register($url)
	{
		if(config('is_debug')) exit('stop:'.__FILE__.':'.__LINE__);

		if(!($x = self::find($url)))
		{
			$x = self::register($url);
			$bbshort = $x->bbshort();
		}
		elseif(config('lcml_cache_disable_full'))
		{
			$data = self::register($url, array(), true); // При сбросе кеша выполняем все расчёты, но не регистрируем
			$bbshort = $data['bbshort'];
			$x->set_bbshort($bbshort);
		}

		if(!$x->bbshort() || preg_match('/timedxout/', $x->bbshort()))
		{
			$data = self::default_bbshort($url);
			$x->set_attr('bbshort', $data['bbshort']);
		}

		return $x;
	}

	static function __dev()
	{
//		echo self::find_or_register('http://www.freeupload.cn');
		self::register('http://www.cliparthost.com', array(), true);
	}

	static function __unit_test($suite)
	{
//		$suite->asserEquals('', 'http://www.balancer.ru:8080/test%20test.jpg');
	}
}
