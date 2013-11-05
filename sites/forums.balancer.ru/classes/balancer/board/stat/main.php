<?php

class balancer_board_stat_main extends balancer_board_page
{
	function title() { return ec('Статистика'); }
	function nav_name() { return ec('статистика'); }
	function template() { return 'xfile:forum/page.html'; }
	function is_auto_url_mapped_class() { return true; }
}
