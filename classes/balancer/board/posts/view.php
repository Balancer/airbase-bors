<?php

class balancer_board_posts_view extends balancer_board_view
{
	static function container_init()
	{
		jquery::load();
		jquery::plugin('cookie');
		jquery::on_ready(__DIR__.'/view.container-ready.js');
	}
}
