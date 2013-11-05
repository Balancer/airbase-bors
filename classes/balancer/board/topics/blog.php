<?php

class balancer_board_topics_blog extends balancer_board_paginated
{
	function title() { return "Блог темы «{$this->topic()->title()}»"; }
	function nav_name() { return 'блог'; }
	function parents() { return array($this->topic()); }

	function main_class() { return 'balancer_board_post'; }
	function config_class() { return 'balancer_board_config'; }

	function is_reversed() { return true; }

	function url() { return $this->url_ex($this->default_page()); }

	function url_ex($page = NULL)
	{
		$cat_url = $this->topic()->category()->url();
		if(preg_match('/viewcat.php/', $cat_url))
		{
			debug_hidden_log('error_category', "Incorrect category '{$this->topic()->category()->debug_title()}' URL: ".$cat_url);
			$cat_url = "http://www.balancer.ru/";
		}

		return $cat_url.date("Y/m", $this->topic()->create_time())."/t{$this->id()}/blog"
			.(is_null($page) || $page == $this->default_page() ? '' : "/{$page}.html");
	}

	function order() { return 'create_time'; }

	function where()
	{
		return array_merge(parent::where(), array(
			'left_join' => array('`blog` ON `blog`.`post_id` = `posts`.`id`'),
			'topic_id' => $this->id(),
			'(`answer_to_post_id` = 0 OR `blog`.`post_id` IS NOT NULL)',
		));
	}

	function body_data()
	{
		return array_merge(parent::body_data(), array(
			'topic' => $this->topic(),
			'posts' => $this->items(),
		));
	}

//	function body_template() { return 'xfile:forum/topic.html'; }

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), array(
			'topic' => 'balancer_board_topic(id)',
		));
	}
}
