<?php
/* Поддержка старого *.hts формата файлов */

class airbase_page_hts_plain extends bors_page
{
	//TODO: на время отладки
	function can_cached() { return false; }
	function can_be_empty() { return false; }

	function storage_engine() { return 'storage_fs_hts'; }

	function parents() { return $this->attr('parents'); }

	var $type = 'hts';

	function init()
	{
		if(preg_match('/^(.+)\.phtml$/', $this->called_url(), $m))
			go($m[1].'/', true);

		return parent::init();
	}

	function pre_show()
	{
		config_set('cache_disabled', true);
		return false;
	}

	function cache_static() { return config('static_forum') ? rand(10*86400, 30*86400) : 0; }
}
