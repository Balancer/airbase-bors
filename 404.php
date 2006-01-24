<?
//    if(!empty($_COOKIE['member_id']) && $_COOKIE['member_id'] == 1)
//         xdebug_start_profiling();
    
//    if(!empty($_COOKIE['member_id']) && $_COOKIE['member_id'] == 1)
//        echo __FILE__.__LINE__." ".$GLOBALS['REQUEST_URI']."<br />\n";

    require_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config.php");

    header("Content-Type: text/html; charset={$GLOBALS['cms']['charset']}");
    header('Content-Language: ru');
    ini_set('default_charset',$GLOBALS['cms']['charset']);
    setlocale(LC_ALL, $GLOBALS['cms']['locale']);

    $query = $_SERVER['REQUEST_URI'];
    $ref   = empty($_SERVER['HTTP_REFERER']) ? NULL : $_SERVER['HTTP_REFERER'];
    $query=preg_replace("!^(.+?)/+$!","$1",$query);
    $query=urldecode($query);

    $page = @$_GET['page'];

    if(empty($_SERVER['REQUEST_URI']))
        $_SERVER['REQUEST_URI'] = $page;

    require_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config.php");
    require_once("funcs/navigation/go.php");
    require_once("funcs/filesystem_ext.php");

    require_once("funcs/strings.php");
    $QUERY_ENCODED=$query;

    //Ответим, что всё ок.
    header("HTTP/1.1 200 OK\n");
    header("Status: 200 OK\n");

    error_reporting(E_ALL  & ~E_NOTICE);

    $QUERY_ENCODED=preg_replace("!^/ubb/!","/forum/",$QUERY_ENCODED);
    if(preg_match("!^/forum/\d!",$QUERY_ENCODED))
    {
        go($QUERY_ENCODED);
    }

    $redir=array(
"/hangar/planes/mil/"=>"/hangar/select/mil/",
"/airbase.css"=>"/inc/css/old/airbase.css",
"/hangar/russia/mikoyan/mig/31/f/index.htm"=>"/sb/russia/mikoyan/mig/31/f/",
"/hangar/russia/mikoyan/mig/31/m/index.htm"=>"/sb/russia/mikoyan/mig/31/m/",
"/dreams/"=>"/cgi-bin/forum/ultimatebb.cgi?ubb=forum&f=10",
"/hangar/planes/mil/bombers/"=>"/hangar/select/mil/bombers/",
"/hangar/planes/civil/"=>"/hangar/select/civil/",
"/dreams/"=>"/cgi-bin/forum/ultimatebb.cgi?ubb=forum&f=10",
"/hangar/planes/mil/fighters/"=>"/hangar/select/mil/fighters/",
"/hangar/planes/yak-141/"=>"/hangar/planes/russia/yak/yak-141/",
"/hangar/planes/mil/fighters/multi/"=>"/hangar/select/mil/fighters/multi/",
"/hangar/planes/drones/"=>"/hangar/select/drones/",
"/hangar/planes/mil/bombers/frontline/"=>"/hangar/select/mil/bombers/front/",
"/hangar/planes/civil/pass/"=>"/hangar/select/civil/pass/",
"/hangar/planes/civil/cargo/"=>"/hangar/select/civil/cargo/",
"/hangar/planes/mil/attack/"=>"/hangar/select/mil/attack/",
"/hangar/planes/mil/navy/"=>"/hangar/select/mil/navy/",
);

    while(list($key,$val)=each($redir))
        if($QUERY_ENCODED==$key || "$QUERY_ENCODED/"==$key)
            go($val);

    if(preg_match("!^/go/!",$QUERY_ENCODED))
    {
        include("{$_SERVER['DOCUMENT_ROOT']}/go/index.phtml");
        exit();
    }

    list($QUERY_ENCODED,$args)=split("\?",$QUERY_ENCODED."?");

    if(!empty($args))
        foreach(split("&", $args."&") as $arg)
        {
            list($key, $value) = split("=", $arg."=");
            if($key && $value)
                $GLOBALS[$key] = $value;
        }

    if(preg_match("!^(.*)/(\w+)\.php$!",$QUERY_ENCODED,$data))
    {
        $script=$data[1]."/".$data[2].".phtml";
        if(file_exists("{$_SERVER['DOCUMENT_ROOT']}$script"))
            go("$script");
    }

    // Полноразмерные картинки
    if(preg_match("!^/images(/.+)/(.+?)\.(jpg|jpe|jpeg|png|gif)$!i",$QUERY_ENCODED,$m))
    {
        $url="$m[1]/$m[2].$m[3]";
        $img="{$_SERVER['DOCUMENT_ROOT']}$url";
        if(file_exists("$img"))
        {
            if(!$HTTP_REFERER)
            {
                header("Location: /images$m[1]/$m[2].htm");
                exit();
            }
            if(preg_match("!^{$GLOBALS['cms_host_url']}!",$HTTP_REFERER))
            {
                header("Status: 200 Ok");
                $size=filesize($img);
                header("Content-Length: $size");
                header("Content-type: image/jpeg");
                $f=fopen($img,"rb");
                echo fread($f,$size);
                fclose($f);
                exit();
            }
            header("Location: /cache$m[1]/200x150/$m[2].$m[3]");
            exit();
        }
    }

    include_once('show/image.php');

    if(preg_match("!^/images(/.+)/(.+?)\.htm$!i",$QUERY_ENCODED,$m))
    {
        $url="$m[1]/$m[2]";
        $img="{$_SERVER['DOCUMENT_ROOT']}$url";
        if(file_exists("$img.jpg"))     show_full_image("$url.jpg");
        if(file_exists("$img.jpe"))     show_full_image("$url.jpe");
        if(file_exists("$img.jpeg"))    show_full_image("$url.jpeg");
        if(file_exists("$img.png"))     show_full_image("$url.png");
        if(file_exists("$img.gif"))     show_full_image("$url.gif");
        if(file_exists("$img.JPG"))     show_full_image("$url.JPG");
        if(file_exists("$img.JPE"))     show_full_image("$url.JPE");
        if(file_exists("$img.JPEG"))    show_full_image("$url.JPEG");
        if(file_exists("$img.PNG"))     show_full_image("$url.PNG");
        if(file_exists("$img.GIF"))     show_full_image("$url.GIF");
    }

    // Страницы картинок
    if(preg_match("!^(.+)/(.+?)\.htm$!",$QUERY_ENCODED,$data))
    {
        $url="$data[1]/$data[2]";
        $img="{$_SERVER['DOCUMENT_ROOT']}$url";
        if(file_exists("$img.jpg"))     show_image("$url.jpg");
        if(file_exists("$img.jpe"))     show_image("$url.jpe");
        if(file_exists("$img.jpeg"))    show_image("$url.jpeg");
        if(file_exists("$img.png"))     show_image("$url.png");
        if(file_exists("$img.gif"))     show_image("$url.gif");
        if(file_exists("$img.JPG"))     show_image("$url.JPG");
        if(file_exists("$img.JPE"))     show_image("$url.JPE");
        if(file_exists("$img.JPEG"))    show_image("$url.JPEG");
        if(file_exists("$img.PNG"))     show_image("$url.PNG");
        if(file_exists("$img.GIF"))     show_image("$url.GIF");
    }

    // Иконки картинок
    if(preg_match("!^/cache(/.+)?/(200x150|200x|128x|468x468|128x96)/(.+?\.(jpg|jpeg|gif|jpe|png))$!i",$QUERY_ENCODED,$data))
    {
//        include("{$_SERVER['DOCUMENT_ROOT']}/scripts/inc/funcs.php");
		$image0 = "{$_SERVER['DOCUMENT_ROOT']}{$data[1]}/{$data[3]}";
		$image = "{$_SERVER['DOCUMENT_ROOT']}$QUERY_ENCODED";
        if(file_exists($image0))
        {
            list($w,$h)=split("x",$data[2]);

//            die($w."x".$h);

            list($w0,$h0,$t0)=GetImageSize($image0);
            if($w && !$h)
            {
				$w = $w0;
				$t = $t0;
                $h=(intval($h0*$size/($w0+1)+1));
            }

            if($w && $h)
            {
                $size=$w."x".$h;
   	            mkpath("{$_SERVER['DOCUMENT_ROOT']}/cache$data[1]/$data[2]",0777);
       	        @chmod("{$_SERVER['DOCUMENT_ROOT']}/cache$data[1]/$data[2]",0777);
				if($h0>$h || $w0>$w)
                	`/usr/local/bin/convert -geometry $size $image0 $image`;
				else
					copy($image0, $image);
                @chmod("{$_SERVER['DOCUMENT_ROOT']}$QUERY_ENCODED",0666);
   	            header("Location: {$_SERVER['REQUEST_URI']}");
       	        exit();
	        }
        }
    }

    // Иконки картинок
    if(preg_match("!^(.+)/(gal|200|128|468x468|128x96)/(.+?\.(jpg|jpeg|gif|jpe|png))$!i",$QUERY_ENCODED,$data))
    {
        if(file_exists("{$_SERVER['DOCUMENT_ROOT']}$data[1]/$data[3]"))
        {
            $size=$data[2];
            if($size=='gal') $size='200x150';
            if($size=='200' || $size=='128')
            {
                list($w,$h,$t)=GetImageSize("{$_SERVER['DOCUMENT_ROOT']}$data[1]/$data[3]");
                $size.='x'.(intval($h*$size/($w+1)+1));
            }
            @mkdir("{$_SERVER['DOCUMENT_ROOT']}$data[1]/$data[2]",0777);
            @chmod("{$_SERVER['DOCUMENT_ROOT']}$data[1]/$data[2]",0777);
            `/usr/local/bin/convert -geometry $size {$_SERVER['DOCUMENT_ROOT']}$data[1]/$data[3] {$_SERVER['DOCUMENT_ROOT']}$QUERY_ENCODED`;
            @chmod("{$_SERVER['DOCUMENT_ROOT']}$QUERY_ENCODED",0666);
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        }
    }

    function translate($s)
    {
        global $trans;
        $res="";
        for($i=0;$i<strlen($s);$i++)
        {
            $char=substr($s,$i,1);
//            echo "$char=>".$trans["$char"]."<br>";
            $res.=isset($trans["$char"])?$trans["$char"]:(ord($char)>127?"":$char);
        }
        return $res;
    }

    function is_digit($s){return strlen($s)==1 && ord($s)>=ord('0') && ord($s)<=ord('9');}
    function is_char($s){return strlen($s)==1 && (ord($s)>=ord('A') && ord($s)<=ord('Z') || ord($s)>=ord('a') && ord($s)<=ord('z'));}

    // Ключевые слова

    require_once('funcs/DataBaseHTS.php');
    $hts = new DataBaseHTS();

    $_SERVER['HTTP_HOST'] = str_replace(':80', '', $_SERVER['HTTP_HOST']);
    $full_uri = empty($page) ? "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" : $page;

