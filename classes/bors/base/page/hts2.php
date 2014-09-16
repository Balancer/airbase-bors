<?php
/*
	Класс, хранящий свои данные в отдельных таблицах БД.
	Приведение старого HTS-формата в одну таблицу данных и отдельные таблицы
	массивов данных.
*/

class base_page_hts2 extends bors_page_db
{
	function access_engine() { return config('hts_access', 'balancer_board_access_public'); }

	function auto_map() { return true; }

	static function id_prepare($url)
	{
		$url = preg_replace('!^(http://[^/]+):\d+!', '$1', $url);
		$url = preg_replace('!^http://www\.!', 'http://', $url);

		// Для тестового сервера
		$url = str_replace('!airbase.home.balancer.ru!', 'airbase.ru', $url);

		if(preg_match('!^(.+/)index\.phtml$!', $url, $m))
			$url = $m[1];
		elseif(preg_match('!^(.+)\.phtml$!', $url, $m))
			$url = $m[1].'/';

		if(!preg_match('!/$!', $url))
			$url .= '/';

		return $url;
	}

//	function can_cached() { return false; }
	function cache_static() { return $this->modify_time() > time() - 86400*7 ? rand(3600, 7200) : rand(86400*7, 86400*30); }

	function storage_engine() { return 'bors_storage_mysql'; }
	function config_class() { return config('admin_config_class'); }
	function html_disable() { return false; }
	function lcml_tags_enabled() { return NULL; }

	function is_loaded()
	{
		if(substr($this->id(), 0, 1) == '/')
			return false;
/*
		if(preg_match('/.+#lev\s+(.+?)\s*,\s*(.+?).*?/s', @$this->data['source'], $m))
		{
			var_dump($m);
			$this->set_attr('parents', array($m[1]));
		}
*/
		return @$this->data['title'] && @$this->data['source'];
	}

	function db_name()
	{
		bors_debug::syslog('000-obsolete', "Call direct base_page_hts");
		return 'HTS_OTHER';
	}

	function table_name() { return 'hts_data_title'; }

	function owner() { return bors_load('balancer_board_user', 10000); }

	function table_fields()
	{
		return [
			'id',
			'title' => 'value',
		];
	}

	function left_join_fields()
	{
		return [
			$this->db_name() => [
				'hts_data_source(id)'		=> ['source' => 'value'],
				'hts_data_description(id)'	=> ['description' => 'value'],
				'hts_data_create_time(id)'	=> ['create_time'  => 'value'],
				'hts_data_modify_time(id)'	=> ['modify_time'  => 'value'],
				'hts_data_nav_name(id)'		=> ['nav_name'  => 'value'],
				'hts_data_cr_type(id)'		=> ['cr_type'  => 'value'],
				'hts_data_template(id)'		=> ['template_db'  => 'value'],
			],
		];
	}

	function parents()
	{
		if($ps = $this->attr('parents'))
			return $ps;

		return $this->set_attr('parents', $this->db(config('hts.database', 'HTS'))->select_array('hts_data_parent', 'value', array('id=' => $this->id())));
	}

	function set_parents($array, $db_up)
	{
		if($db_up)
		{
			$this->db()->delete('hts_data_parent', array('id' => $this->id()));
			for($i=0; $i<sizeof($array); $i++)
				$this->db()->replace('hts_data_parent', array('id' => $this->id(), 'value' => $array[$i], 'sort_order' => $i));
		}

		return parent::set_parents($array, $db_up);
	}

	function children()
	{
		return array_map(
			create_function('$x', 'return trim($x);'),
			$this->db()->select_array('hts_data_child', 'value', array('id' => $this->id(), 'order' => 'sort_order'))
		);
	}

	function set_children($array, $db_up)
	{
		if($db_up)
		{
			$this->db()->delete('hts_data_child', array('id' => $this->id()));
			for($i=0; $i<sizeof($array); $i++)
				$this->db()->replace('hts_data_child', array('id' => $this->id(), 'value' => $array[$i], 'sort_order' => $i));
		}

		return parent::set_children($array, $db_up);
	}

	function template()
	{
		if($tpl = $this->get('template_db'))
		{
//			if(preg_match('/^\w+$/', $tpl))
//				$tpl = "xfile:$tpl/index.html";

			if($tpl == 'balancer' || $tpl == 'doors' || $tpl == 'kron')
				$tpl = 'xfile:/var/www/www.balancer.ru/bors-site/templates/blue_spring/index.html';

			if($tpl == 'wide')
				$tpl = 'xfile:airbase/default/index2.html';

			return $tpl;
		}

		//WTF? Найти, где оно присваивается.
		unset($this->attr['template']);

		return parent::template();
	}

	function init()
	{
		$host = $this->args('host');
		if($host && preg_match('!/!', $this->id()))
			$this->set_id('http://'.$host.$this->id());

		if(!$this->id())
			$this->set_id($this->called_url());

		return parent::init();
	}

	function url()
	{
		$url = preg_replace('!http://airbase\.ru!', 'http://www.airbase.ru', $this->id());
		$url = preg_replace('!http://balancer\.ru!', 'http://www.balancer.ru', $url);
		return $url;
	}

	function url_ex($page) { return $this->url(); }

	function post_set(&$data)
	{
		config_set('cache_disabled', true);
		$text = lcml($data['source'],
			array(
				'cr_type' => $this->cr_type(),
				'nocache' => true,
				'sharp_not_comment' => $this->sharp_not_comment(),
				'html_disable' => $this->html_disable(),
		));

		$this->set_body($text, true);
	}

	function editor_fields_list()
	{
		return array(
			ec('Заголовок:') => 'title',
			ec('Тело страницы:') => 'source|bbcode=30',
		);
	}

/*
	hts__aliases	- будет использоваться таблица bors_uris (uri -> class(id))
	autolink -> таблица keywords
	keyword -> массив
	backup -> продумать wiki :-/
	category - категория тикетов.
	email - ? - торг?
	fax - ? - торг?
	height -> для картинок

	child - таблица детей

	author - массив авторов
	copyright - объединить?
	flags - массив?

	access_level
	color
	description -> description_html
	description_source -> description
	forum_id -> comments_id

	hts_data_local_path
	hts_data_logdir
	hts_data_marker
	hts_data_order
	hts_data_org
	hts_data_origin_uri
	hts_data_parent
	hts_data_phone
	hts_data_priority
	hts_data_public_time
	hts_data_publisher
	hts_data_referer
	hts_data_right_column
	hts_data_size
	hts_data_split_type
	hts_data_stop_time
	hts_data_style
	hts_data_type
	hts_data_version
	hts_data_views
	hts_data_views_first
	hts_data_views_last
	hts_data_width
	hts_ext_log
	hts_ext_referers
	hts_ext_system_data
	hts_hosts
	hts_host_redirect
	hts_ids
	hts_keys
	hts_logs
*/

	static function __dev()
	{
		config_set('lcml_cache_disable', true);
		$x = bors_load_uri('http://www.airbase.ru/alpha/e/');
		echo $x->source();
		echo $x->body();
	}
}