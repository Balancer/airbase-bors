<?

//require_once("/home/airbase/html/inc/funcs/funcs.phtml");

function _obsolete_hts_extract($key,$new_name=NULL)
{
    if(!$new_name)
        $new_name=$key;
    global $$key,$$new_name,$hts;

    preg_match("!(^|\n)#$key\s+(.*?)(\n|$)!",$hts,$data);

//    echo "Extracted for ($key,$new_name) = '$data[2]'<br>";

    $$new_name=isset($data[2])?$data[2]:NULL;
    $hts=preg_replace("!(^|\n)#$key\s+.*?(\n|$)!","$1$2",$hts);
}

function _obsolete_hts_load($hts_file)
{
    global $hts, $head, $title, $h1, $h2, $h3, $copyr, $body, $source, $type, $forum_id;
    global $create_time, $style, $template, $color, $logdir, $navs, $forum, $flags, $cr_type, $split_type;
    global $nav1, $nav2, $nav3, $nav4, $nav5, $nav6, $nav7, $nav8;
    global $doc_root;

    $hts_file=preg_replace("!^http://[^/]+/!","/",$hts_file);
    $hts_file=preg_replace("!^$doc_root/!","/",$hts_file);
    $hts_file=$doc_root."$hts_file";
    $hts_file=preg_replace("!\.(php|phtml)$!",".hts",$hts_file);

//    echo "Try load $hts_file<br>";

    $old=0;

    if(file_exists($hts_file))
    {
        $fh=fopen($hts_file,"rt");
        $hts=fread($fh,filesize($hts_file));
        fclose($fh);
        $hts=str_replace("\r","",$hts);
//        $hts=iconv('WINDOWS-1251','UTF-8//TRANSLIT',$hts);
    }
    else
        $hts='';

    $body=@join("",file(str_replace(".hts",".phtml",$hts_file)));
    $body = preg_replace("!^.*<\!\-\-begin_page_body\-\->(.+)<\!\-\-/begin_page_body\-\->.*$!s","$1",$body);

    _obsolete_hts_extract('head');
    list($title,$h1,$h2,$h3)=split("\|",$head."|||");

    _obsolete_hts_extract('copyr','copyright');
    _obsolete_hts_extract('type');
    _obsolete_hts_extract('maked','create_time');
    _obsolete_hts_extract('style');
    _obsolete_hts_extract('template');
    _obsolete_hts_extract('color');
    _obsolete_hts_extract('logdir');
    _obsolete_hts_extract('cr_type');
    _obsolete_hts_extract("split_type");

    _obsolete_hts_extract('flags');

    $hts=preg_replace("!#begin\s*\n!","",$hts);
    $hts=preg_replace("!\n#end\s*!","",$hts);

    _obsolete_hts_extract('long');
    _obsolete_hts_extract('short');
    _obsolete_hts_extract('start');
    _obsolete_hts_extract('file');
    _obsolete_hts_extract('forum_id');

    $hts=split("\n",$hts);
    $navs=0;
    $nav_open=0;

    for($i=0;$i<sizeof($hts);$i++)
    {
        if(preg_match("!^#nav!",$hts[$i]))
        {
            $navs++;
            $hts[$i]="";
            $nav_open=1;
            $nav_sum="";
        }
        if($nav_open && preg_match("!^#!",$hts[$i]))
        {
            $nav="nav$navs";
            $$nav=$nav_sum;
            $nav_open=0;
            $hts[$i]="";
        }
        if($nav_open)
        {
            $nav_sum.="$hts[$i]\n";
            $hts[$i]="";
        }
    }

    if(!$type)
    {
        $type='hts';

        $nav_sum="";

        for($i=0;$i<sizeof($hts);$i++)
        {
            if(preg_match("!^#lev\s+(.+)!",$hts[$i],$data))
            {
                $nav_sum.="$data[1]\n";
                $hts[$i]="";
            }
        }

        if($nav_sum)
        {
            $navs++;
            $nav="nav$navs";
            $$nav=$nav_sum;
            $old=1;
        }
    }

    $hts=join("\n",$hts);

    if($old)
    {
        $hts=preg_replace("!\n\n#p\s+!","\n#p\n",$hts);
        $hts=preg_replace("!\n\n#p(\n|$)!","\n#p$1",$hts);
        $hts=preg_replace("!\n+#p(\n|$)!","\n$1",$hts);
        $hts=preg_replace("!\n#t\s+!","\n\n",$hts);
        $hts=preg_replace("!\|(.+?)\|!","$1",$hts);
        $copyr=preg_replace("!\|(.+?)\|!","$1",$copyr);
    }

    $hts=preg_replace("!^\n+!","",$hts);
    $source=preg_replace("!\n+$!","",$hts);

    if(!$title) $title="$h1, $h3, $h2";
    if(!$h1) $h1=$title;
}

