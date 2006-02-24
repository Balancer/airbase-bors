<?  
	function template_assign_data($assign_template, $data=array(), $uri=NULL)
	{
//		echo "tpl=$assign_template";
	
		require_once('Smarty/Smarty.class.php');
		$smarty = new Smarty;
		require('mysql-smarty.php');
		require('smarty-register.php');

		$smarty->compile_dir = $GLOBALS['cms']['cache_dir'].'/smarty-templates_c/';
		$smarty->plugins_dir = $GLOBALS['cms']['base_dir'].'/funcs/templates/plugins/';
		$smarty->cache_dir   = $GLOBALS['cms']['cache_dir'].'/smarty-cache/';

		if(!file_exists($smarty->compile_dir))
			mkdir($smarty->compile_dir, 0775, true);
		if(!file_exists($smarty->cache_dir))
			mkdir($smarty->cache_dir, 0775, true);

		$caching = !is_null($uri)
				&& @$data['caching'] !== false
				&& @$GLOBALS['cms']['cache_disabled'] !== true
			;
			
		$smarty->caching = $caching;
		$smarty->compile_check = true; 
		$smarty->php_handling = SMARTY_PHP_QUOTE; //SMARTY_PHP_PASSTHRU;
		$smarty->security = false;
		$smarty->cache_modified_check = true;
		$smarty->cache_lifetime = 86400*7;
		$smarty->secure_dir += array(dirname($assign_template));
//		print_r($smarty->secure_dir); exit();

		$modify_time = empty($data['modify_time']) ? time() : $data['modify_time'];

		if(!$caching)
			$smarty->clear_cache($assign_template, $uri);
		
		if(!$caching || !$smarty->is_cached($assign_template, $uri))
		{
			foreach($data as $key => $val)
			{
//				echo "$key -> ".print_r($val,true)."<br />\n";
				$$key = $val;
				$smarty->assign($key, $val);
			}
	
			$smarty->assign("page_template", $assign_template);
			$smarty->assign("time", time());

			$smarty->clear_cache($assign_template, $uri);
			
			header("X-Recompile: Yes");
		}

		$smarty->assign("uri", $uri);
		$smarty->assign("main_uri", @$GLOBALS['main_uri']);

		debug("fetch(\"hts:{$assign_template}\", $uri)");
		$out = $smarty->fetch($assign_template, $uri);

		$out = preg_replace("!<\?php(.+?)\?>!es", "do_php(stripslashes('$1'))", $out);

		return $out;
	}
?>
