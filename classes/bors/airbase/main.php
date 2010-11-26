<?php

class airbase_main extends base_page
{
//	function template() { return 'bors:http://airbase.ru/cms/templates/skins/default/body/'; }
	function template() { return 'airbase/default/index2.html'; }
	function cache_static() { return config('static_forum') ? 600 : 0; }
	function title() { return ec('Авиабаза'); }
	function nav_name() { return ec('авиабаза'); }
	function parents() { return array('http://balancer.ru/'); }
	function create_time() { return strtotime('24.12.1997'); }
}
