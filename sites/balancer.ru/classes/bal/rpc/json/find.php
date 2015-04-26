<?php

class bal_rpc_json_find extends bors_json
{
	function data()
	{
		if(!in_array($_SERVER['REMOTE_ADDR'], config('trusted.ips')))
			return ['REMOTE_ADDR' => $_SERVER['REMOTE_ADDR']];

		$target_class = $this->id();
		if(!class_exists($target_class))
			return ['error' => 'Unknown class '.$target_class];

		$where = [
			'*set' => 'topics.subject as topic_title',
			'inner_join' => 'topics ON posts.topic_id = topics.id',
			'create_time BETWEEN' => [time() - 86400*93,  time()],
			'balancer_board_topic.is_public=' => true,
			'order' => '-create_time',
			'CHARACTER_LENGTH(`source`)>512',
			'score>=' => 5,
			'limit' => bors()->request()->data('limit', 1),
			'by_id' => true,
		];

		if($id_not = bors()->request()->data('id_not'))
			$where['id NOT IN'] = explode(',', $id_not);

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
				$data['thumbnail_url'] = $x->image()->thumbnail($thumb)->url();

//			$data['title']

			$result[$id] = $data;
		}

		return $result;
	}
}
