<?  
//    require_once('debug.php');

/*	function do_php($code)
	{
		ob_start();
		eval($code);
		$out = ob_get_contents();
		ob_clean();
		return $out;
	}*/

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
			$template = $GLOBALS['cms']['default_template'];
		}
		
		require_once('Smarty/Smarty.class.php');
		$smarty = new Smarty;
		require('mysql-smarty.php');

		$smarty->compile_dir = $GLOBALS['cms']['cache_dir'].'/smarty-templates_c/';
		$smarty->plugins_dir = $GLOBALS['cms']['base_dir'].'/funcs/templates/plugins/';
		$smarty->cache_dir   = $GLOBALS['cms']['cache_dir'].'/smarty-cache/';

		if(!file_exists($smarty->compile_dir))
			mkdir($smarty->compile_dir, 0775, true);
		if(!file_exists($smarty->cache_dir))
			mkdir($smarty->cache_dir, 0775, true);

		$smarty->caching = empty($data['caching']) ? !$GLOBALS['cms']['cache_disabled'] : $data['caching'];
		$smarty->compile_check = true; 
		$smarty->php_handling = SMARTY_PHP_QUOTE; //SMARTY_PHP_PASSTHRU;
		$smarty->security = false;
		$smarty->cache_modified_check = true;
		$smarty->cache_lifetime = 86400*7;
//		$smarty->secure_dir += array("/home/airbase/forums/cms/funcs/templates");
//		print_r($smarty->secure_dir);

		$modify_time = $data['modify_time'];
		$source = $data['source'];
		$action = @$data['action'];
//		if(!$action)
//			$action = $GLOBALS['cms']['action'];

		$cct = @$data['cache_create_time'];

		if($action || $modify_time > $cct)
			$smarty->clear_cache("hts:{$template}", $uri);
		
		$data['access'] = access_allowed($uri) ? 1 : 0;
		$us = new User();
		$data['level'] = intval($us->data('level'));
		$data['user_id'] = $us->data('id');

		include_once("funcs/actions/subscribe.php");

		$data['subscribed'] = cms_funcs_action_is_subscribed($uri);

		$last_modify = gmdate('D, d M Y H:i:s', $modify_time).' GMT';
   		header('Last-Modified: '.$last_modify);

		if(!$smarty->is_cached("hts:{$template}", $uri))
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

			$smarty->clear_cache("hts:{$template}", $uri);
			
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
		$smarty->assign("main_uri", $GLOBALS['main_uri']);
//		$smarty->assign("action", $GLOBALS['cms']['action']);

		debug("fetch(\"hts:{$template}\", $uri)");
		$out = $smarty->fetch("hts:{$template}", $uri);

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
?>
