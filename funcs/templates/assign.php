<?  
	function template_assign_data($assign_template, $data=array(), $uri=NULL)
	{
//		echo "tpl=$assign_template";
//		print_r($data);
	
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
//		print_r($smarty->secure_dir); exit();

		$caller = array_shift(debug_backtrace());
		$caller_path = dirname($caller['file']);
		
		$smarty->secure_dir += array($caller_path);

		$template_uri = $assign_template;
		if(!$smarty->template_exists($template_uri))
			$template_uri = "xfile:$caller_path/".$assign_template;
		if(!$smarty->template_exists($template_uri))
			$template_uri = "xfile:".$assign_template;
		if(!$smarty->template_exists($template_uri))
			$template_uri = $GLOBALS['cms']['default_template'];

		$modify_time = empty($data['modify_time']) ? time() : $data['modify_time'];

		if(!$caching)
			$smarty->clear_cache($template_uri, $uri);
		
		if(!$caching || !$smarty->is_cached($template_uri, $uri))
		{
			foreach($data as $key => $val)
			{
//				echo "$key -> ".print_r($val,true)."<br />\n";
				$$key = $val;
				$smarty->assign($key, $val);
			}
	
			$smarty->assign("page_template", $assign_template);
			$smarty->assign("template_uri", $template_uri);
			$smarty->assign("template_dirname", dirname($template_uri));
			$smarty->assign("time", time());

			$smarty->clear_cache($template_uri, $uri);
			
			header("X-Recompile: Yes");
		}

		$smarty->assign("uri", $uri);

		if(empty($data['main_uri']))
			$smarty->assign("main_uri", @$GLOBALS['main_uri']);

		if(preg_match('!^http://!',$template_uri))
			$template_uri = "hts:".$template_uri;

//		echo $template_uri;

		if($template_uri{0}=='/')
			if(file_exists($template_uri))
				$template_uri = "xfile:".$template_uri;
			else
				$template_uri = "hts:http://{$_SERVER['HTTP_HOST']}$template_uri";

		$out = $smarty->fetch($template_uri, $uri);

		$out = preg_replace("!<\?php(.+?)\?>!es", "do_php(stripslashes('$1'))", $out);

		return $out;
	}
?>
