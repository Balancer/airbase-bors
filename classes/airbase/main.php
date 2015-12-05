<?php

class airbase_main extends airbase_page
{
//	function template() { return 'bors:http://www.airbase.ru/cms/templates/skins/default/body/'; }
//	function template() { return 'airbase/default/index2.html'; }
	function cache_static() { return rand(300, 600); }
	function title() { return ec('Авиабаза'); }
	function nav_name() { return ec('авиабаза'); }
	function parents() { return array('http://www.balancer.ru/'); }
	function create_time() { return strtotime('24.12.1997'); }

	function body_data()
	{
		$data = json_decode(file_get_contents('http://www.wrk.ru/news/tags/авиация/last.json'), true);
		$site_news = bors_load('airbase_pages_markdown', '/../news.md');

		$data['site_news'] = $site_news;

		return $data;
	}
}
