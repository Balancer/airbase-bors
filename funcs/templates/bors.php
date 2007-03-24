<?  
	function template_assign_bors_object($obj)
	{
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

		$caching = !$obj->is_cache_disabled()
				&& @$GLOBALS['cms']['templates_cache_disabled'] !== true;
			
		$smarty->caching = $caching;
		$smarty->compile_check = true; 
		$smarty->php_handling = SMARTY_PHP_QUOTE; //SMARTY_PHP_PASSTHRU;
		$smarty->security = false;
		$smarty->cache_modified_check = true;
		$smarty->cache_lifetime = 86400*7;

		foreach(split(' ', $obj->template_vars()) as $var)
			$smarty->assign($var, $obj->$var());
		
		$template = smarty_template($obj->template());
		
		if(!empty($GLOBALS['cms']['templates']['data']))
            foreach($GLOBALS['cms']['templates']['data'] as $key => $value)
       	        $smarty->assign($key, $value);

		if(!$caching)
			$smarty->clear_cache($template, $obj->uri());

		$out = $smarty->fetch($template, $obj->uri());

//		$out = preg_replace("!<\?php(.+?)\?>!es", "do_php(stripslashes('$1'))", $out);

		return $out;
	}
