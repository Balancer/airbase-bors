<?php

class balancer_board_keywords_tags extends base_page
{
	function can_be_empty() { return false; }
	function loaded() { return count($this->all_items()) && $this->_items_this_page(); }

	static function keywords_explode($keywords_string)
	{
		$keywords_string = str_replace('/', ',', $keywords_string);
		$keywords = explode(',', $keywords_string);
		$keywords = array_map('urldecode', $keywords);
		$keywords = array_map('trim', $keywords);
		$keywords = array_filter($keywords);
		$keywords = array_filter($keywords, create_function('$x', 'return strlen($x) > 1;'));
		sort($keywords);
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

	function title() { return str_replace(',', ', ', $this->keywords_string()); }

	function description()
	{
		require_once('inc/airbase_keywords.php');

		$sub_keywords = array();
		$base_keywords = array();

		foreach($this->all_items() as $x)
		{
			$subkw = self::keywords_explode($x->keywords_string());
			foreach($subkw as $kw)
				@$base_keywords[$kw]++;
			foreach(array_diff($subkw, $this->keywords()) as $kw)
				@$sub_keywords[$kw]++;
		}

		arsort($sub_keywords);
		arsort($base_keywords);

		if(count($this->all_items()) <= 50)
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

	private function _items_this_page()
	{
		return $this->__havec('_items_this_page') ? $this->__lastc() : $this->__setc(array_slice($this->all_items(),
				($this->args('page')-1) * $this->items_per_page(),
				$this->items_per_page()
		));
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

	function local_data()
	{
		return array(
			'items' => $this->_items_this_page(),
//			'keyword' => $this->keyword(),
		);
	}

	function items_per_page() { return 25; }
	function total_items() { return count($this->all_items()); }

	private $_all_items = NULL;
	function all_items()
	{
		if(!is_null($this->_all_items))
			return $this->_all_items;

		$keys = $this->keywords();
		$keys_norm = array();
		foreach($keys as $key)
			if($key_norm = common_keyword::normalize(trim($key)))
				$keys_norm[] = $key_norm;

		$items = array();
		foreach(objects_array('common_keyword', array('keyword IN' => $keys_norm)) as $kw)
		{
			if($binds = objects_array('common_keyword_bind', array('keyword_id' => $kw->id())))
			{
				$objects_map = array();
				foreach($binds as $b)
					$objects_map[$b->target_class_id()][] = $b->target_object_id();

				$list = array();
				foreach($objects_map as $class_id => $objects_ids)
				{
					$class_name = class_id_to_name($class_id);
					foreach(objects_array($class_name, array(
							'id IN' => $objects_ids,
							'order' => '-modify_time',
						)) as $x)
						$list[$x->internal_uri()] = $x;
				}

				$items[] = $list;
			}
		}

		if(count($items) > 1)
			$items = call_user_func_array('array_intersect', $items);
		elseif(count($keys) > 1)
			$items = array();
		elseif($items)
			$items = $items[0];
		else
			$items = array();

		return $this->_all_items = $items;
	}
}
