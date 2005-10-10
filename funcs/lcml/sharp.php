<?
//    error_reporting(E_ALL);

    include_once("extentions.php");
    ext_load($GLOBALS['cms']['base_dir'].'/funcs/lcml/sharp');

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
                if(function_exists("lst_$m[1]"))
                {
                    $func="lst_$m[1]";
                    $array[$i]=$func(trim($m[3]));
                    $changed=1;
                    continue;
                }

                if(function_exists("lsp_$m[1]"))
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
                    $func="lsp_$tag";
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
?>