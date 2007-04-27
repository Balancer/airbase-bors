<?
	hts_data_posthandler_add("!.*!", '*', 'params_bors_data');
	function params_bors_data($uri, $m, $plugin_data, $key)
	{	
		if(function_exists('class_load') && ($obj = borsclass_uri_load($uri)) && method_exists($obj, $key))
			return $obj->$key();
		else
			return NULL;
	}
