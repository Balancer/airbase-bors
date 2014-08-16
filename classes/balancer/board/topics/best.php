<?php

class balancer_board_topics_best extends balancer_board_paginated
{
	function title() { return "Лучшие сообщения темы «{$this->topic()->title()}»"; }
	function nav_name() { return 'лучшее'; }
	var $main_class = 'balancer_board_post';

	function order() { return '-score'; }

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), [
			'topic' => 'balancer_board_topic(id)',
		]);
	}

	function where()
	{
		return array(
			'topic_id' => $this->id(),
			'score>0',
		);
	}
}
