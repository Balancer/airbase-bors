<?php

class bal_rpc_json_find extends bors_json
{
	const DAYS=93;

	function data()
	{
		if(!in_array($_SERVER['REMOTE_ADDR'], config('trusted.ips')))
			return ['REMOTE_ADDR' => $_SERVER['REMOTE_ADDR']];

		$target_class = $this->id();
		if(!class_exists($target_class))
			return ['error' => 'Unknown class '.$target_class];

		$where = [
			'*set' => 'topics.subject as topic_title',
			'inner_join' => ['topics ON posts.topic_id = topics.id'],
			'create_time BETWEEN' => [time() - 86400*self::DAYS,  time()],
			'balancer_board_topic.is_public=' => true,
			'order' => '-create_time',
//			'CHARACTER_LENGTH(`source`)>512',
//			'score>=' => 1,
			'answer_to_id' => NULL,
			'limit' => bors()->request()->data('limit', 1),
			'by_id' => true,
		];

		if($tags = bors()->request()->data('tags'))
		{
			$topic_ids = NULL;

			foreach(explode('/', $tags) as $tag)
			{
				$tids = [];
				foreach(explode(',', $tag) as $t)
				{
					$kid = common_keyword::loader($t)->id();
					if($kid)
					{
						$tids += bors_field_array_extract(bors_find_all('common_keyword_bind', [
							'keyword_id' => $kid,
							'target_class_name IN' => ['forum_topic', 'balancer_board_topic'],
							'target_modify_time>' => time() - 86400*self::DAYS,
						]), 'target_object_id');
					}
				}
				if(is_null($topic_ids))
					$topic_ids = $tids;
				else
					$topic_ids = array_intersect($topic_ids, $tids);
			}

//			$where['inner_join'][] = '`AB_BORS`.`bors_keywords_index` AS `kwi` ON `kwi`.`target_object_id`=`topics`.id';
//			$where['target_modify_time>'] = time() - 86400*self::DAYS;
			$where['topic_id IN'] = $topic_ids;
		}

		if($id_not = bors()->request()->data('id_not'))
			$where['`posts`.`id` NOT IN'] = explode(',', $id_not);

		$finder = bors_find_all($target_class, $where);

		$result = [];

		$fields = explode(',', bors()->request()->data('fields', []));

		foreach($finder as $id => $x)
		{
			$data = $x->data;

			if($fields)
				foreach($fields as $f)
					$data[$f] = $x->get($f);

			if($thumb =  bors()->request()->data('thumb'))
				$data['thumbnail_url'] = ($i = $x->image()) ? $i->thumbnail($thumb)->url() : NULL;

			$data['url'] = $x->url_for_igo();
			$data['topic_tags'] = $x->topic()->keywords();

			$result[$id] = $data;
		}

		return $result;
	}
}
