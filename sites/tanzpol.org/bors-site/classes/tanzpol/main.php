<?php

class tanzpol_main extends tanzpol_page
{
	var $title_ec = 'Tanzpol';
	var $nav_name_ec = 'политика';
	var $description = 'Политические дискуссии';

	function create_time() { return 1343759827; }

	static function cat_names() { return "tanzpol"; }

	function body_data()
	{
		return json_decode(file_get_contents('http://www.wrk.ru/news/tags/политика/last.json'), true);
	}
}