//    if($hts->normalize_uri($full_uri) != $full_uri)
//        go($hts->normalize_uri($full_uri));

    $parse = $hts->normalize_uri($full_uri);


//    echo "********$full_uri = {$_SERVER['REQUEST_URI']} / $page****<br>\n";

    if($hts->get_data($full_uri, 'modify_time'))
    {
        include_once('main.php');
        exit();
    }

    $iid = substr($_SERVER['REQUEST_URI'],1);
    if(preg_match("!^\d+$!", $iid) && $hts->get_data($iid, 'body'))
    {
        $page = $hts->page_uri_by_id($iid);
        go($page);
    }

    if(substr($page,-1)=='/' && file_exists($parse['local_path'].'index.htm'))
        go("http://{$parse['host']}{$parse['path']}index.htm");

//    print_r($parse);
//    exit();

    $query=preg_replace("!/$!","",$QUERY_ENCODED);
    $query=preg_replace("!^/!","",$query);

    if($page = $hts->dbh->get("SELECT `id` FROM `hts_data_title` WHERE `value`='".addslashes($query)."' LIMIT 0,1"))
    {
        go($page);
        exit();
    }

    if($page = $hts->dbh->get("SELECT `id` FROM `hts_data_keyword` WHERE `value`='".addslashes($query)."' LIMIT 0,1"))
    {
//        $page = $hts->page_uri_by_id($page);
        go($page);
        exit();
    }

    $query=str_replace(".phtml",".hts",$QUERY_ENCODED);
    if(substr($query,-4) != ".hts")
    {
        if(substr($query,-1)!="/") $query.="/";
        $query.="index.hts";
    }

