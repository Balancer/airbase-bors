<?php

class airbase_files_webroot extends bors_files_webroot
{
	function doc_root() { return '/var/www/airbase.ru/htdocs'; }

	function cache_static_dir() { return $this->doc_root().'/cache-static'; }
//	function cache_static_path() { return '/cache-static'; }

	function webroot() { return '/var/www/airbase.ru/bors-site/webroot'; }
}
