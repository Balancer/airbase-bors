<?php

class balancer_board_topics_images extends balancer_board_topics_blog
{
	function title() { return "Сообщения с картинками в теме «{$this->topic()->title()}»"; }
	function nav_name() { return 'изображения'; }

	function url_ex($page = NULL)
	{
		return $this->topic()->category()->url()
			.date("Y/m", $this->topic()->create_time())."/t{$this->id()}/images"
			.(is_null($page) || $page == $this->default_page() ? '' : "/{$page}.html");
	}

	function order() { return 'create_time'; }
	function is_reversed() { return false; }

	function where()
	{
		return array_merge(parent::where(), array(
			'topic_id' => $this->id(),
			'(source LIKE "%jpg%"
				OR source LIKE "%png%"
				OR source LIKE "%img%"
				OR source LIKE "%gif%")',
		));
	}
}
