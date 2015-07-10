<?php

class airbase_user_warnings extends balancer_board_page
{
	function class_file() { return __FILE__; } // Не сносить, так как есть класс-наследник с этим же шаблоном!

	function can_be_empty() { return false; }
	function is_loaded() { return (bool) $this->user(); }

	function template()
	{
		return 'forum/common.html';
	}

	private $user;
	function user()
	{
		if($this->user === NULL)
			$this->user = bors_load('bors_user', $this->id());

		return $this->user;
	}

	function body_data($skip_passive = false)
	{
		template_noindex();

		$data = array(
			'ref' => urldecode(@$_GET['ref']) or @$_SERVER['HTTP_REFERER'],
			'skip_passive' => $skip_passive,
		);

		if(!$skip_passive)
			$data['passive_warnings'] = array_reverse(bors_find_all('airbase_user_warning', array(
				'user_id=' => $this->id(),
				'`expired_timestamp` <= NOW()',
				'order' => 'time',
				'page' => $this->page(),
				'per_page' => $this->items_per_page(),
			)));

		if(!$this->page() || $this->page() == $this->total_pages())
			$data['active_warnings']  = array_reverse(bors_find_all('airbase_user_warning', array(
				'user_id' => $this->id(),
				'`expired_timestamp` > NOW()',
				'order' => 'time',
//				'limit' => 10,
			)));

		return $data;
	}

	function title() { return ec("Штрафы и поощрения пользователя ") . $this->user()->title(); }

	function nav_name() { return ec('Штрафы'); }

	function parents() { return array($this->user()->url()); }

	function total_items() { return bors_count('airbase_user_warning', array('user_id=' => $this->id(), '`expired_timestamp`<=NOW()')); }
	function default_page() { return $this->total_pages(); }

	function url_ex($page)
	{
		if(!$page || $this->total_pages() == 1)
			$url = "http://www.balancer.ru/user/".$this->id()."/warnings.html"; 
		else
			$url = "http://www.balancer.ru/user/".$this->id()."/warnings,{$page}.html"; 

		if($qs = @$_SERVER['QUERY_STRING'])
			$url .= '?'.$qs;

		return $url;
	}

	function cache_static() { return config('static_forum') ? rand(80000, 90000) : 0; }
	function is_public_access() { return false; }
}
