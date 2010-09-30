<?php

class balancer_board_personal_main extends base_page
{
	function title() { return ec('Персональный раздел'); }
	function is_auto_url_mapped_class() { return true; }
	function nav_name() { return ec('персональное'); }
	function template() { return 'forum/_header.html'; }
}
