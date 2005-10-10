<?
    // Smilies processing
    // Global vars:
    // $GLOBALS['cms_smilies_dir'] - full path to smilies dir
    // $GLOBALS['cms_smilies_url'] - full or relative url of smilies dir
    //
    // (c) Balancer 2003-2004

    include_once('funcs/Cache.php');

    function lcml_smilies($txt)
    {
        $smilies=file($GLOBALS['cms']['smilies_dir']."/list.txt");
//		echo "****";
        for($i=0;$i<sizeof($smilies);$i++)
        {
            $spl=split(" ",str_replace("\r","",chop($smilies[$i])));
            $spl[]="";
            list($code,$file)=$spl;
            if(!$file)
            {
                $txt=preg_replace("!([^\"]):$code:([^\"])!","$1<img src=\"{$GLOBALS['cms']['smilies_url']}/$code.gif\" alt=\":$code:\" title=\":$code:\" border=\"0\">$2",$txt);
            }
            else
            {
//                debug("Smile: =$code=$file=$txt=");
                
                $from=array("/\(/","/\)/","/\[/","/\]/","/\-/","/\*/","/\+/","/\./","/\?/","/\|/","/\!/");
                $to=array("\\\(","\\\)","\\\[","\\\]","\\\-","\\\*","\\\+","\\\/","\\\?","\\\|","\\\!");

//                debug("txt=preg_replace(\"!(^|\s)\".preg_replace($code)\"(?=(\s|$|\)|\]|\.))!\",\"1<img src=\"{$GLOBALS['cms']['smilies_url']}/$file.gif\" alt=\"$code\" title=\"$code\" border=\"0\">\",$txt);");

                $txt=preg_replace("!(^|\s)".preg_replace($from,$to,$code)."(?=(\s|$|\)|\]|\.))!us","$1<img src=\"{$GLOBALS['cms']['smilies_url']}/$file.gif\" alt=\"$code\" title=\"$code\" border=\"0\">",$txt);
            }
        }

        $txt = lcml_smilies_by_files($GLOBALS['cms']['smilies_dir'],$txt);

        return $txt;
    }

    function lcml_smilies_by_files($dir,$txt)
    {
        foreach(lcml_smilies_load($dir) as $code)
            $txt=preg_replace("![^\"]:$code:!","$1<img src=\"{$GLOBALS['cms']['smilies_url']}/$code.gif\" alt=\":$code:\" title=\":$code:\" border=\"0\">",$txt);
        return $txt;
    }

    function lcml_smilies_load($dir)
    {
        $cache = new Cache;
        $cache->clear_check('smilies', $dir,3600);
        if($cache->get('smilies', $dir))
            return unserialize($cache->last());

        $list = array();
        if(is_dir($dir))
        {
            if($dh = opendir($dir)) 
            {
                while(($file = readdir($dh)) !== false) 
                {
                    if(substr($file,-4)=='.gif')
                        $list[] = substr($file,0,-4);
                    elseif(filetype("$dir/$file")=='dir' && substr($file,0,1)!='.')
                        $list = array($list, lcml_smilies_load("$dir/$file"));
                }
                closedir($dh);
            }
        }
        $cache->set('smilies', $dir, serialize($list));
//        print_r($list);
        return $list;
    }
?>
