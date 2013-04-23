<?php

class balancer_board_tool extends balancer_board_page
{
	function template() { return 'xfile:balancer/board/tools.tpl'; }

	function pre_show()
	{
		twitter_bootstrap::load();
		return parent::pre_show();
	}
}
