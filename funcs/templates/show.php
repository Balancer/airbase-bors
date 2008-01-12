<?  
//    require_once('debug.php');

	function template_assign_and_show($uri, $template, $data=NULL)
	{
//		echo "($uri, $template, $data)";
		echo template_assign($uri, $template, $data);
		return true;
	}
	
	function template_assign($uri, $template, $data=NULL)
	{
	
		if(is_null($data))
		{
			$data = $template;
			$hts = &new DataBaseHTS();
			$template = $hts->get_data($uri, 'template');
			if(!$template)
				$template = $GLOBALS['cms']['default_template'];
		}
		
		require_once('Smarty/Smarty.class.php');
		$smarty = &new Smarty;
		require('mysql-smarty.php');
		require('smarty-register.php');

		$smarty->compile_dir = $GLOBALS['cms']['cache_dir'].'/smarty-templates_c/';
		$smarty->plugins_dir = $GLOBALS['cms']['base_dir'].'/funcs/templates/plugins/';
		$smarty->cache_dir   = $GLOBALS['cms']['cache_dir'].'/smarty-cache/';

		if(!file_exists($smarty->compile_dir))
			mkdir($smarty->compile_dir, 0775, true);
		if(!file_exists($smarty->cache_dir))
			mkdir($smarty->cache_dir, 0775, true);

		$smarty->caching = empty($data['caching']) ? empty($GLOBALS['cms']['templates_cache_disabled']) : $data['caching'];
		$smarty->compile_check = true; 
		$smarty->php_handling = SMARTY_PHP_QUOTE; //SMARTY_PHP_PASSTHRU;
		$smarty->security = false;
		$smarty->cache_modified_check = true;
		$smarty->cache_lifetime = 86400*7;
//		$smarty->secure_dir += array("/home/airbase/forums/cms/funcs/templates");
//		print_r($smarty->secure_dir);

		$modify_time = empty($data['modify_time']) ? time() : $data['modify_time'];
		$modify_time = max(@$data['compile_time'], $modify_time);

		$source = @$data['source'];
		$action = @$data['action'];
//		if(!$action)
//			$action = $GLOBALS['cms']['action'];

		$cct = @$data['cache_create_time'];


		$smarty->template_dir = dirname(preg_replace("!^xfile:!", "", $template));
		
		$data['access'] = access_allowed($uri) ? 1 : 0;
		$us = &new User();
		$data['level'] = intval($us->data('level'));
		$data['user_id'] = $us->data('id');
		$data['user_name'] = $us->data('name');

		include_once("funcs/actions/subscribe.php");

		$data['subscribed'] = cms_funcs_action_is_subscribed($uri);

		$last_modify = gmdate('D, d M Y H:i:s', $modify_time).' GMT';
   		header('Last-Modified: '.$last_modify);

		if(empty($data['body']) && !empty($data['source']))
		{
			$lcml_params = NULL;
			if(!empty($data['lcml_params']))
				$lcml_params = $data['lcml_params'];
			$data['body'] = lcml($data['source'], $lcml_params);
		}

		if(empty($data['ref']) && !empty($_SERVER['HTTP_REFERER']))
			$data['ref'] = $_SERVER['HTTP_REFERER'];
		
		if(!$uri || !$smarty->is_cached("hts:{$template}", $uri))
		{
			foreach($data as $key => $val)
			{
//				echo "$key -> ".print_r($val,true)."<br />\n";
				$$key = $val;
				$smarty->assign($key, $val);
			}
	
			$smarty->assign("views_average", sprintf("%.1f",86400*@$views/(@$views_last-@$views_first+1)));
			$smarty->assign("page_template", $template);
			$smarty->assign("time", time());

			header("X-Recompile: Yes");
		}
		else
		{
			if(strstr($source, '[module')===false)
			{
				$hdr = getallheaders();
				if(isset($hdr['If-Modified-Since']))
				{ 
					// Разделяем If-Modified-Since (Netscape < v6 отдаёт их неправильно) 
					$modifiedSince = explode(';', $hdr['If-Modified-Since']); 
					// Преобразуем запрос клиента If-Modified-Since в таймштамп
					$modifiedSince = strtotime($modifiedSince[0]); 
				} 
				else 
				{ 
					// Устанавливаем время модификации в ноль
					$modifiedSince = 0; 
				}

				if($modifiedSince >= $modify_time)
				{
					header("HTTP/1.1 304 Not Modified");
					echo "Not modified since $last_modify";
					return false;
				}
			}
		}

		if(strstr($source, '[module')!==false)
		{
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
			header('Cache-Control: no-store, no-cache, must-revalidate'); 
			header('Cache-Control: post-check=0, pre-check=0', false); 
			header('Pragma: no-cache');
		}

		$smarty->assign("uri", $uri);
		$smarty->assign("main_uri", @$GLOBALS['main_uri']);
//		$smarty->assign("action", $GLOBALS['cms']['action']);

//		debug("fetch(\"hts:{$template}\", $uri)");

		if(preg_match('!^http://!',$template))
			$template = "hts:".$template;

		if(preg_match('!^/!',$template))
			if(file_exists($template))
				$template = "xfile:$template";
			else
				$template = "hts:http://{$_SERVER['HTTP_HOST']}$template";
		
		if(!$smarty->template_exists($template))
			$template = $GLOBALS['cms']['default_template'];
		if(!$smarty->template_exists($template))
			$template = $GLOBALS['cms']['default_template_file'];

//		echo $template;

		$out = $smarty->fetch($template, $uri);

		$out = preg_replace("!<\?php(.+?)\?>!es", "do_php(stripslashes('$1'))", $out);

//<div align="right" style="margin: 8px;">
//<small>debug_page_stat();</small>
//	if(user_data('member_id') == 1)
//		xdebug_dump_function_profile(XDEBUG_PROFILER_CPU); 
//</div>
//</body>
//</html>
		return $out;
	}

	function smarty_init()
	{
		require_once('Smarty/Smarty.class.php');

		$smarty = &new Smarty;

		require('mysql-smarty.php');

		$smarty->compile_dir = $GLOBALS['cms']['cache_dir'].'/smarty-templates_c/';
		$smarty->plugins_dir = $GLOBALS['cms']['base_dir'].'/funcs/templates/plugins/';
		$smarty->cache_dir   = $GLOBALS['cms']['cache_dir'].'/smarty-cache/';

		if(!file_exists($smarty->compile_dir))
			mkdir($smarty->compile_dir, 0775, true);
		if(!file_exists($smarty->cache_dir))
			mkdir($smarty->cache_dir, 0775, true);

		$smarty->caching = empty($data['caching']) ? !$GLOBALS['cms']['templates_cache_disabled'] : $data['caching'];
		$smarty->compile_check = true; 
		$smarty->php_handling = SMARTY_PHP_QUOTE; //SMARTY_PHP_PASSTHRU;
		$smarty->security = false;
		$smarty->cache_modified_check = true;
		$smarty->cache_lifetime = 86400*7;
//		$smarty->secure_dir += array("/home/airbase/forums/cms/funcs/templates");
//		print_r($smarty->secure_dir);

		return $smarty;
	}

	function get_parsed_page($uri, $data = array())
	{

		$hts = &new DataBaseHTS();

		if($template = $hts->get_data($uri, 'template'))
		{
			$tpl1 = "/$template/";
			$tpl2 = "/$template/";
		}
		else
		{
			$tpl1 = "/default/";
			$tpl2 = "";
		}
		
		
		foreach(array(
			"{$uri}template$tpl2/",
			"{$GLOBALS['cms']['base_uri']}/templates$tpl1",
			"{$GLOBALS['cms']['base_uri']}/templates$tpl2/body",
		) as $tpl)
		{
//			echo "Check '$tpl'<br />";
			if($hts->get_data($tpl, 'source'))
				break;
		}
		
        if(!$hts->get_data($tpl, 'source'))// || ($action && $action!='virtual'))
            $tpl = $GLOBALS['cms']['default_template'];

//		echo $tpl;

        if(!$hts->get_data($tpl, 'source'))
            $tpl = $GLOBALS['cms']['default_template_file'];

		$tpl = preg_match("!^/!", $tpl) ? $tpl : "hts:$tpl";
		
		$smarty = smarty_init();

		$modify_time = max($hts->get_data($page, 'modify_time'), $hts->get_data($page, 'compile_time'));

		$source = $hts->get_data($uri, 'source');

		$access = access_allowed($uri) ? 1 : 0;
		$us = &new User();
		$level = $us->data('level');
		$user_id = $us->data('id');
		$user_name = $us->data('name');

		include_once("funcs/actions/subscribe.php");
		$subscribed = cms_funcs_action_is_subscribed($uri);

		$last_modify = gmdate('D, d M Y H:i:s', $modify_time).' GMT';
   		header('Last-Modified: '.$last_modify);

		if(empty($data['ref']) && !empty($_SERVER['HTTP_REFERER']))
			$data['ref'] = $_SERVER['HTTP_REFERER'];
		
		if(!$smarty->is_cached("hts:{$template}", $uri))
		{
			foreach(split(" +","title body") as $key)
				$smarty->assign($key, $hts->get_data($uri, $key));

			foreach(split(" +","access level user_id user_name") as $key)
				$smarty->assign($key, $$key);

			foreach($data as $key => $value)
				$smarty->assign($key, $value);
	
			$smarty->assign("views_average", sprintf("%.1f",86400*@$views/(@$views_last-@$views_first+1)));
			$smarty->assign("page_template", $template);
			$smarty->assign("time", time());

			
			header("X-Recompiled: Yes");
		}
		else
		{
			if(strstr($source, '[module')===false)
			{
				$hdr = getallheaders();
				if(isset($hdr['If-Modified-Since']))
				{ 
					// Разделяем If-Modified-Since (Netscape < v6 отдаёт их неправильно) 
					$modifiedSince = explode(';', $hdr['If-Modified-Since']); 
					// Преобразуем запрос клиента If-Modified-Since в таймштамп
					$modifiedSince = strtotime($modifiedSince[0]); 
				} 
				else 
				{ 
					// Устанавливаем время модификации в ноль
					$modifiedSince = 0; 
				}

				if($modifiedSince >= $modify_time)
				{
					header("HTTP/1.1 304 Not Modified");
					echo "Not modified since $last_modify";
					return false;
				}
			}
		}

		if(strstr($source, '[module')!==false)
		{
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
			header('Cache-Control: no-store, no-cache, must-revalidate'); 
			header('Cache-Control: post-check=0, pre-check=0', false); 
			header('Pragma: no-cache');
		}

		$smarty->assign("uri", $uri);
		$smarty->assign("main_uri", @$GLOBALS['main_uri']);
		
		if(is_array(@$GLOBALS['cms']['smarty']))
			foreach($GLOBALS['cms']['smarty'] as $key => $val)
				$smarty->assign($key, $val);
			
		foreach(split(' ', 'host_name main_host_uri') as $key)
			$smarty->assign($key, @$GLOBALS['cms'][$key]);
//		$smarty->assign("action", $GLOBALS['cms']['action']);

//		echo("fetch(\"$tpl\", $uri)");

		if($tpl{0}=='/')
			if(file_exists($tpl))
				$tpl = "xfile:".$tpl;
			else
				$tpl = "hts:http://{$_SERVER['HTTP_HOST']}$tpl";

		if($uri && ($action || $modify_time > $cct))
			$smarty->clear_cache($tpl, $uri);

		$out = $smarty->fetch($tpl, $uri);

		$out = preg_replace("!<\?php(.+?)\?>!es", "do_php(stripslashes('$1'))", $out);

//<div align="right" style="margin: 8px;">
//<small>debug_page_stat();</small>
//	if(user_data('member_id') == 1)
//		xdebug_dump_function_profile(XDEBUG_PROFILER_CPU); 
//</div>
//</body>
//</html>
		return $out;
	}
