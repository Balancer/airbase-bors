<?php

class wrk_news_tags_view extends balancer_board_paginated
{
	const DAYS=93;

	function title() { return 'Новости по теме «'.$this->id().'»'; }
	var $main_class = 'balancer_board_post';

	static function id_prepare($id)
	{
		$id = array_map('urldecode', array_filter(explode('/', $id)));
		sort($id);
		return join('/', $id);
	}

	function where()
	{
		$tags = explode('/', $this->id());

		$topic_ids = bors_field_array_extract(bors_find_all('common_keyword_bind', [
			'keyword_id' => common_keyword::loader('новости')->id(),
			'target_class_name IN' => ['forum_topic', 'balancer_board_topic'],
			'target_modify_time>' => time() - 86400*self::DAYS,
		]), 'target_object_id');

		foreach($tags as $t)
		{
			$kid = common_keyword::loader($t)->id();
			if($kid)
			{
				$tids = bors_field_array_extract(bors_find_all('common_keyword_bind', [
					'keyword_id' => $kid,
					'target_class_name IN' => ['forum_topic', 'balancer_board_topic'],
					'target_modify_time>' => time() - 86400*self::DAYS,
				]), 'target_object_id');

				$topic_ids = array_intersect($topic_ids, $tids);
			}
			else
			{
				$topic_ids = [];
				break;
			}
		}

		return [
			'topic_id IN' => $topic_ids,
			'is_deleted' => false,
//			'is_hidden' => false,
//			'is_spam' => false,
//			'is_incorrect' => false,
			'create_time>' => time() - 86400*self::DAYS,
			'answer_to_id' => 0,
			'(score>=0 OR score IS NULL)',
//			'(warning_id = 0 OR warning_id IS NULL)',
		];
	}

	function url_ex($page)
	{
//		config_set('debug_redirect_trace', true);

		return 'http://www.wrk.ru/news/tags/'.$this->id().'/'.($page>1?$page.'.html':'');
	}
}