function _obsolete_hts_save($hts_file)
{
    global $title, $h1, $h2, $h3, $copyr, $source, $type, $forum_id, $flags;
    global $create_time, $style, $template, $color, $logdir, $navs, $forum, $cr_type, $split_type;
    global $nav1, $nav2, $nav3, $nav4, $nav5, $nav6, $nav7, $nav8;
    global $doc_root, $host, $path, $file_name;
    

    $hts_file=preg_replace("!^http://[^/]+/!","/",$hts_file);
    $hts_file=preg_replace("!^$doc_root/!","/",$hts_file);
    $hts_file=$doc_root."$hts_file";

    $hts_file=preg_replace("!\.(php|phtml)$!",".hts",$hts_file);

//    echo "Try save to $hts_file<br>";

//    backup($hts_file,"convert-to-hts");

    $hts=''; //"#head $title|$h1|$h2|$h3\n";
    if($create_time) $hts.="#create_time $create_time\n";
    if($copyr) $hts.="#copyr $copyr\n";
    if($type) $hts.="#type $type\n";
    if($style) $hts.="#style $style\n";
    if($cr_type) $hts.="#cr_type $cr_type\n";
    if($split_type) $hts.="#split_type $split_type\n";
    if($template) $hts.="#template $template\n";
    if($color) $hts.="#color $color\n";
    if($logdir) $hts.="#logdir $logdir\n";
    if($flags) $hts.="#flags $flags\n";
    if($forum_id) $hts.="#forum_id $forum_id\n";

    require_once("funcs/DataBaseHTS.php");

    $db=new DataBaseHTS();

    $page_uri = $db->normalize_uri($hts_file);

    for($i=1;$i<=$navs;$i++)
    {
        $nav="nav$i";
        $nav=$$nav;
        $nav=preg_replace("!^\n+!","",$nav);
        $nav=preg_replace("!\n+$!","",$nav);

        $prev_nav=0;

        foreach(split("\n",$nav) as $nav)
        {
            if(!preg_match("!^(.+?),(.+)$!",$nav,$m))
                exit("Неверный формат навигации: $nav");

            $current_nav_uri=$m[1];
            $current_nav_name=$m[2];

            if(!$current_nav_uri && $current_nav_name)
                exit("Ошибка! Есть навигационное имя $current_nav_name, но нет идентификатора!");

            $current_nav_uri=$db->alias_uri($current_nav_uri);

            if(!$db->get_data($current_nav_uri,'nav_name'))
                $db->set_data($current_nav_uri,'nav_name',$current_nav_name);

            if($prev_nav != $current_nav_uri)
            {
                echolog("<b>Set nav pair</b> for '$prev_nav' and '$current_nav_uri'");
                $db->append_data($prev_nav,'child',$db->page_id_by_uri($current_nav_uri));
                $db->append_data($current_nav_uri,'parent',$db->page_id_by_uri($prev_nav));
            }

            $prev_nav=$current_nav_uri;
        }
        
        if($prev_nav && ($prev_nav != $page_uri))
        {
            echolog("<b>Set tail nav pair</b> for '$prev_nav' and '$page_uri'");
            $db->append_data($prev_nav,'child',$db->page_id_by_uri($page_uri));
            $db->append_data($page_uri,'parent',$db->page_id_by_uri($prev_nav));
        }
    }


    $hts.="\n$source\n";

    $hts=str_replace("\r","",$hts);

    if(preg_match("!^(\d\d?)\.(\d\d?)\.(\d\d|\d\d\d\d)$!",$create_time,$m))
        $mktime=mktime(0,0,0,$m[2],$m[1],$m[3]);
    else
        $mktime=$create_time;

    $hts=trim($hts);

    if($hts)
    {
//        $hts=iconv('UTF-8','WINDOWS-1251//TRANSLIT',$hts);
        die("Debug: write '$hts'");
        mkdirs(preg_replace("!^(.+)/(.+?)$!","$1",$hts_file));
        $f=fopen($hts_file,"w");
        if(!$f) die("Can't open write $hts_file!");
        fwrite($f,$hts);
        fclose($f);
        @chmod($hts_file,0666);
    }
    else
    {
        $GLOBALS['obsolete_hts_modify_time'] = @filemtime($hts_file);
//        @unlink($hts_file);
    }
}
?>
