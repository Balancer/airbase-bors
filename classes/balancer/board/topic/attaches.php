<?php

class balancer_board_topic_attaches extends balancer_board_paginated
{
	function main_class() { return 'balancer_board_attach'; }

	function where()
	{
		return array(
				'inner_join' => 'balancer_board_post ON balancer_board_post.id = balancer_board_attach.post_id',
				'balancer_board_post.topic_id = ' => $this->id(),
		);
	}

	function order() { return 'balancer_board_post.create_time'; }

	function config_class() { return 'balancer_board_config'; }

	function items_per_page() { return 60; }

	function auto_objects()
	{
		return array(
			'topic' => 'balancer_board_topic(id)',
		);
	}

	function title() { return ec('Все приложения темы «').$this->topic()->title().ec('»'); }
	function nav_name() { return ec('все приложения'); }
	function parents() { return array($this->topic()->url()); }
	function url_ex($page)
	{
		$t = $this->topic();
		return $t->base_url().strftime("%Y/%m/", $t->modify_time()).'t'.$t->id().'/attaches/' . ($page > 1 ? "$page.html" : '');
	}

	function pre_show()
	{
		$t = $this->topic();

		if(!$t->forum() || !$t->forum()->can_read())
		{
			template_noindex();
			return bors_message("Извините, запрашиваемый материал отсутствет, был удалён или у Вас отсутствует к нему доступ");
		}

		return parent::pre_show();
	}
}
