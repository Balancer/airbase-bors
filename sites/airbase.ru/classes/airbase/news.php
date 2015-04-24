<?php

//TODO: убрать старый код из БД HTS http://www.airbase.ru/news/ — не забыть перенести связи и прочее.

class airbase_news extends airbase_page
{
	var $title = 'Новости авиации';
	var $nav_name = 'новости';

//	function create_time() { return strtotime('2007-03-01 20:35:53'); }

	function news()
	{
		$news = json_decode(file_get_contents('http://www.wrk.ru/news/tags/авиация/last.json'), true);
		return $news['news'];
	}
}
