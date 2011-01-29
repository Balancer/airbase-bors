<?php

class balancer_board_mobile_topics_view extends balancer_board_mobile_page
{
	function url($page = NULL) { return '/t'.$this->id().($page > 1 ? ".$page" : ""); }
	function title() { return ($t = $this->topic()) ? $t->title() : 'Ошибочный топик'; }

	function pre_parse() { if(!$this->topic()) return bors_http_error(404); else return parent::pre_parse(); }

	function can_read() { return ($t = $this->topic()) && $t->is_public(); }

	function parents() { return ($t = $this->topic()) ? array($t->forum()->url()) : array(); }

	function auto_objects()
	{
		return array(
			'topic' => 'balancer_board_mobile_topic(id)',
		);
	}
}
