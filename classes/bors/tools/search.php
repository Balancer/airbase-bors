<?php

class bors_tools_search extends balancer_board_page
{
	function parents()
	{
		if(empty($_GET['t']))
			return array('/tools/', '/forum/');
		else
			return array(bors_load('balancer_board_topic', $_GET['t']));
	}

	function body_data()
	{
		return array_merge(parent::body_data(), array(
			'me' => bors()->user(),
		));
	}

	function pre_parse()
	{
//		$url = $this->url();
		$url = bors()->request()->url();
		$clean_url = url_clean_params($url);
//		echo "'$url' => '$clean_url'<br/>";
		if($url != $clean_url)
			return go($clean_url);

		return parent::pre_parse();
	}

	function title() { return ec('Поиск по форуму'); }
	function nav_name() { return ec('поиск'); }
	function total_items() { return 0; }
	function q() { return ''; }
	function f()
	{
		$f = @$_GET['f'];
		if(!is_array($f))
			$f = explode(',', urldecode($f));

		return $f;
	}
	function t() { return ''; }
	function s() { return 't'; }
	function x() { return ''; }
	function u() { return ''; }
	function w() { return 'q'; }
	function y() { return ''; }
	function d1() { return ''; }
	function m1() { return ''; }
	function y1() { return ''; }
	function d2() { return ''; }
	function m2() { return ''; }
	function y2() { return ''; }
	function origins() { return bors()->request()->data('origins'); }

	function access() { return $this; }
	function can_action($action, $data) { return true; }
	function can_read() { return true; }
	function _can_list_def() { return $this->can_read(); }

//	function url() { return '/tools/search/'; }
	// Для исправной работы старых кривых ссылок вида http://balancer.ru/tools/search/result/?q=%D1%82%D1%8D%D0%BC2%D1%83&w=a&s=r&class_name=bors_tools_search
	function skip_save() { return true; }

	function is_public_access() { return true; }
	function can_adsense() { return !preg_match('/balancer\.ru/', $_SERVER['HTTP_HOST']); }
	function can_yandex_direct() { return preg_match('/forums\.balancer\.ru/', $_SERVER['HTTP_HOST']); }
}
