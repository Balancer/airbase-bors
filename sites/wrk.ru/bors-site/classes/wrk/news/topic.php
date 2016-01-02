<?php

class wrk_news_topic extends balancer_board_page
{
	function template() { return 'forum/_header.html'; }

	function title()
	{
		return 'Новости по теме: '.$this->topic()->title();
	}

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), [
			'topic' => 'balancer_board_topic(id)',
		]);
	}

	function posts()
	{
		return balancer_board_post::find(['topic_id' => $this->id(), 'answer_to_post_id' => 0])->order('create_time')->all();
	}
}
