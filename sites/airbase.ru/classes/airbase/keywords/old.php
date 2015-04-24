<?php

class airbase_keywords_old extends bors_object
{
	private $url = false;

	function can_be_empty() { return false; }
	static function id_prepare($id) { return trim(trim(urldecode($id)), '/'); }

	function is_loaded()
	{
		$found = false;
		$kw = $this->id();
//		foreach(file(BORS_SITE.'/webroot/system/keyword-links.map') as $s)
		foreach(file('/var/www/airbase.ru/zim-airbase/Авиабаза/webroot/system/keyword-links.map') as $s)
		{
			if(preg_match('/^\s+/', $s))
			{
				if($found)
					return $this->url = trim($s);

				continue;
			}
			if(trim($s) == $kw)
				$found = true;
		}

		return false;
	}

	function pre_show()
	{
		return go($this->url, true);
	}
}
