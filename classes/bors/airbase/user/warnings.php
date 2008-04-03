<?php

class airbase_user_warnings extends base_page
{
	function template() { return 'forum/common.html'; }

	private $user;
	function user()
	{
		if($this->user === NULL)
			$this->user = object_load('bors_user', $this->id());
		
		return $this->user;
	}

	function preParseProcess()
	{
//		if(!$this->id() || !$this->user())
//			return go('/users/');
			
		return false;
	}

	function data_providers()
	{
		$data = array(
			'ref' => urldecode(@$_GET['ref']) or @$_SERVER['HTTP_REFERER'],
			'passive_warnings' => array_reverse(objects_array('airbase_user_warning', array(
				'user_id=' => $this->id(),
				'time<=' => time()-86400*30,
				'order' => 'time',
				'page' => $this->page(),
				'per_page' => $this->items_per_page(),
			))),
		);
		
		if(!$this->page() || $this->page() == $this->total_pages())
			$data['active_warnings']  = array_reverse(objects_array('airbase_user_warning', array('user_id=' => $this->id(), 'time>' => time()-86400*30, 'order' => 'time')));

		return $data;
	}

	function title() { return ec("Штрафы пользователя ") . $this->user()->title(); }

	function nav_name() { return ec('Штрафы'); }
	
	function parents() { return array($this->user()); }
	
	function total_items() { return objects_count('airbase_user_warning', array('user_id=' => $this->id(), 'time<=' => time()-86400*30)); }
	function default_page() { return $this->total_pages(); }

	function url($page = NULL)
	{	
		if(!$page || $this->total_pages() == 1)
			return "http://balancer.ru/user/".$this->id()."/warnings.html"; 
		else
			return "http://balancer.ru/user/".$this->id()."/warnings,{$page}.html"; 
	}
}
