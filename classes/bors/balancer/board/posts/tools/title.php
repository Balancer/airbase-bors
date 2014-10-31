<?php

class balancer_board_posts_tools_title extends bors_page
{
	function template() { return 'null.html'; }

	function auto_objects()
	{
		return array(
			'post' => 'balancer_board_post(id)',
		);
	}

	function pre_show()
	{
		if(!bors()->user_id() || !bors()->user()->is_coordinator())
			return "Только для координаторов!";

		return false;
	}

	function body_data()
	{
		return array(
			'p' => $this->post(),
			'owner_id' => $this->post()->owner_id(),
		);
	}
}
