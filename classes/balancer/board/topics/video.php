<?php

class balancer_board_topics_video extends balancer_board_topics_blog
{
	function title() { return "Сообщения с видео в теме «{$this->topic()->title()}»"; }
	function nav_name() { return 'видео'; }

	function url_ex($page = NULL)
	{
		return $this->topic()->category()->category_base_full()
			.date("Y/m", $this->topic()->create_time())."/t{$this->id()}/video/"
			.(is_null($page) || $page == $this->default_page() ? '' : "{$page}.html");
	}

	function order() { return 'create_time'; }
	function is_reversed() { return false; }

	function where()
	{
		return array_merge(parent::where(), array(
			'topic_id' => $this->id(),
			'(source LIKE "%flv%"
				OR source LIKE "%youtube%"
				OR source LIKE "%rutube%"
				OR source LIKE "%vimeo%")',
		));
	}
}
