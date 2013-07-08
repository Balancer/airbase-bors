<?php

class balancer_board_posts_body extends balancer_board_posts_view
{
	function url() { return NULL; }
	function url_ex($page) { return NULL; }

	function content()
	{
		return $this->post()->body();
	}
}
