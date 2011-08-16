<?php

class balancer_board_keywords_tags extends base_page
{
//	function can_be_empty() { return !bors()->client()->is_bot(); }
//	function loaded() { return count($this->items()); }

	static function keywords_explode($keywords_string)
	{
		$keywords_string = str_replace('/', ',', $keywords_string);
		$keywords = explode(',', $keywords_string);
		$keywords = array_map('urldecode', $keywords);
		$keywords = array_map('trim', $keywords);
		$keywords = array_filter($keywords);
		$keywords = array_filter($keywords, create_function('$x', 'return strlen($x) > 1;'));
		sort($keywords, SORT_LOCALE_STRING);
		return $keywords;
	}

	static function id_prepare($id) { return join(',', self::keywords_explode($id)); }

	function template() { return 'forum/_header.html'; }
	function keywords_string() { return urldecode($this->id()); }
//	function body_engine() { return 'body_php'; }

	private $_keywords = NULL;
	function keywords()
	{
		if(is_null($this->_keywords))
			$this->_keywords = explode(',', $this->id()); 

		return $this->_keywords;
	}

	function title() { return ec('Тэги форумов: ').str_replace(',', ', ', $this->keywords_string()); }
	function nav_name() { return bors_lower(str_replace(',', ', ', $this->keywords_string())); }

	function description()
	{
		require_once('inc/airbase_keywords.php');

		$sub_keywords = array();
		$base_keywords = array();

		foreach($this->items() as $x)
		{
			$subkw = self::keywords_explode($x->keywords_string());
			foreach($subkw as $kw)
				@$base_keywords[$kw]++;
			foreach(array_diff($subkw, $this->keywords()) as $kw)
				@$sub_keywords[$kw]++;
		}

		arsort($sub_keywords);
		arsort($base_keywords);

		if(count($this->items()) <= 50)
			$filters = '';
		else
			$filters = ec("Фильтр: ").
			airbase_keywords_linkify(join(',', 
				array_slice(array_keys($sub_keywords), 0, 7)
			), $this->keywords_string());

		$kw = ec("Тэги		: ").
			airbase_keywords_linkify(join(',', array_slice(array_keys($base_keywords), 0, 7)));

		return join('<br />', array($filters, $kw));

	}

	function url($page = 1)
	{
		$keywords = array_map('urlencode', $this->keywords());

		return "http://forums.balancer.ru/tags/"
			.join('/', $keywords)."/"
			.($page > 1 ? $page.'.html' : '');
	}

	function pre_show()
	{
		template_noindex();
		template_jquery();
		return parent::pre_show();
	}

	function keyword()
	{
//		return objects_first('common_keyword', '');
	}

	function body_data()
	{
//		print_dd($this->items());
//		echo $this->page();
		return array_merge(parent::body_data(), array(
			'items' => $this->items(),
//			'keyword' => $this->keyword(),
		));
	}

	function items_per_page() { return 25; }

	function _selected_keywords()
	{
		if($this->__havefc())
			return $this->__lastc();

		$keys = $this->keywords();
		$keys_ids = array();
		foreach($keys as $key)
			if($key = common_keyword::loader($key))
				$keys_ids[] = $key->id();

		return $this->__setc(array_unique($keys_ids));
	}

	function total_items()
	{
		if($this->__havefc())
			return $this->__lastc();

		return $this->__setc(objects_count('common_keyword_bind', array(
			'keyword_id IN' => $this->_selected_keywords(),
			'target_object_id=target_container_object_id',
//			'target_create_time>' => 0,
			'group' => 'target_class_name,target_object_id',
			'having' => 'COUNT(*) = '.count($this->_selected_keywords()),
		)));
	}


	function items()
	{
		if($this->__havefc())
			return $this->__lastc();

		$targets = array();

		foreach(bors_find_all('common_keyword_bind', array(
			'keyword_id IN' => $this->_selected_keywords(),
			'target_object_id=target_container_object_id',
//			'target_create_time>' => 0,
			'group' => 'target_class_name,target_object_id',
			'having' => 'COUNT(*) = '.count($this->_selected_keywords()),
			'order' => '-target_modify_time',
			'page' => $this->page(),
			'per_page' => $this->items_per_page(),
		)) as $bind)
		{
			$targets[$bind->target_class_name()][] = $bind->target_object_id();
		}

		$items = array();
		foreach($targets as $class_name => $ids)
			$items = array_merge($items, objects_array($class_name, array('id IN' => $ids)));

		uasort($items, create_function('$a,$b', 'return $a->modify_time() < $b->modify_time();'));

		return $this->__setc($items);
	}
}
