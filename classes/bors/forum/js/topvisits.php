<?php

class forum_js_topvisits extends base_js
{
	function can_be_empty() { return true; }

	function pre_show()
	{
		$ret = parent::pre_show();
		header("Content-type", "text/javascript");
        $expire = gmdate('D, d M Y H:i:s', time()+7200).' GMT';
		header("Expires: {$expire}"); 
		
		return $ret;
	}

	function data_providers()
	{
		$top = objects_array('forum_topic', array(
				'num_views>=' => 10,
				'last_visit - first_visit > 600',
				'order' => '(86400*num_views)/(last_visit-first_visit) DESC',
				'limit' => 20,
		));
		
		return array('top' => $top);
	}

	function cache_static() { return rand(3600,7200); }
	function url_engine() { return 'url_calling'; }
}
