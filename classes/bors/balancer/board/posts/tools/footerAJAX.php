<?php

class balancer_board_posts_tools_footerAJAX extends base_page
{
	function object() { return $this->load_attr('object', object_load($this->id())); }
	function template() { return 'null.html'; }

	function pre_show()
	{
		if(!bors()->user_id())
			return "Только для зарегистрированных пользователей!";
		
		return false;
	}

	function local_data()
	{
		return array(
			'p' => $this->object(),
			'id' => $this->object()->id(),
			'owner_id' => $this->object()->owner_id(),
		);
	}
}
