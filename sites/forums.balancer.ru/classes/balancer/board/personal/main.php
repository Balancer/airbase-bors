<?php

class balancer_board_personal_main extends base_page
{
	function title() { return ec('Персональный раздел'); }
	function is_auto_url_mapped_class() { return true; }
	function nav_name() { return ec('персональное'); }
	function template() { return 'forum/_header.html'; }

	function local_data()
	{
		return array_merge(parent::local_data(), array('me_id' => bors()->user_id()));
	}
}
