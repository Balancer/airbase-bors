<?  
//    require_once('debug.php');
    require_once('Smarty/Smarty.class.php');
    require_once('funcs/DataBaseHTS.php');
    require_once('actions/recompile.php');
    require_once('funcs/lcml.php');
    require_once('funcs/navigation/go.php');
    require_once('funcs/users.php');

    function do_php($code)
    {
        ob_start();
//		if(user_data('level') > 10)
//			echo "do code<xmp>$code</xmp>";
        eval($code);
        $out = ob_get_contents();
        ob_clean();
		if(preg_match("!{$_SERVER['DOCUMENT_ROOT']}/cms/funcs/templates/smarty.php!", $out))
        	return "$out Error in code<xmp>$code</xmp>";

        return $out;
    }

    function show_page($uri)
    {
        $hts  = new DataBaseHTS();

        $page = $hts->normalize_uri($uri);

        if($page != $uri && empty($GLOBALS['title']))
            go($page);

//        if(!empty($_COOKIE['member_id']) && $_COOKIE['member_id'] == 1)
//            echo __FILE__.__LINE__." ".$GLOBALS['title']."<br />\n";


//        echo "************".$hts->get_data($page, 'modify_time');
	
		if(empty($GLOBALS['page_data']['source']))
		{
			$source = $hts->get_data($page, 'source');
			$body 	= $hts->get_data($page, 'body');
			$action = false;
		}
		else
		{
			$source = $GLOBALS['page_data']['source'];
			$action = @$GLOBALS['cms']['action'];
			$body = lcml($source, array('with_html'=>true));
//			exit("<xmp>".$body."</xmp>");
		}
		

        if(!$source)
        {
			// Такой страницы ещё нет - создаём

            if(!empty($GLOBALS['title']))
            {
                $ref = empty($GLOBALS['HTTP_REFERER']) ? '' : $GLOBALS['HTTP_REFERER'];
                go($page,"/edit-new/?title=".urlencode($GLOBALS['title'])."&page=$page&ref=$ref");
            }
            else
            {
                require_once("404.php");
                exit();
            }
        }
        
        $smarty = new Smarty;
        require("mysql-smarty.php");

        $smarty->compile_dir = $GLOBALS['cms']['cache_dir'].'/smarty-templates_c/';
        $smarty->plugins_dir = $GLOBALS['cms']['base_dir'].'/funcs/templates/plugins/';
        $smarty->cache_dir   = $GLOBALS['cms']['cache_dir'].'/smarty-cache/';

		if(!file_exists($smarty->compile_dir))
			@mkdir($smarty->compile_dir, 0775, true);
		if(!file_exists($smarty->cache_dir))
			@mkdir($smarty->cache_dir, 0775, true);

        $smarty->caching = $action ? false : !$GLOBALS['cms']['cache_disabled'];
        $smarty->compile_check = true; 
        $smarty->php_handling = SMARTY_PHP_QUOTE; //SMARTY_PHP_PASSTHRU;
        $smarty->security = false;
        $smarty->cache_modified_check = true;
        $smarty->cache_lifetime = 86400*7;
        $smarty->secure_dir += array("{$GLOBALS['cms']['base_dir']}/templates");

        $template = empty($GLOBALS['page_data']['template']) ? $hts->get_data($page, 'template', '', true) : $GLOBALS['page_data']['template'];
        
		if($template)
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
			"{$page}template$tpl2/",
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

        if(!$hts->get_data($tpl, 'source')
				// || ($action && $action!='virtual')
				|| @$_GET['tpl']=='safe'
			)
            $tpl = $GLOBALS['cms']['default_template_file'];

		$tpl = preg_match("!^/!", $tpl) ? $tpl : "hts:$tpl";
		
//		echo "<br/>base={$GLOBALS['cms']['base_uri']}; tpl='$tpl' Using template $template";	exit();

        if(!$hts->get_data($page, 'views_first')) $hts->set_data($page, 'views_first', time());
        $hts->set_data($page, 'views', $hts->get_data($page, 'views') + 1);

		$GLOBALS['cms']['cache_copy'] = $hts->get_data($page, 'cache_create_time');

		$nocache = $action || $GLOBALS['cms']['cache_disabled'];
		$nocache |= $hts->get_data($page, 'modify_time') > $hts->get_data($page, 'cache_create_time');

		if($nocache)
			$smarty->clear_cache($tpl, $page);

		$access = access_allowed($page, $hts) ? 1 : 0;
		$us = new User();
		$level = $us->data('level');
		$user_id = $us->data('id');
		
		include_once("funcs/actions/subscribe.php");

		$subscribed = cms_funcs_action_is_subscribed($page);

		$modify_time = $hts->get_data($page, 'modify_time');
        $last_modify = gmdate('D, d M Y H:i:s', $modify_time).' GMT';
   	    header ('Last-Modified: '.$last_modify);

        if($nocache || !$smarty->is_cached($tpl, $page))
        {
			$GLOBALS['cms']['cached_copy'] = 0;	
		
            $page_vars = 'author copyright compile_time create_time description modify_time publisher right_column subscribe title version views views_first views_last';

            foreach(split(' ', $page_vars) as $key)
			{
				global $$key;
               	$$key = empty($GLOBALS['page_data'][$key]) ? $hts->get_data($page, $key) : $GLOBALS['page_data'][$key];
			}

            if(!empty($_GET['version']))
            {
                $version = $_GET['version'];
                $smarty->caching = false;
//            $GLOBALS['log_level'] = 9;
                $res = $hts->dbh->get("SELECT * FROM hts_data_backup WHERE `id` = '".addslashes($page)."' AND `version` = ".intval($version));
//            $GLOBALS['log_level'] = 2;
//            echo "<xmp>";
//            print_r($res);
//            echo "</xmp>";
                if(!empty($res['title'])) $title = $res['title'];
                if(!empty($res['description_source'])) $description = lcml($res['description_source']);
                if(!empty($res['source'])) $body = lcml($res['source']);

                echo "<h2>Версия $version, сохранённая ".strftime("%d.%m.%Y %H:%M:%S", $res['backup_time'])."</h2>\n";
            }

//            echo "action = $action; ct = $compile_time; now=". time();

            if(
                empty($action) 
				&& empty($GLOBALS['version']) 
				&& empty($GLOBALS['page_data']['source'])
				&& (
                	$compile_time < $hts->dbh->get_value('hts_ext_system_data', 'key', 'global_recompile', 'value')
	                ||
    	            $compile_time < time()-86400*7
                	))
            {
                recompile($page);
                foreach(split(' ', $page_vars) as $key)
                    $$key = $hts->get_data($page, $key);
            }

            foreach(split(' ', "access level action body user_id $page_vars") as $key)
            {
//                echo "assign <xmp>'$key' -> '{$$key}'</xmp>";
                $smarty->assign("$key", "{$$key}");
            }
    
			$uri = @$GLOBALS['uri'];
			if(!$uri)
				$uri = $page;

            $smarty->assign("views_average", sprintf("%.1f",86400*$views/($views_last-$views_first+1)));
            $smarty->assign("page_template", $template);
            $smarty->assign("page", $page);
            $smarty->assign("uri", $page);
            $smarty->assign("main_uri", @$GLOBALS['main_uri']);
            $smarty->assign("time", time());
            $smarty->assign("ref", @$_SERVER['HTTP_REFERER']);

			$hts->set_data($page, 'cache_create_time', time());

	        $smarty->clear_cache($tpl, $page);
			
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
	                exit("Not modified since $last_modify");
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

		if(is_array(@$GLOBALS['cms']['smarty']))
			foreach($GLOBALS['cms']['smarty'] as $key => $val)
				$smarty->assign($key, $val);

	    error_reporting(E_ALL & ~E_NOTICE);
        $out = $smarty->fetch($tpl, $page);
	    error_reporting(E_ALL);

		$out = preg_replace("!<\?php(.+?)\?>!es", "do_php(stripslashes('$1'))", $out);

        echo $out;

        $hts->set_data($page, 'views_last', time());
    }
?>
