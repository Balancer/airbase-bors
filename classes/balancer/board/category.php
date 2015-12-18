<?php

class balancer_board_category extends forum_category
{
	function class_title() { return ec('Категория'); }

	function extends_class_name() { return 'forum_category'; }

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), array(
			'parent_category' => 'balancer_board_category(parent_category_id)',
		));
	}

	function parent_category_admin_titled_link()
	{
		$cat = $this->parent_category();
		if(!$cat)
			return NULL;

		return $cat->admin()->titled_link();
	}

	function full_name($cats = NULL)
	{
		$result = array();
		$current_cat = $this;
		do {
			$result[] = $current_cat->nav_name();
			if($parent = $current_cat->parent_category_id())
				$current_cat = $cats ? $cats[$parent] : bors_load('balancer_board_category', $parent);
		} while($parent);

		return join(' « ', $result);
	}

	function all_subforums()
	{
		$cats_processed = array();
		$forums_processed = array();
		$forums = array();

		foreach(array_merge(array($this->id()), $this->direct_subcats_ids()) as $cat_id)
		{
			if(in_array($cat_id, $cats_processed))
				continue;

			$cats_processed[] = $cat_id;
			$subcat = bors_load('balancer_board_category', $cat_id);

			foreach($subcat->direct_subforums_ids() as $forum_id)
			{
				if(in_array($forum_id, $forums_processed))
					continue;

				$forums_processed[] = $forum_id;
				$subforum = $forums[$forum_id] = bors_load('balancer_board_forum', $forum_id);
				$forums += $subforum->all_subforums($forums_processed);
			}
		}

		return blib_array::factory($forums);
	}

	static function forums_for_category_names($categories)
	{
		if(!is_array($categories))
			$categories = array_filter(explode(',', $categories));

		$forums = new blib_array;
		foreach($categories as $cat_name)
		{
			foreach(bors_find_all('balancer_board_category', array('project' => $cat_name)) as $cat)
				$forums->append_array($cat->all_subforums());
		}

		return $forums;
	}

	function item_list_admin_fields()
	{
		return array(
			'image_html' => '',
			'admin()->imaged_titled_link()' => ec('Название'),
			'parent_category_admin_titled_link' => ec('Родительская категория'),
			'project' => ec('Проект'),
			'id' => 'ID',
		);
	}

	function infonesy_push()
	{
		$storage = '/var/www/sync/airbase-forums-push';
		$file = $storage.'/category-'.$this->id().'.json';

//		if(file_exists($file))
//			return;

		require_once 'inc/functions/fs/file_put_contents_lock.php';

		$data = [
			'UUID'		=> 'ru.balancer.board.category.'.$this->id(),
			'Node'		=> 'ru.balancer.board',
			'Title'		=> $this->title(),
			'Type'		=> 'Category',
		];

		if($this->parent_category_id())
			$data['ParentUUID']	= 'ru.balancer.board.category.'.$this->parent_category_id();

		@file_put_contents_lock($file, json_encode(array_filter($data), JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		@chmod($file, 0666);
	}

	static function __dev()
	{
//		$cat = bors_load('balancer_board_category', 1);
//		$forums = $cat->all_subforums();
		$forums = self::forums_for_category_names('bionco,balancer_nt');
//		print_dd($cat->direct_subforums_ids());
//		var_dump(BORS_CORE);
		$forums->print_d();
	}
}
