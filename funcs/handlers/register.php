<?
	function register_handler($uri_pattern, $func = NULL)
	{
		if ($func == NULL)
		{
			$func = $uri_pattern;
			$uri_pattern = "!.*!";
		}

		if(!empty ($_GET['debug']))
			echo "Register uri '$uri_pattern' handler: $func<br/>";

		$GLOBALS['cms_patterns'][$uri_pattern] = $func;
	}

	function register_action($regexp, $action_type, $func = NULL)
	{
		if (!$func)
		{
			$func = $action_type;
			$action_type = $regexp;
			$regexp = "!.*!";
		}

		if (empty ($GLOBALS['cms_actions'][$action_type][$regexp]))
			$GLOBALS['cms_actions'][$action_type][$regexp] = $func;
	}

	function register_alias($uri_regexp, $function)
	{
		$GLOBALS['cms_aliases'][$uri_regexp] = $function;
	}

	require_once("funcs.php");
	function hts_data_prehandler_add($regexp, $data_key, $func)
	{
//	if (!empty ($_GET['debug']))
//		echo "<small>Add pre function $func to uri like '$regexp' for key $data_key</small><br />";

		if(!empty($GLOBALS['cms']['plugin_data']['base_uri']))
		{
			$regexp = "!".preg_quote($GLOBALS['cms']['plugin_data']['base_uri'], "!").$regexp.'$!';
//			echo "--- plugin pre $data_key : $regexp = ".print_r($GLOBALS['cms']['plugin_data'], true)."<br />";
		}
		
//		echo "$data_key: $regexp <br />";

		$GLOBALS['cms']['data_prehandler'][$data_key][$regexp] = array(
			'func' => make_func($func),
			'plugin_data' => @$GLOBALS['cms']['plugin_data'],
		);
//		krsort($GLOBALS['cms']['data_prehandler'][$data_key]);
	}

	function hts_data_posthandler_add($regexp, $data_key, $func)
	{
//		if (!empty ($_GET['debug']))
//			echo "<small>Add post function $function to uri like '$regexp' for key $data_key</small><br />";

		if(!empty($GLOBALS['cms']['plugin_data']))
		{
			$regexp = "!".preg_quote($GLOBALS['cms']['plugin_data']['base_uri'], "!").$regexp.'$!';
//			echo "plugin $data_key : $regexp = ".print_r($GLOBALS['cms']['plugin_data'], true)."<br />";
		}

		$GLOBALS['cms']['data_posthandler'][$data_key][$regexp] = array(
			'func' => make_func($func),
			'plugin_data' => @$GLOBALS['cms']['plugin_data'],
		);
//		krsort($GLOBALS['cms']['data_posthandler'][$data_key]);
	}

	function hts_data_prehandler($pattern, $data)
	{
		foreach ($data as $key => $value)
		{
			if ($value == 'default')
				continue;

			if (function_exists($value))
			{
				hts_data_prehandler_add($pattern, $key, $value);
				continue;
			}

			hts_data_prehandler_add($pattern, $key, create_function('$uri, $m', "return \"".addslashes($value)."\";"));
		}

		if (empty ($data['parent']))
		{
			if ($pattern)
				hts_data_prehandler_add($pattern, 'parent', create_function('$uri, $m, $plugin_data', 'return array($plugin_data["base_uri"].@$m[1]);'));
			else
				hts_data_prehandler_add($pattern, 'parent', create_function('$uri, $m, $plugin_data', 'return array($plugin_data["parent_uri"]);'));
		}
		
		if (empty ($data['nav_name']))
			hts_data_prehandler_add($pattern, 'nav_name', create_function('$uri, $m', '$hts = new DataBaseHTS(); return strtolower($hts->get_data($uri, "title"));'));

		if (empty ($data['source']))
			hts_data_prehandler_add($pattern, 'source', create_function('$uri, $m', 'return NULL;'));

		if (empty ($data['modify_time']))
			hts_data_prehandler_add($pattern, 'modify_time', create_function('$uri, $m', 'return time();'));

		if (empty ($data['create_time']))
			hts_data_prehandler_add($pattern, 'create_time', create_function('$uri, $m', 'return time();'));
	}

	function hts_data_posthandler($pattern, $data)
	{
		foreach($data as $key => $value)
		{
			if($value == 'default')
				continue;

			if(function_exists($value))
			{
				hts_data_posthandler_add($pattern, $key, $value);
				continue;
			}

			hts_data_posthandler_add($pattern, $key, create_function('$uri, $m', "return \"".addslashes($value)."\";"));
		}

		if(empty ($data['parent']))
			hts_data_posthandler_add($pattern, 'parent', create_function('$uri, $m', 'return array($m[1]);'));

		if(empty ($data['nav_name']))
			hts_data_posthandler_add($pattern, 'nav_name', create_function('$uri, $m', '$hts = new DataBaseHTS(); return strtolower($hts->get_data($uri, "title"));'));

		if(empty ($data['source']))
			hts_data_posthandler_add($pattern, 'source', create_function('$uri, $m', 'return NULL;'));

		if(empty ($data['modify_time']))
			hts_data_posthandler_add($pattern, 'modify_time', create_function('$uri, $m', 'return time();'));

		if(empty ($data['create_time']))
			hts_data_posthandler_add($pattern, 'create_time', create_function('$uri, $m', 'return time();'));
	}
