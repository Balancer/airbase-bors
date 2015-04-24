<?php

class balancer_board_tags_main extends balancer_board_tags_meta
{
	function title() { return 'Тег-форумы'; }
	function nav_name() { return 'теги'; }

	function body_data()
	{
		template_noindex();

		return array(
			'tags_top' => $this->tags_top(),
		);
	}

	function tags_top()
	{
		return bors_find_all('common_keyword', array(
				'target_containers_count>' => 100,
				'order' => '-target_containers_count',
				'limit' => 20,
				'by_id' => true,
		));
	}
}
