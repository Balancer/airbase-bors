<?php

class balancer_tests_bors_demo_jsmod_main extends bors_page
{
	var $title_ec = "Тест JS-модулей";
	var $is_auto_url_mapped_class = true;

	function cache_static() { return 600; }
}
