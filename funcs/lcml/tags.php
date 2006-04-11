<?
    function lcml_tags($txt, &$mask)
    {

		$mask = str_repeat('X', strlen($txt));

        $end = -1;
		$next_end = -1;
        do
        {
//            echo "text: $txt\n";
            list($pos, $end, $tag, $func, $params) = find_next_open_tag($txt, $end);
            if($pos === false)
                break;

            // Если нашли тэг и он не закрывающийся
            if($pos !== false && $end && substr($txt, $pos+1, 1) != '/')
            {
//            	echo "Test *_{$func}";

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

                                if(!empty($outfile))
                                {
                                    $fh=fopen($GLOBALS['cms']['base_dir']."/funcs/lcml.log","at");
                                    fwrite($fh,"$cfunc('".substr($txt,$end+1,$next_pos-$end-1).",".print_r(params($params), true)."\n----------------\n");
                                    fclose($fh);
                                }
                                
                                $part1 = substr($txt, 0, $pos);
								$part2 = substr($txt, $end+1, $next_pos-$end-1);
//								echo "call '$cfunc' for '$part2'";
                                $part2 = $cfunc($part2, params($params));
                                $part3 = substr($txt, $next_end+1);
                                $txt = $part1.$part2.$part3;
								$mask = substr($mask, 0, $pos).str_repeat('X',strlen($part2)).substr($mask, $next_end+1);
// 				                echo "<xmp>tag=$func,p1='$part1'\np2='$part2'\np3='$part3'\n,end=$end,nextpos=$next_pos</xmp>";
                                $next_pos = false;
                                $pos = strlen($part1.$part2); //с конца изменённого фрагмента
                            }
                        }
                    } while($next_pos !== false);
                    $end  = $pos; // В другой раз проверяем с этого же места
                    continue;
                }

                if(function_exists("lt_$func"))
                {
                    $func = "lt_$func";

                    if(!empty($outfile))
                    {
                        $fh = fopen($GLOBALS['cms']['base_dir']."/funcs/lcml.log","at");
                        fwrite($fh,"$func(".print_r(params($params), true).")\n----------------\n");
                        fclose($fh);
                    }

                    $part1 = substr($txt,0,$pos);
                    $part2 = $func(params($params));
                    $part3 = substr($txt,$end+1);
                    $txt  = $part1.$part2.$part3;
					$mask = substr($mask, 0, $pos).str_repeat('X',strlen($part2)).substr($mask, $next_end+1);
//                    echo "<xmp>tag=$func,p1='$part1'\np2='$part2'\np3='$part3'</xmp>";
                    $end  = strlen($part1.$part2)-1; // В другой раз проверяем с конца изменённого фрагмента
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

        return $txt;
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
                $params['_align_b']="<div {$params['_border']} style=\"text-align: left;\"><table{$params['_width']} cellPadding=\"0\" cellSpacing=\"0\"><tr><td style=\"text-align: justify;\">"; // {$params['_border']}{$params['_style']}
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
