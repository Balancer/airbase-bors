<?  
	function template_assign_data($assign_template, $data=array(), $uri=NULL, $caller=NULL)
	{
//		echo "tpl=$assign_template<br />";
//		print_r($data);

		unset($GLOBALS['module_data']);

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


		$caching = !is_null($uri)
				&& @$data['caching'] !== false
				&& @$GLOBALS['cms']['templates_cache_disabled'] !== true
			;
			
		$smarty->caching = $caching;
		$smarty->compile_check = true; 
		$smarty->php_handling = SMARTY_PHP_QUOTE; //SMARTY_PHP_PASSTHRU;
		$smarty->security = false;
		$smarty->cache_modified_check = true;
		$smarty->cache_lifetime = 86400*7;
//		print_r($smarty->secure_dir); exit();

		$hts = &new DataBaseHTS();

//		echo "<xmp>"; print_r(debug_backtrace()); echo "</xmp>";
		$caller  = array_shift(debug_backtrace());
//		echo $caller2['file']."<br />";
		$caller_path = dirname($caller['file']);
		$module_relative_path = preg_replace("!^.+?/cms/!", "", $caller_path)."/";
//		print_r($GLOBALS['cms']);

//		$caller_local_tpln = "xfile:{$GLOBALS['cms']['local_dir']}".preg_replace("!^.+?/cms/!", "/templates/".$hts->get_data($GLOBALS['main_uri'], 'template', '', true)."/", $caller_path)."/";
		$caller_local_main = "xfile:{$GLOBALS['cms']['local_dir']}".preg_replace("!^.+?/cms/!", "/templates/", $caller_path)."/";
		$caller_local_tpl = "xfile:{$GLOBALS['cms']['local_dir']}".preg_replace("!^.+?/cms/!", "/templates/".@$GLOBALS['page_data']['template']."/", $caller_path)."/";
		$caller_cms_main   = "xfile:{$GLOBALS['cms']['base_dir']}".preg_replace("!^.+?/cms/!", "/", $caller_path)."/";
		$caller_default_template = dirname($GLOBALS['cms']['default_template'])."/".$module_relative_path;
		
//		if($uri == NULL)
//			$uri = "$caller_path/$assign_template";
	
/*		if(preg_match("!^[\w\-]+\.[\w\-]+$!", $assign_template))
		{
			$assign_template_local = "xfile:{$GLOBALS['cms']['local_dir']}/templates/modules/".$assign_template;
			$assign_template_base = "xfile:$caller_path/$assign_template";
		}
*/		
		$smarty->template_dir = $caller_path;
		if(!empty($data['template_dir']) && $data['template_dir'] != 'caller')
			$smarty->template_dir = $data['template_dir'];
		
		$smarty->secure_dir += array($caller_path, $caller_default_template);

//		$template_uri = @$caller_local_tpln.$assign_template;
//		if(!$smarty->template_exists($template_uri))
			$template_uri = $caller_local_tpl.$assign_template;
		if(!$smarty->template_exists($template_uri))
			$template_uri = $caller_local_main.$assign_template;
		if(!$smarty->template_exists($template_uri))
			$template_uri = "xfile:{$GLOBALS['cms']['base_dir']}/templates/$module_relative_path/$assign_template";
		if(!$smarty->template_exists($template_uri))
			$template_uri = $caller_cms_main.$assign_template;
		if(!$smarty->template_exists($template_uri))
			$template_uri = "xfile:".$caller_default_template.$assign_template;
		if(!$smarty->template_exists($template_uri))
			$template_uri = "xfile:$caller_path/".$assign_template;
		if(!$smarty->template_exists($template_uri))
			$template_uri = "xfile:".$assign_template;

//		echo "==$template_uri==";
			
		if(!$smarty->template_exists($template_uri))
			$template_uri = $assign_template;
		if(!$smarty->template_exists($template_uri))
			$template_uri = $GLOBALS['cms']['default_template'];


		$modify_time = empty($data['modify_time']) ? time() : $data['modify_time'];
		$modify_time = max(@$data['compile_time'], $modify_time);

		include_once("funcs/users.php");
		if(!isset($data['access']))
			$data['access'] = access_allowed($uri) ? 1 : 0;

		$me = &new User();

		if(!isset($data['level']))
			$data['level'] = $me->get('level');
		
		if(is_array(@$GLOBALS['cms']['smarty']))
			foreach($GLOBALS['cms']['smarty'] as $key => $val)
				$smarty->assign($key, $val);
		
		if(!$caching || !$smarty->is_cached($template_uri, $uri))
		{
			foreach($data as $key => $val)
			{
//				echo "$key -> ".print_r($val,true)."<br />\n";
				$$key = $val;
				$smarty->assign($key, $val);
			}
	
			if(!empty($GLOBALS['cms']['templates']['data']))
	            foreach($GLOBALS['cms']['templates']['data'] as $key => $value)
        	        $smarty->assign($key, $value);

			$smarty->assign("page_template", $assign_template);
			$smarty->assign("template_uri", $template_uri);
			$dirname = dirname($template_uri);
			if(!preg_match("!^\w+:!", $dirname))
				$dirname = "xfile:$dirname";
			$smarty->assign("template_dirname", $dirname);
			$smarty->assign("time", time());

			@header("X-Recompile: Yes");
		}

		$smarty->assign("uri", $uri);
		$smarty->assign("now", time());
	
		$me = &new User();
		$smarty->assign("me", $me);
		$smarty->assign("me_id", $me->get('id'));

		$smarty->assign("cms", $GLOBALS['cms']);

		if(empty($data['main_uri']))
			$smarty->assign("main_uri", @$GLOBALS['main_uri']);

		if(preg_match('!^http://!',$template_uri))
			$template_uri = "hts:".$template_uri;

		foreach(split(' ', 'host_name main_host_uri') as $key)
			$smarty->assign($key, @$GLOBALS['cms'][$key]);

		global $bors;
		if(!empty($bors) && ($obj = $bors->main_object()))
		{
			foreach(split(' ', $obj->template_local_vars()) as $var)
			{
//				echo "Assign $var to {$obj->$var()}<br />";
				$smarty->assign($var, $obj->$var());
			}

			$smarty->assign("this", $obj);
		}
		
//		echo $template_uri;

		if($template_uri{0}=='/')
			if(file_exists($template_uri))
				$template_uri = "xfile:".$template_uri;
			else
				$template_uri = "hts:http://{$_SERVER['HTTP_HOST']}$template_uri";

		if(!$caching)
			$smarty->clear_cache($template_uri, $uri);

		$out = $smarty->fetch($template_uri, $uri);

		$out = preg_replace("!<\?php(.+?)\?>!es", "do_php(stripslashes('$1'))", $out);

		return $out;
	}
