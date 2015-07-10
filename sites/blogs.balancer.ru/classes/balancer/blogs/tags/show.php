<?php

class balancer_blogs_tags_show extends balancer_board_page
{
//TODO: разобраться, почему не работает!
//	function can_be_empty() { return false; }
//	function is_loaded() { return true; }

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

		$kw = ec("Теги		: ").
			airbase_keywords_linkify(join(',', array_slice(array_keys($base_keywords), 0, 7)));

		return join('<br />', array($filters, $kw));

	}

	function url($page = NULL)
	{
		if(is_null($page))
			$page = $this->default_page();

		$keywords = array_map('urlencode', $this->keywords());

		return "http://blogs.balancer.ru/tags/"
			.join('/', $keywords)."/"
			.($page != $this->default_page() ? $page.'.html' : '');
	}


	function pre_show()
	{
		if($this->page() > $this->total_pages() || !$this->items())
			return bors_http_error(404);

		template_noindex();
		template_jquery();
		return parent::pre_show();
	}

	function keyword()
	{
//		return bors_find_first('common_keyword', '');
	}

	function page_data()
	{
		$items = array_filter($this->items());

		$post_ids = array();
		$topics = array();
		$blogs  = array();
		foreach($items as $item)
		{
			if($item->new_class_name() == 'balancer_board_topic')
			{
				$post_ids[] = $item->first_post_id();
				$topics[$item->id()] = $item;
			}
			else
			{
				$post_ids[] = $item->id();
				$blogs[$item->id()] = $item;
			}
		}

		$posts = bors_find_all('balancer_board_post', array('id IN' => $post_ids, 'order' => '-create_time', 'by_id' => true));

		balancer_board_posts_lib::load_keywords($posts, $topics, $blogs);

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

		return $this->__setc(bors_count('common_keyword_bind', array(
			'keyword_id IN' => $this->_selected_keywords(),
			'target_class_name IN' => array('balancer_board_blog', 'forum_blog', 'balancer_board_topic', 'forum_topic'),
			'target_object_id<>target_container_object_id',
			'group' => 'target_class_name,target_object_id',
			'having' => 'COUNT(*) = '.count($this->_selected_keywords()),
		)));
	}

	function items()
	{
		if(!$this->page())
			return NULL;

		if($this->__havefc())
			return $this->__lastc();
		$targets = array();
		$bindings = bors_find_all('common_keyword_bind', array(
			'keyword_id IN' => $this->_selected_keywords(),
			'target_class_name IN' => array('balancer_board_blog', 'forum_blog', 'balancer_board_topic', 'forum_topic'),
			'target_object_id<>target_container_object_id',
			'group' => 'target_class_name,target_object_id',
			'having' => 'COUNT(*) = '.count($this->_selected_keywords()),
			'order' => 'target_create_time',
			'page' => $this->page(),
			'per_page' => $this->items_per_page(),
		));

		foreach($bindings as $bind)
			$targets[$bind->target_class_name()][] = $bind->target_object_id();

		$items = array();
		foreach($targets as $class_name => $ids)
			$items = array_merge($items, bors_find_all($class_name, array('id IN' => $ids)));

		uasort($items, create_function('$a,$b', 'return $a->create_time() < $b->create_time();'));

		return $this->__setc($items);
	}

	function is_reversed() { return true; }
	function default_page() { return $this->total_pages(); }

//	function set_args(&$data) { var_dump($data); return parent::set_args($data); }
}
