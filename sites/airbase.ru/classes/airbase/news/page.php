<?php

class airbase_news_page extends bors_view
{
	var $model_class = 'balancer_board_post';

	function url() { return 'http://www.airbase.ru/news/'.date('Y/m/d', $this->post()->create_time()).'/'.$this->id().'.html'; }
	function url_ex($foo) { return $this->url(); }

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), [
			'post' => 'balancer_board_post(id)',
		]);
	}
}
