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

    foreach(split(" ","b big i s sub sup small u") as $tag) eval("function lp_$tag(\$txt){return '<$tag>'.lcml(\"\$txt\").'</$tag>';}");
    foreach(split(" ","br hr") as $tag) eval("function lt_$tag(){return '<$tag />';}");

    ext_load($GLOBALS['cms']['base_dir'].'/funcs/lcml/tags');

    function lcml_out($txt)
    {
        $txt=preg_replace("!(\s)(http://|ftp://)(\S+)(\s)!i","$1<a href=\"$2$3\">$2$3</a>$4",$txt);
        return $txt;
    }


    function lcml($txt, $params=array())
    {
		if(!isset($GLOBALS['lcml']['level']))
			$GLOBALS['lcml']['level'] = 0;

		$GLOBALS['lcml']['level']++;
	
//		echo "<xmp>'".print_r($txt,true)."'</xmp>";

//		$txt .= "g={$GLOBALS['lcml']['level']}";
	
        if(!trim($txt))
            return $txt;

		$ch_type = 'lcml-compiled';
		$ch_key = md5($txt);

		$ch = new Cache();
		if(/*$GLOBALS['lcml']['level']==1 &&*/ $ch->get($ch_type,$ch_key))
			return $ch->last;

/*		foreach(split(' ','b br code hr i li p pre s u ul xmp') as $tag)
		{
			$txt = preg_replace("!<$tag>!","[$tag]", $txt);
			$txt = preg_replace("!<$tag\s+/>!","[$tag]", $txt);
			$txt = preg_replace("!</$tag>!","[/$tag]", $txt);
		}

//		if(empty($params['with_html']))
//			$txt = htmlspecialchars($txt); */

        $page = $GLOBALS['cms']['page_path'];

		$hts = new DataBaseHTS();

		$data = $hts->parse_uri($page);
//		exit(print_r($data, true));

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

//		$GLOBALS['lcml'] = array();

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
		{
			$GLOBALS['lcml']['level']--;
            return $ch->set($ch_type,$ch_key,"<xmp>$txt</xmp>");
		}
		
        if(empty($page))
            $page = '';

		$page = empty($GLOBALS['lcml']['page']) ? $page : $GLOBALS['lcml']['page'];

//        if($page) include("config.php");

        $txt=str_replace("\r","",$txt);


//        require_once("tags/code.php");
//        $txt=preg_replace("!\[code([^\]]*)\](.+?)\[/code\]!ise","lp_code_(\"$2\",'$1')",$txt);

        $txt = ext_load($GLOBALS['cms']['base_dir'].'/funcs/lcml/pre',$txt);

        include_once("lcml/sharp.php");
        $txt = lcml_sharp($txt);

        $end = -1;
        do
        {
//            echo "text: $txt\n";
            list($pos,$end,$tag,$func,$params) = find_next_open_tag($txt,$end);
            if($pos === false)
                break;

            // Если нашли тэг и он не закрывающийся
            if($pos !== false && $end && substr($txt,$pos+1,1) != '/')
            {
//            	echo "Test *_{$func}";

                if(function_exists("lt_$func"))
                {

                    $func = "lt_$func";

                    if($outfile)
                    {
                        $fh=fopen($GLOBALS['cms']['base_dir']."/funcs/lcml.log","at");
                        fwrite($fh,"$func(".print_r(params($params), true).")\n----------------\n");
                        fclose($fh);
                    }

                    $part1 = substr($txt,0,$pos);
                    $part2 = $func(params($params));
                    $part3 = substr($txt,$end+1);
                    $txt  = $part1.$part2.$part3;
//                    echo "<xmp>tag=$func,p1='$part1'\np2='$part2'\np3='$part3'</xmp>";
                    $end  = strlen($part1.$part2)-1; // В другой раз проверяем с конца изменённого фрагмента
                    continue;
                }

                if(function_exists("lp_$func"))
                {
                    $opened   = 0; // число открытых тэгов данного типа
                    $cfunc    = "lp_$func";
                    $next_end = $end;
                    do
                    {
                        // Ищем следующий открывающийся тэг
                        list($next_pos,$next_end,$next_tag,$next_func)=find_next_open_tag($txt,$next_end);
                        // Если он такой же, как наш, то увеличиваем счётчик вложений
                        if(strtolower($next_func)==strtolower($func))
                            $opened++;
                        // Если он закрывающийся нашего типа, то...
                        if(strtolower($next_func)==strtolower("/$func"))
                        {
                            // Если есть вложения - уменьшаем
                            if($opened)
                            {
                                $opened--;
                            }
                            // иначе - вычисляем тэг, заменяя его на новое содержимое
                            else
                            {

                                if($outfile)
                                {
                                    $fh=fopen($GLOBALS['cms']['base_dir']."/funcs/lcml.log","at");
                                    fwrite($fh,"$cfunc('".substr($txt,$end+1,$next_pos-$end-1).",".print_r(params($params), true)."\n----------------\n");
                                    fclose($fh);
                                }
                                
                                $part1 = substr($txt,0,$pos);
								$part2 = substr($txt,$end+1,$next_pos-$end-1);
                                $part2 = $cfunc($part2,params($params));
                                $part3 = substr($txt,$next_end+1);
                                $txt=$part1.$part2.$part3;
// 				                echo "<xmp>tag=$func,p1='$part1'\np2='$part2'\np3='$part3'\n,end=$end,nextpos=$next_pos</xmp>";
                                $next_pos = false;
                                $pos = strlen($part1.$part2); //с конца изменённого фрагмента
                            }
                        }
                    } while($next_pos !== false);
                    $end  = $pos; // В другой раз проверяем с этого же места
                    continue;
                }

//                $txt=substr($txt,0,$pos)."&#91;".substr($txt,$pos+1,$end-$pos-1).($end<strlen($txt)?"&#93;":"").substr($txt,$end+1);
                // Неопределённый тэг - пропускаем
                if($pos !== false)
                    $end = $pos + 1;
                else
                    $end = false;
//                echo "$pos($func):====$txt\n====";
            }

        } while($end !== false); //  && $loops++ < 10

//        $txt=str_replace("&#91;","[",$txt);
//        $txt=str_replace("&#93;","]",$txt);

        $txt=ext_load($GLOBALS['cms']['base_dir'].'/funcs/lcml/post',$txt);

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

		$GLOBALS['lcml']['level']--;
        return $ch->set($ch_type,$ch_key,$txt,1209600);
    }

    function find_next_open_tag($txt,$pos)
    {
//        echo ". '$txt':$pos\n";

        while($pos+1 < strlen($txt) && ($pos = strpos($txt, '[', $pos+1)) !== false)
        {
            $pos_open  = $pos;
            $pos_close = $pos;
            $in=0;

            $pos_open=strpos($txt,'[',$pos_open+1); // Следующий открывающийся тэг
            $end=0;
            $pos_close=strpos($txt,']',$pos_close+1); // Ближайший закрывающий знак

            while($pos_close!==false && $pos_open!==false)
            {
//                echo "test: $pos_open / $pos_close $in ".strlen($txt)."\n";
                //  Закрывающий находится ближе открывающего
                //  никаких особых случаев
                if($pos_open > $pos_close && $in==0)
                {
                    $end = $pos_close;
                    break;
                }
                // Закрывающийся имеется ближе открывающегося, но
                // мы уже внутри другого открытого.
                // закрываем его и считаем дальше
                if($pos_open>$pos_close && $in!=0)
                {
                    $in--;
                    $pos_close=strpos($txt,']',$pos_close+1);
                }
                // Новый тэг открывается раньше, чем закрывается наш
                // Начинаем учёт вложений
                if($pos_open<$pos_close)
                {
                    $pos_open=strpos($txt,'[',$pos_open+1);
                    $pos_close=strpos($txt,']',$pos_close+1);
                }
            }
            if(!$end)
                $end = $pos_close;
            if(!$end)
                $end = strlen($txt);

            // Вырезаем целиком найденный тэг, без квадратных скобок
            $tag = substr($txt,$pos+1,$end-$pos-1);

            preg_match("!^([^\s\|]*)\s*(.*?)$!s",$tag,$m); // func, params
            return array($pos, $end, $tag, isset($m[1])?$m[1]:"",isset($m[2])?$m[2]:"");
        }
        return array(false, false, '', '', '');
    }
    
    function params($in)
    {
        $params=array();

        if(preg_match("!^(.*?)\|(.*)$!s",$in,$m))
        {
            $in=$m[1];
            $params['description']=$m[2];
        }

        $params['orig']    = $in;
        $params['width']   = '';//"100%";
        $params['height']   = '';
        $params['_width']  = '';
        $params['align']   = "left";
        $params['flow']    = ""; // noflow
        $params['_border'] = "";
        $params['border']  = 1;
        $params['size'] = '';
        $params['nohref'] = false;

        foreach(preg_split("!\s+!",$in) as $param)
        {
            if(preg_match("!^\d+x\d+$!",$param)) { $params['size']=$param; continue;}
            if(preg_match("!^\d+x$!",$param)) { $params['size']=$param; continue;}
            if(preg_match("!^x\d+$!",$param)) { $params['size']=$param; continue;}
            if(preg_match("!^\d+(%|px)$!",$param)) { $params['width']=$param; continue;}
//            if(preg_match("!^(\d+)px$!",$param, $m)) { $params['width']=$m[1]; continue;}
            if(preg_match("!^(left|right|center)$!",$param)) { $params['align']=$param; continue;}
            if(preg_match("!^(flow|noflow)$!",$param)) { $params['flow']=$param; continue;}
            if(preg_match("!^border$!",$param))   { $params['border']=1; continue;}
            if(preg_match("!^noborder$!",$param)) { $params['border']=0; continue;}
            if(preg_match("!^nohref$!",$param)) { $params['nohref']=true; continue;}
            if(preg_match("!^(\w+)=\"(.*?)\"$!s",$param,$m)) { $params[$m[1]]=$m[2]; continue;}
            if(empty($params['url']))
                $params['url'] = $param;
        }

//	echo "nohref={$params['nohref']}<br />";

        if(empty($params['uri']))
			$params['uri'] = @$params['url'];

        if(empty($params['uri']))
			$params['uri'] = @$params['cms']['main_uri'];

		require_once("funcs/security.php");
		$params['uri'] = secure_path($params['uri']);

        list($iws, $ihs) = split("x", $params['size']."x");
        if(!$params['width'] && $iws)
            $params['width'] = $iws + 6;

        if(!$params['height'] && $ihs)
            $params['height'] = $ihs + 6;

        if($params['flow'] == "noflow" && !$params['width'])
            $params['width'] = '100%';

        if(isset($params['width']) && $params['width']) $params['_width']=" width=\"{$params['width']}\"";
        if($params['border']) $params['_border']=" class=\"box\"";
//        if(isset($params['_style']) && $params['_style']) $params['_style']=" style=\"".ltrim($params['_style'])."\"";

//        if(!isset($params['_style']))
//            $params['_style']="";

		$params['xwidth'] = $params['width'] ? "width:{$params['width']};" : "";

        if(!empty($params['align']))
        {
            if($params['align']=='center')
            {
                $params['_align_b']="<div align=\"left\"><table{$params['_width']} cellPadding=\"0\" cellSpacing=\"0\"><tr><td>"; // {$params['_border']}{$params['_style']}
                $params['_align_e']="</td></tr></table></div>";
            }
            else // right or left
            {
                if(empty($params['flow']) || $params['flow'] == 'flow') // С обтеканием текста
                {
//                    $params['_align_b']="<table{$params['_width']} cellPadding=\"0\" cellSpacing=\"0\" align=\"{$params['align']}\"><tr><td>"; // {$params['_border']}{$params['_style']}
//                    $params['_align_e']="</td></tr></table>";
                    $params['_align_b']="<div{$params['_border']} style=\"{$params['xwidth']} xdisplay: xblock; float: {$params['align']}; margin-left: 10px; margin-right: 10px;\">"; // {$params['_style']}
                    $params['_align_e']="</div>";
                }
                else
                {
                    $params['_align_b']="<table cellPadding=\"0\" cellSpacing=\"0\"><tr><td{$params['_width']} align=\"{$params['align']}\">"; //{$params['_style']}
                    $params['_align_e']="</td></tr></table>";
                }
            }
        }

        return $params;
    }

//    function lp_code($txt,$params) { include_once("tags/code.php"); return lp_code_($txt,$params);}

?>