//    echo "$query<br>";

    preg_match("!^/?(.+)/(.+?)$!",$query,$m);

    $query=$m[1];
    $file_name=$m[2];

    $trans=array(
        'A'=>'a','B'=>'b','C'=>'c','D'=>'d','E'=>'e','F'=>'f','G'=>'g','H'=>'h','I'=>'i','J'=>'j','K'=>'k','L'=>'l','M'=>'m','N'=>'n','O'=>'o','P'=>'p','Q'=>'q','R'=>'r','S'=>'s','T'=>'t','U'=>'u','V'=>'v','W'=>'w','X'=>'x','Y'=>'y','Z'=>'z',
        'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'zh','з'=>'z','и'=>'i','й'=>'j','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'kh','ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'sch','ъ'=>'','ы'=>'y','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya',
        'А'=>'a','Б'=>'b','В'=>'v','Г'=>'g','Д'=>'d','Е'=>'e','Ё'=>'e','Ж'=>'zh','З'=>'z','И'=>'i','Й'=>'j','К'=>'k','Л'=>'l','М'=>'m','Н'=>'n','О'=>'o','П'=>'p','Р'=>'r','С'=>'s','Т'=>'t','У'=>'u','Ф'=>'f','Х'=>'kh','Ц'=>'ts','Ч'=>'ch','Ш'=>'sh','Щ'=>'sch','Ъ'=>'','Ы'=>'y','Ь'=>'','Э'=>'e','Ю'=>'yu','Я'=>'ya',
        '-'=>'/','.'=>'/',' '=>'/',
    );

    $suffix='';
    $first_char=isset($trans[substr($query,0,1)])?$trans[substr($query,0,1)]:substr($query,0,1);

    $translated=$query;
    if(ord(substr($query,0,1))>127)
        $suffix='rus/';

    $translated=$res=translate($query);
    //echo "=$query=$res=".$trans["-"];

    $last_type='';
    $type='';
    $res='';

    for($i=0;$i<strlen($translated);$i++)
    {
        $char=substr($translated,$i,1);
        if($char!="/")
        {
            if(is_digit($char))
                $type='d';
            else
                if(is_char($char))
                    $type='c';
                else
                    $type='o';
            if($type!=$last_type && $last_type!='')
                $res.="/";
        }
        else
            $type='';
        $res.=$char;
        $last_type=$type;
    }

    if(!$_SERVER['REQUEST_URI'])
        $page="http://airbase.ru/alpha/$suffix$first_char/$res/$file_name";
    else
        $page=$full_uri;

    $dest_file=preg_replace("!^http://airbase.ru(.+?)\.hts!","$1",$page);
    if(file_exists("{$_SERVER['DOCUMENT_ROOT']}$dest_file.hts") || file_exists("{$_SERVER['DOCUMENT_ROOT']}$dest_file.phtml"))
    {
        header("Location: $dest_file.phtml");
        exit();
    }

    if(!empty($title) && !empty($page))
    {
        $ref = empty($GLOBALS['HTTP_REFERER']) ? '' : $GLOBALS['HTTP_REFERER'];
        go("?title=".urlencode($GLOBALS['title'])."&ref=$ref");
    }
    
