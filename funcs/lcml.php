<?
//    require_once("funcs/DataBaseHTS.php");
    require_once("debug.php");

    require_once("funcs/Cache.php");
    require_once("lcml/funcs.php");
    require_once("lcml/extentions.php");

    $GLOBALS['cms']['smilies_dir']="{$GLOBALS['cms']['main_host_dir']}/forum/smilies";
    $GLOBALS['cms']['smilies_url']="{$GLOBALS['cms']['main_host_uri']}/forum/smilies";
    $GLOBALS['cms']['images_dir']="{$_SERVER['DOCUMENT_ROOT']}/images";
//    $GLOBALS['cms_images_url']='http://img.airbase.ru';
    $GLOBALS['cms']['sites_store_path'] = "{$GLOBALS['cms']['main_host_dir']}/sites";
    $GLOBALS['cms']['sites_store_url'] = "{$GLOBALS['cms']['main_host_uri']}/sites";

    ext_load($GLOBALS['cms']['base_dir'].'/funcs/lcml/tags');

    function lcml_out($txt)
    {
        $txt=preg_replace("!(\s)(http://|ftp://)(\S+)(\s)!i","$1<a href=\"$2$3\">$2$3</a>$4",$txt);
        return $txt;
    }

	function rest_return($ret_val, $saved_params)
	{
		$GLOBALS['lcml']['params'] = $saved_params;
		$GLOBALS['lcml']['level']--;
		return $ret_val;
	}

    function lcml($txt, $params=array())
    {
		$GLOBALS['lcml']['level'] = intval(@$GLOBALS['lcml']['level']) + 1;

		$saved_params = empty($GLOBALS['lcml']['params']) ? array() : $GLOBALS['lcml']['params'];
		foreach($saved_params as $key => $val)
			if(!isset($params[$key]))
				$params[$key] = $val;
	
		$GLOBALS['lcml']['params'] = $params;

        if(!trim($txt))
            return rest_return($txt, $saved_params);

		$ch_type = 'lcml-compiled';
		$ch_key = md5($txt);

		$ch = new Cache();
		if(empty($params['cache_disable']) && $GLOBALS['lcml']['level'] < 2 && $ch->get($ch_type,$ch_key))
			return rest_return($ch->last, $saved_params);

        $page = @$GLOBALS['cms']['page_path'];

		$hts = new DataBaseHTS();

		$data = $hts->parse_uri($page);

		if(empty($params['page_path']))
			$params['page_path'] = $data['path'];

		if(empty($params['uri']))
			$params['uri'] = $page;

        $outfile=0;

        if($outfile)
        {
            $fh=fopen($GLOBALS['cms']['base_dir']."/funcs/lcml.log","at");
            fwrite($fh,$txt."\n---------------------------------------------\n");
            fclose($fh);
        }

        if(is_array($params))
        {
            foreach($params as $key => $value)
			{
//				if(user_data('level')>100)
//					$txt .= "$key = {$value}<br/>";
                $GLOBALS['lcml'][$key] = $value;
			}
        }
        else
        {
            debug(__FILE__.__LINE__." Unknown parameter '$params'");
        }

        if(empty($GLOBALS['lcml']['cr_type']))
        {
            if(empty($GLOBALS['ibforums']))
                $GLOBALS['lcml']['cr_type'] = 'empty_as_para';
            else
                $GLOBALS['lcml']['cr_type'] = 'ignore_cr';
        }

        if($GLOBALS['lcml']['cr_type'] == 'plain_text')
            return rest_return($ch->set($ch_type,$ch_key,"<xmp>$txt</xmp>"), $saved_params);
		
        if(empty($page))
            $page = '';

		$page = empty($GLOBALS['lcml']['page']) ? $page : $GLOBALS['lcml']['page'];

//        if($page) include("config.php");

        $txt = str_replace("\r","",$txt);

//        require_once("tags/code.php");
//        $txt=preg_replace("!\[code([^\]]*)\](.+?)\[/code\]!ise","lp_code_(\"$2\",'$1')",$txt);


        $txt = ext_load($GLOBALS['cms']['base_dir'].'/funcs/lcml/pre', $txt);

        include_once("lcml/sharp.php");

		$mask = str_repeat('.', strlen($txt));
        $txt = lcml_sharp($txt, $mask);

        include_once("lcml/tags.php");
        $txt = lcml_tags($txt, $mask);

        $txt = ext_load($GLOBALS['cms']['base_dir'].'/funcs/lcml/post',$txt);

        if($outfile)
        {
            $fh=fopen($GLOBALS['cms']['base_dir']."/funcs/lcml.log","at");
            fwrite($fh,$txt."\n=============================================\n\n");
            fclose($fh);
        }

        if(preg_match("!^(#.+)$!m", $txt, $m) && !empty($GLOBALS['lcml']['page']))
            debug("{$GLOBALS['lcml']['page']}: {$m[1]}", "LCML:"); 
        if(preg_match("!(\[.+?\])!m", $txt, $m) && !empty($GLOBALS['lcml']['page']))
            debug("{$GLOBALS['lcml']['page']}: {$m[1]}", "LCML:"); 

//        if(user_data('member_id') == 1)
//            xdebug_dump_function_profile(XDEBUG_PROFILER_CPU); 

//		echo "<xmp>Out: '$txt'</xmp>";

        return rest_return($ch->set($ch_type,$ch_key,$txt,1209600), $saved_params);
    }

//    function lp_code($txt,$params) { include_once("tags/code.php"); return lp_code_($txt,$params);}

?>
