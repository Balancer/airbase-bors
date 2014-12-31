<?php

class forum_topic_rss extends bors_rss
{

	var $items_class_name = 'balancer_board_post';
	var $limit = 50;

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), [
			'topic' => 'balancer_board_topic(id)',
		]);
	}

	function title() { return $this->topic()->title(); }
	function description() { return $this->topic()->description(); }
	function main_url() { return $this->topic()->url(); }

	function pre_show()
	{
		$forum = $this->topic()->forum();

		if(!$forum->can_read())
			return bors_message("Извините, запрашиваемый материал отсутствет, был удалён или у Вас отсутствует к нему доступ");

		config_set('rss_skip_images', true);

		return parent::pre_show();
	}

	function where()
	{
		return [
			'topic_id' => $this->id(),
		];
	}

	function cache_group_depends() { return parent::cache_group_depends() + "balancer-board-topic-".$this->id(); }

	function cache_static()
	{
		if(!$this->topic()->is_public_access())
			return 0;

		$mt = $this->modify_time();

		if($mt < time() - 86400*365)
			return 86400*rand(180, 360);

		if($mt < time() - 86400*30)
			return 86400*rand(7, 30);

		if($mt < time() - 86400*7)
			return rand(3600, 86400);

		if($mt < time() - 86400)
			return rand(600, 1200);

		return rand(60, 300);
	}
}
