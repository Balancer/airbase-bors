<?
//    error_reporting  (E_ALL);

    function lcml_sharp($txt)
    {
        $array = split("\n",$txt);
        $in_pair=0;
        $changed=0;
        $start=-1;
        $tag="";
        for($i=0; $i<sizeof($array); $i++)
        {
            $s=$array[$i];

            if(preg_match("!^#(\w+)(\s*)(.*?)$!" ,$s,$m)) // Открывающийся или одиночный тэг
            {
                if(function_exists("lcml_sharp_tag_$m[1]"))
                {
                    $func="lcml_sharp_tag_$m[1]";
                    $array[$i]=$func(trim($m[3]));
                    $changed=1;
                    continue;
                }

                if(function_exists("lcml_sharp_pair_$m[1]"))
                {
                    if(!$in_pair) // новый
                    {
                        $in_pair++;
                        $start=$i;
                        $tag=$m[1];
                        $params=trim($m[3]);
                        continue;
                    }

                    if($in_pair && $tag==$m[1]) // такой же
                    {
                        $in_pair++;
                        continue;
                    }
                }
            }

            if(preg_match("!^#/(\w+)(\s|$)!",$s,$m) && $tag==$m[1]) // Новый открывающийся тэг
            {
                $in_pair--;
                if(!$in_pair)
                {
                    $func="lcml_sharp_pair_$tag";
                    $txt=$func(join("\n",array_slice($array,$start+1,$i-$start-1)),$params);
                    $txt=split("\n",$txt);
                    $right=array_slice($array,$i+1);
                    $left=array_slice($array,0,$start);
                    $array=array_merge($left,$txt,$right);
                    $changed=1;
                }
            }

        }
        
        $txt=join("\n",$array);

        if($changed)
            $txt=lcml_sharp($txt);
    
/*        if(!isset($GLOBALS['forum_tag_found'] && !$GLOBALS['forum_tag_found']))
            $txt.="\n<?\$id=\"$::page_data{forum_id}\";\$page=\"$::page\";include(\"/home/airbase/html/inc/show/forum-comments.phtml\");?>\n";
*/        
        return $txt;
    }

    function lcml_sharp_getset($txt)
    {
        $params=array();
        $key="";
        foreach(split("\n",$txt) as $s)
        {
            if(preg_match("!^(\w+)=(.+)$!",$s,$m))
            {
                $key=$m[1];
                $params[$key]=$m[2];
            }
            else
            {
                if($key)
                    $params[$key].="\n".$s;
            }
        }
        return $params;
    }

    function lcml_sharp_pair_article($txt)
    {
        $a = lcml_sharp_getset($txt);
        $href  = empty($a['href']  )? "":$a['href'];
        $title = empty($a['title'] )? "":$a['title'];
        $author= empty($a['author'])? "":$a['author'];
        $text  = empty($a['text']  )? "":$a['text'];
        $time  = empty($a['time']  )? "":$a['time'];
        $img   = empty($a['img']   )? "":$a['img'];

        return "#box\n[$href|[$img 128x left nohref]][small]{$author}[/small]<br>[$href|[b]{$title}[/b]]<br>[small][i]{$text}[/i][/small]\n#/box";
    }

    function lcml_sharp_tag_c($txt) { return "<h2>$txt</h2>\n";}
    function lcml_sharp_tag_n($txt) { return "\n<p>$txt ";}
    function lcml_sharp_tag_i($txt) { return "<li>$txt\n";}
    function lcml_sharp_tag_ib($txt) { return "<li><b>$txt</b>\n";}
    function lcml_sharp_tag_p($txt) { return "<p>$txt "; }

    function lcml_sharp_tag_addurl($txt)
    {
        list($url,$tag,$author,$date,$name,$desc)=split("\|",$txt."||||||");
        return "<table><tr><td><b><a href=$url>$name</a></b>, <small>$author, $date<br>$desc</td></tr></table>\n";
        #\s+(.*?)\|(.*?)\|(.*?)\|(.*?)\|(.*?)\|(.*)~"<table id=addurl><caption>".($1?"<a href=$1>":"")."$4 $5".($1?"</a>":"")."</caption><tr><td>$6<div align=right>".($1?"/<a href=$1>зайти...</a>/":"")."<br>разместил: $3</div></td></tr></table>\n"~ge;
    }
    
    function lcml_sharp_tag_forum($txt)
    {
        if(!trim($txt))
            return "";
        $GLOBALS['pagedata']['forum']=$txt;
        $GLOBALS['forum_tag_found']=1;
        return "<?\$id=\"$txt\";\$"."page=\"".$GLOBALS['page']."\";include(\"/home/airbase/html/inc/show/forum-comments.phtml\");?>\n";
    }

    function lcml_sharp_tag_news($txt)
    {
        list($url, $title, $text) = split("\|",$txt."||");
        if($url)
            $title="<a href=$url>$title</a>";
        return "<table width=550 class=btab cellSpacing=0><caption>$title</caption><tr><td>$text</td></tr></table>\n";
    }

    function lcml_sharp_pair_l($txt) 
    { 
        $txt=preg_replace("!^\-\s+!m","\n<li>",$txt);
        return "<ul>\n\n$txt\n</ul>";
    }

    function lcml_sharp_pair_logos($txt,$params)
    {
        $w=''; $h='';
        if(preg_match("!^\s*(\d*)\s*,\s*(\d*)\s*$!s",$params,$m))
        {
            $w=$m[1];
            $h=$m[2];
        }

        $res='';
        if(!isset($GLOBALS['logos_included']))
        {
            $GLOBALS['logos_included']=1;
            $res.="<script charset=\"UTF-8\" src=\"/js/logos.js\"></script>\n";
        }
        $res.="<script charset=\"UTF-8\">begLogos($w".($h?",":"")."$h)</script><noscript><ul></noscript>\n";

        foreach(split("\n",$txt) as $s)
        {
            preg_match("!^(#logitm\s+)?(.*?)\|(.*?),(.*?),(.*)$!",$s,$m);
            list($name,$url,$img,$desc)=array($m[2],$m[3],$m[4],$m[5]);
            $desc=str_replace("\"","\\\"",$desc);
            $res.="<script charset=\"UTF-8\">logoItem(\"$name\",\"$url\",\"$img\",\"$desc\")</script><noscript><li><a href=$url>$name</a> - $desc</noscript>\n";
        }

        return "$res<script charset=\"UTF-8\">endLogos()</script><noscript></ul></noscript>\n";
    }

    include_once("tags/sharp_boxes.php");
?>