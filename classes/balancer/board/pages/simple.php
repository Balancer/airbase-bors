<?php

class balancer_board_pages_simple extends bors_page
{
	function pre_show()
	{
		twitter_bootstrap::load();
		return parent::pre_show();
	}

	function template() { return 'xfile:bootstrap/index.tpl'; }
}
