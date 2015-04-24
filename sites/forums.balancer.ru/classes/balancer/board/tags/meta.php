<?php

class balancer_board_tags_meta extends balancer_board_meta_main
{
	function main_class() { return 'balancer_board_topic'; }
	function can_be_empty() { return true; }
	function auto_map() { return true; }
}
