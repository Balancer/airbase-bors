<?php

class wrk_news_post extends balancer_board_page
{
	function template() { return 'forum/_header.html'; }

	function title()
	{
		return $this->news()->title();
	}

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), [
			'news' => 'balancer_board_post(id)',
		]);
	}

	function posts()
	{
		return balancer_board_post::find([
			'(id = '.$this->id().' OR root_post_id=' . $this->id().')',
			'create_time>=' => $this->news()->create_time(),
		])->order('create_time')->all();
	}
}
