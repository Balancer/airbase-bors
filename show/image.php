<?php
    require_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config.php");
    require_once('funcs/DataBaseHTS.php');
    require_once('funcs/users.php');
    require_once("funcs/images/fill.php");
    ini_set('default_charset','utf-8');
    @header('Content-Type: text/html; charset=utf-8');
    setlocale(LC_ALL, "ru_RU.utf8");

    function show_image($img)
    {
        global $doc_root, $host;
        preg_match("!^(.+)/(.+?)\.([^\.]+?)$!",$img,$m);

        $hts = new DataBaseHTS();
        $img = $hts->normalize_uri($img);

        fill_image_data($img);

        $title = $hts->get_data($img, 'title');
        $desc  = $hts->get_data($img, 'description', $img);

?>
<html>
<head>
<title><?php echo $title?> /Авиабаза =KRoN=/</title>
<link rel="stylesheet" type="text/css" href="http://www.airbase.ru/inc/css/style.phtml">
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<?php/*include("commerce-banner.phtml");*/?>
<br><table width="500" cellPadding="0" cellSpacing="0">
<tr><td><div id="head">
<h1><?php echo $title;?></h1>
<h3><?php echo $desc;?></h3>
<h4><?php echo $hts->get_data($img, 'copyright');?></h4>
</div></tr></td></table>
<?php
        $w = $hts->get_data($img, 'width');
        $h = $hts->get_data($img, 'height');
        $t = $hts->get_data($img, 'type');
        $x = "width=\"$w\" height=\"$h\"";

        $ow=$w;
        $oh=$h;
        if($w>468 || $h>468)
        {
            if($w>$h) // "широкая" картинка
            {
                $h=intval($h*468/$w);
                $w=468;
                $x="width=$w height=$h";
            }
            else // "высокая" картинка
            {
                $w=intval($w*468/$h);
                $h=468;
                $x="width=$w height=$h";
            }
            $ico_url="/cache$m[1]/468x468/$m[2].$m[3]";

            $ico640_url="/cache$m[1]/640x480/$m[2].$m[3]";
            $ico800_url="/cache$m[1]/800x600/$m[2].$m[3]";
            $ico1024_url="/cache$m[1]/1024x768/$m[2].$m[3]";

            function img_x_size($img) 
			{ 
				global $host, $doc_root; 
				$tmp = @file("http://{$_SERVER['HTTP_HOST']}$img"); 
				list($w,$h,$rest) = @getimagesize($tmp); 
				return $w."x".$h; 
			}
			
            $ico640_url =($ow>640  || $oh>480)?"<a href=\"$ico640_url\">".img_x_size("$ico640_url")."</a>":"";
            $ico800_url =($ow>800  || $oh>600)?"<a href=\"$ico800_url\">".img_x_size("$ico800_url")."</a>":"";
            $ico1024_url=($ow>1024 || $oh>768)?"<a href=\"$ico1024_url\">".img_x_size("$ico1024_url")."</a>":"";

            $icons=$ico640_url?"Другие размеры: ".join(" ",(array($ico640_url,$ico800_url,$ico1024_url))):"";

            ///images$m[1]/$m[2].htm
            $code="$icons<br><a href=\"$img\"><img border=\"0\" src=\"$ico_url\" $x></a><br>При нажатии на картинку, откроется полноразмерная копия $ow"."x$oh";
        }
        else
            $code="<img src=\"$img\" $x>";

        echo "<br>$code";

//      echo "<br><img src=$img width=$w height=$h border=0><br>";
?>
<table width="500" cellSpacing="0" class="btab">
<tr><th align="right">Размер:</th><td><?php echo "{$ow}x{$oh}, ".intval($hts->get_data($img, 'size')/1024+0.5)."Kb"?></td></tr>
<tr><th align="right">Выложено на сайт:</th><td><?php echo user_data("nick",$hts->get_data($img, 'author'))." ".strftime("%d.%m.%Y %H:%M:%S",$hts->get_data($img, 'create_time'));?></td></tr>
<?php
    if($hts->get_data($img, 'origin_uri'))
        echo "<tr><th align=\"right\">Оригинальная ссылка:</th><td><a href=\"".$hts->get_data($img, 'origin_uri')."\">".$hts->get_data($img, 'origin_uri')."</a></td></tr>\n";
?>
<tr><th align="right">Страницы с этой картинкой:</th><td><small><ul><?php
        foreach($hts->get_data_array($img, 'parent') as $parent)
        {
            $title = $hts->get_data($parent, 'title');
            echo "<li><nobr><a href=\"$parent\">$title</a></nobr>\n";
        }
?></ul></small></td></tr>
<!--<tr><td colSpan="2"><a href="/admin/img.phtml?img=<?php echo$img?>">Редактировать параметры картинки</a></td></tr>-->
</table>
</body></html>
<?php
        exit();
    }

    function show_full_image($img)
    {
        echo "<html><head><link rel=stylesheet type=text/css href=/inc/css/style.phtml></head><body><center>";
        include("{$_SERVER['DOCUMENT_ROOT']}/inc/banners-top.phtml");
        list($w,$h,$t,$x)=GetImageSize("{$_SERVER['DOCUMENT_ROOT']}$img");
        preg_match("!^(.+)/(.+?)\.([^\.]+?)$!",$img,$m);
        echo "<br>[ <a href=$m[1]/$m[2].htm>Назад на страницу картинки</a> ]<br><br><img src=/images$m[1]/$m[2].$m[3] $x><br>[ <a href=$m[1]/$m[2].htm>Назад на страницу картинки</a> ]</center></body></html>";
        exit();
    }

?>