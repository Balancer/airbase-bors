<?php

class balancer_blogs_tags_show extends base_page
{
	function can_be_empty() { return false; }
	function loaded() { return !!$this->items(); }

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

	function default_page() { return $this->total_pages(); }

	function keywords()
	{
		if($this->__havefc())
			return $this->__lastc();

		$keys = explode(',', $this->id());
		$keys_unique = array();
		foreach($keys as $key)
			if($key = common_keyword::loader($key))
				$keys_unique[$key->id()] = $key;

		$names = bors_field_array_extract($keys_unique, 'title');
		$names = array_map('trim', $names);

		return $this->__setc($names);
	}

	function title() { return str_replace(',', ', ', $this->keywords_string()); }

	function description()
	{
		require_once('inc/airbase_keywords.php');

		$sub_keywords = array();
		$base_keywords = array();

		foreach($this->items() as $x)
		{
			if(!$x)
				continue;

			$subkw = self::keywords_explode($x->keywords_string());
			foreach($subkw as $kw)
				@$base_keywords[$kw]++;
			foreach(array_diff($subkw, $this->keywords()) as $kw)
				@$sub_keywords[$kw]++;
		}

		arsort($sub_keywords);
		arsort($base_keywords);

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

		return "http://blogs.balancer.ru/tags/"
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

	function page_data()
	{
		$items = array_filter($this->items());
		$post_ids = array();
		foreach($items as $item)
		{
			if($item->extends_class_name() == 'forum_topic')
				$post_ids[] = $item->first_post_id();
			else
				$post_ids[] = $item->id();
		}

		$posts = bors_find_all('balancer_board_post', array('id IN' => $post_ids, 'order' => 'create_time', 'by_id' => true));

		foreach($items as $i)
		{
			if($item->extends_class_name() == 'forum_topic')
				$pid = $item->first_post_id();
			else
			{
				$pid = $item->id();
				if($pid)
					$posts[$pid]->set_blog($i, false);
			}

			if(empty($posts[$pid]))
				continue;

			if($kws = $i->keywords())
				$posts[$pid]->set_kws_links(balancer_blogs_tag::linkify($kws, '', ' | ', true), false);
		}

		return array(
			'items' => array_values($posts),
//			'keyword' => $this->keyword(),
		);
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
			'target_class_name IN' => array('balancer_board_blog', 'forum_blog', 'balancer_board_topic', 'forum_topic'),
			'group' => 'target_class_name,target_object_id',
			'having' => 'COUNT(*) = '.count($this->_selected_keywords()),
		)));
	}

	function items()
	{
		if($this->__havefc())
			return $this->__lastc();

		$targets = array();
		$bindings = objects_array('common_keyword_bind', array(
			'keyword_id IN' => $this->_selected_keywords(),
			'target_class_name IN' => array('balancer_board_blog', 'forum_blog', 'balancer_board_topic', 'forum_topic'),
			'group' => 'target_class_name,target_object_id',
			'having' => 'COUNT(*) = '.count($this->_selected_keywords()),
			'order' => '-target_create_time',
			'page' => $this->page(),
			'per_page' => $this->items_per_page(),
		));

		foreach($bindings as $bind)
			$targets[$bind->target_class_name()][] = $bind->target_object_id();

		$items = array();
		foreach($targets as $class_name => $ids)
			$items = array_merge($items, objects_array($class_name, array('id IN' => $ids)));

		uasort($items, create_function('$a,$b', 'return $a->create_time() < $b->create_time();'));

		return $this->__setc($items);
	}
}