?>
<body bgcolor=#c0c0c0>
<font face=Verdana size=-1>

<!--<hr>
<h2>Служебная информация</h2>
<?
    echo "HTTP_REFERER=$HTTP_REFERER<br>\n";
//    echo "SERVER_NAME=$SERVER_NAME<br>\n";
    echo "REQUEST_URI={$_SERVER['REQUEST_URI']}<br>\n";
    echo "HTTP_REFERER=$HTTP_REFERER<br>\n";
//    echo "HTTP_USER_AGENT=$HTTP_USER_AGENT<br>\n";
    echo "REMOTE_HOST=$REMOTE_HOST<br>\n";
//    echo "REMOTE_ADDR=$REMOTE_ADDR<br>\n";
    echo "HTTP_X_FORWARDED_FOR=$HTTP_X_FORWARDED_FOR<br>\n";

?>
<hr>-->
<ul>
<?

//    echo "(ref='$ref' title='$title' && page='$page')";

    $query=substr($QUERY_ENCODED,1);
    if(strpos($query,"/")===false)
    {
        $keyword="&keyword=$query";
        echo "<li><a href=\"?edit$keyword&ref=$ref\">создать страницу</a> <font color=red>$QUERY_ENCODED</font> в новом формате (<a href=\"?edit\">$page</a>)\n";
    }
    else
        $keyword="";
    
    $script=get_script($QUERY_ENCODED);

    if(substr($script,-4)==".hts")
        $script=substr($script,0,-4).".phtml";

    if(substr($script,-4)==".htm")
        $script=substr($script,0,-4).".phtml";

    if(substr($script,-6)!=".phtml")
    {
        if(substr($script,-1)!="/") $script.="/";
        $script.="index.phtml";
    }

    $path=dirname($script);
    $filename=preg_replace("!^.+/(.+)\..+?$!","$1",$script);

    if(substr($script,-11)=="index.phtml")
        $file=@file($_SERVER['DOCUMENT_ROOT'].secure_path(dirname($script)."/../index.hts"));
    else
        $file=@file($_SERVER['DOCUMENT_ROOT'].dirname($script)."/index.hts");

    if($script=="/rus.phtml"||$script=="/eng.phtml")
        $file="";

    $nick=user_data("nick");
    $page="http://{$_SERVER['HTTP_HOST']}".preg_replace("!^(/.+)\.phtml$!","$1.hts",$script);
    $edit_uri = "http://airbase.ru/admin/edit.php?page=$page&ref=$ref";
    if(!preg_match("![\x80-\xFF]!",$query))
        echo "<li><a href=\"$edit_uri\">создать страницу</a> <font color=red>$QUERY_ENCODED</font> в старом формате (<a href=\"$edit_uri\">$page</a>)\n";
?>
</ul>
<br><br>
</body>
