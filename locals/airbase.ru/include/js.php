<?php 
@header('Content-Type: text/html; charset=utf-8');
@header('Content-Language: ru');

ini_set('default_charset','utf-8');

setlocale(LC_ALL, "ru_RU.utf8");
?>
top.data = <?php 
    $uri=preg_replace("!^.+/js\.php/!","",$REQUEST_URI);
    ob_start();

    if(preg_match("!http://!", $uri))
        include($uri);
    else
    {
        list($uri,$params)=split("\?",$uri);
        if($params)
        {
            $pairs=split("&",$params);
            for($i=0;$i<sizeof($pairs);$i++)
            {
                list($key,$val)=split("=",$pairs[$i]);
                $$key=urldecode($val);
            }
        }
        
        include("$DOCUMENT_ROOT/$uri");
    }

    $file = split("\n",ob_get_contents());
    
    ob_end_clean();

    $out = array();

    for($i=0;$i<sizeof($file);$i++)
    {
        $s=str_replace("\\","\\\\",$file[$i]);
        $s=str_replace("\"","\\\"",$s);
//        $s=str_replace("\n"," ",$s);
        $s=str_replace("\r","",$s);
        $s=preg_replace("! src=(\")?/!", " src=$1http://www.airbase.ru/", $s);
        $s=preg_replace("! href=(\")?/!", " href=$1http://www.airbase.ru/", $s);
        $out[] = "\"$s\\n\"";
    }
    echo join("+\n", $out);
?>

if(top.insert_element)
{
	top.insert_element.innerHTML = top.data
	top.data = ''
}
