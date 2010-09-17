<?php

class balancer_board_posts_show extends bors_page
{
	function config_class() { return 'balancer_board_config'; }

	function parents() { return array($this->post()->topic()->url()); }

	function auto_objects()
	{
		return array(
			'post' => 'balancer_board_post(id)',
		);
	}

	function title() { return $this->post()->title(); }
	function nav_name() { return $this->post()->nav_name(); }
	function description() { return $this->post()->description(); }

	function local_data()
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
    	if(!$this->post()->topic()->forum()->can_read())
		{
			template_noindex();
			return bors_message("Извините, доступ к этому ресурсу закрыт для Вас");
		}

		return false;
	}
}
