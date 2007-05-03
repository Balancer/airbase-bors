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

//		$smarty->assign("views_average", sprintf("%.1f",86400*$views/($views_last-$views_first+1)));
		$smarty->assign("main_uri", @$GLOBALS['main_uri']);
		$smarty->assign("now", time());
		$smarty->assign("ref", @$_SERVER['HTTP_REFERER']);
		$smarty->assign("queries_time", sprintf("%.3f", $GLOBALS['stat']['queries_time']));
		$smarty->assign("queries", $GLOBALS['global_db_queries']);
		$smarty->assign("this", $obj);

		//TODO: убрать user_id и user_nameв старых шаблонах.
		$me = &new User();
		$smarty->assign("me", $me);
		$smarty->assign("me_id", $me->get('id'));
		$smarty->assign("user_id", $me->get('id'));
		$smarty->assign("user_name", $me->get('name'));

		foreach(split(' ', $obj->template_vars()) as $var)
			$smarty->assign($var, $obj->$var());

		foreach(split(' ', $obj->template_local_vars()) as $var)
			$smarty->assign($var, $obj->$var());
		
		$template = smarty_template($obj->template());
		$smarty->template_dir = dirname(preg_replace("!^xfile:!", "", $template));
		$smarty->assign("page_template", $template);
		
		if(!empty($GLOBALS['cms']['templates']['data']))
            foreach($GLOBALS['cms']['templates']['data'] as $key => $value)
			{
//				echo "assign data $key to $value<br />";
       	        $smarty->assign($key, $value);
			}

		if(!$caching)
			$smarty->clear_cache($template, $obj->uri());

		if(!empty($GLOBALS['stat']['start_microtime']))
		{
		    list($usec, $sec) = explode(" ",microtime());
   	        $smarty->assign("make_time", sprintf("%.3f", ((float)$usec + (float)$sec) - $GLOBALS['stat']['start_microtime']));
		}

		$out = $smarty->fetch($template, $obj->uri());

/*		$out = preg_replace("!<\?php(.+?)\?>!es", "do_php(stripslashes('$1'))", $out); */

		return $out;
	}

	function smarty_template($template_name)
	{
		if(substr($template_name, 0, 8) == 'xfile://')
			return $template_name;
		
		if($template_name{0} == '/')
			return "xfile:".$template_name;
		
		return $GLOBALS['cms']['default_template'];
	}
