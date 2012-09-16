<?php

class balancer_board_forum extends forum_forum
{
	function class_title() { return ec('Форум'); }

	function class_title_dp() { return ec('форуму'); }
	function class_title_vp() { return ec('форум'); }
	function class_title_tpm() { return ec('форумами'); }

	function extends_class_name() { return 'forum_forum'; }

	function last_topics($limit)
	{
		return bors_find_all('balancer_board_topic', array(
			'forum_id' => $this->id(),
			'order' => '-last_post_create_time',
			'limit' => $limit,
		));
	}

	function full_name($forums = NULL, $cats = NULL)
	{
		$result = array();
		$current_forum = $this;
		do {
			$result[] = $current_forum->nav_name();
			if($parent = $current_forum->parent_forum_id())
				$current_forum = $forums ? $forums[$parent] : object_load('airbase_forum_forum', $parent);
		} while($parent);

		$cat = $cats ? $cats[$current_forum->category_id()] : $current_forum->category();

		return join(' « ', $result).' « '.$cat->full_name();
	}

	function item_list_admin_fields()
	{
		return array(
			'admin()->imaged_titled_link()' => ec('форум'),
			'num_topics' => ec('Число тем'),
			'num_posts' => ec('Число сообщений'),
			'description' => ec('Описание'),
			'redirect_url' => ec('Перенаправление'),
			'id' => 'ID',
		);
	}

	function editor_fields_list()
	{
		return array(
			ec('Название') => 'title',
			ec('Описание') => 'description',
			ec('Родительский форум') => array('property' => 'parent_forum_id', 'class' => 'balancer_board_forum', 'have_null' => true),
			ec('Категория') => array('property' => 'category_id', 'class' => 'balancer_board_category'),
			ec('Порядок сортировки') => 'sort_order',
			ec("Тэги\nчерез запятую") => 'keywords_string',
			ec('Адрес перенаправления') => 'redirect_url',
			ec('Открытый доступ') => 'is_public',
		);
	}

	function admin_url()
	{
		return 'http://forums.balancer.ru/_bors/admin/edit-smart/?object='.$this->internal_uri_ascii(); 
	}

	function admin_parent_url() { return 'http://forums.balancer.ru/admin/forums/'; }
}
