<?php

class balancer_board_posts_show extends bors_view
{
	function main_class() { return 'balancer_board_post'; }

	function config_class() { return 'balancer_board_config'; }

	function can_be_empty() { return false; }
	function is_loaded() { return $this->post() != NULL; }

	function parents()
	{
		$topic = $this->post()->topic();
		return array($topic ? $topic->url() : 'http://forums.balancer.ru/');
	}

	function auto_objects()
	{
		return array(
			'post' => 'balancer_board_post(id)',
		);
	}

	function title() { return $this->post()->title(); }
	function nav_name() { return $this->post()->nav_name(); }
	function description() { return $this->post()->description(); }

	function body_data()
	{
		return array(
			'post' => $this->post(),
			'topic' => $this->post()->topic(),
			'forum' => $this->post()->topic()->forum(),
		);
	}

	function url() { return $this->post()->url(); }

	function pre_show()
	{
		if(!($topic = $this->post()->topic()))
		{
			bors_debug::syslog('error_post_lost', "Lost post (not found topic {$this->post()->topic_id()}) ".$this->post()->id(), false);
			return bors_message(ec("Тема данного сообщения была утеряна<br/>\n=====================================<br/>\n<small>").lcml_bbh($this->post()->source())."</small>");
		}

    	if(!$topic->forum()->can_read())
		{
			template_noindex();
			return bors_message("Извините, запрашиваемый материал отсутствет, был удалён или у Вас отсутствует к нему доступ");
		}

		return go($this->post()->url_in_container());

		return false;
	}

	function is_public_access() { return $this->post()->topic()->is_public_access(); }
}
