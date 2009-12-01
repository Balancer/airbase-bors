<?php

class balancer_board_new_topics extends base_page_paged
{
	function main_class() { return 'forum_topic'; }
	function order() { return '-create_time'; }
	function template() { return 'forum/_header.html'; }
	function title() { return 'Новые темы за месяц'; }
	function nav_name() { return 'новые темы'; }
	function where() { return array('posted>' => time()-86400*31); }
}
