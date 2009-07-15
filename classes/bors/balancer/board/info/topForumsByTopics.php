<?php

class balancer_board_info_topForumsByTopics extends base_page_list
{
	function title() { return ec('Форумы с максимальным числом новых тем за последний месяц'); }

	function main_class() { return 'balancer_board_forum'; }
	function order() { return 'COUNT(*) DESC'; }
	function where() { return array('posted >= ' => time() + 31*86400); }
	
	function group() { return 'forum_id'; }
	function limit() { return 10; }
}
