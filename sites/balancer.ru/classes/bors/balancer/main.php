<?php

class balancer_main extends base_page
{
	function title() { return ec('Сайт расходящихся тропок'); }
	function nav_name() { return 'Balancer.Ru'; }
	function template() { return 'blue_spring'; }
	function can_public_load() { return true; }

	function cache_static() { return 600; }
}
