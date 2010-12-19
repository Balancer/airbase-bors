<?php

class balancer_blogs_main extends bors_paginated
{
	function pre_show()
	{
		template_noindex();
		return parent::pre_show();
	}

	//FIXME: разрбраться, почему игнорируется дефолтовый стиль сайта. Возможно, переопределяется явно в user_blog?
//	function template() { return 'blue_spring'; }

	function title() { return ec('Блоги на Balancer.ru'); }
	function nav_name() { return ec("блоги"); }

	function parents() { return array('http://balancer.ru/'); }

	function config_class() { return 'balancer_board_config'; }

	function cache_static() { return config('static_forum') ? 86400*14 : 0; }

	function main_class() { return 'forum_blog'; }

	function order() { return '-id'; }
}
