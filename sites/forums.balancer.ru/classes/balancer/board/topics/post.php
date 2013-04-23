<?php

class balancer_board_topics_post extends balancer_board_tool
{
	function title() { return 'Новое сообщение в тему ' . $this->topic()->nav_name(); }
	function page_title() { return 'Новое сообщение в тему «' . $this->topic()->title().'»'; }
	function nav_name() { return 'новое сообщение'; }

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), array(
			'topic' => 'balancer_board_topic(id)',
		));
	}

	function pre_show()
	{
		$ret = parent::pre_show();
		jquery_markitup::appear('#editor');
		jquery::on_ready('$("#editor").focus()');
		return $ret;
	}

	function on_action_post($data)
	{
		var_dump($data);
		exit();
	}
}
