<?
    // Smilies processing
    // Global vars:
    // $GLOBALS['cms_smilies_dir'] - full path to smilies dir
    // $GLOBALS['cms_smilies_url'] - full or relative url of smilies dir
    //
    // (c) Balancer 2003-2004

    function lcml_smilies($txt)
    {
        $smilies=@file($GLOBALS['cms_smilies_dir']."/list.txt");
        for($i=0;$i<sizeof($smilies);$i++)
        {
            $spl=split(" ",chop($smilies[$i]));
            $spl[]="";
            list($code,$file)=$spl;
            if(!$file)
            {
                $txt=preg_replace("!([^\"]):$code:([^\"])!","$1<img src=\"{$GLOBALS['cms_smilies_url']}/$code.gif\" alt=\":$code:\" title=\":$code:\" border=\"0\">$2",$txt);
            }
            else
            {
                $from=array("/\(/","/\)/","/\[/","/\]/","/\-/","/\*/","/\+/","/\./","/\?/","/\|/","/\!/");
                $to=array("\\\(","\\\)","\\\[","\\\]","\\\-","\\\*","\\\+","\\\/","\\\?","\\\|","\\\!");
                $txt=preg_replace("!(^|\s)".preg_replace($from,$to,$code)."(?=(\s|$|\)|\]|\.))!","$1<img src=\"{$GLOBALS['cms_smilies_url']}/$file.gif\" alt=\"$code\" title=\"$code\" border=\"0\">",$txt);
            }
        }

        $txt=lcml_smilies_load($GLOBALS['cms_smilies_dir'],$txt);

        return $txt;
    }

    function lcml_smilies_load($dir,$txt)
    {
        if(is_dir($dir))
        {
            if($dh = opendir($dir)) 
            {
                while(($file = readdir($dh)) !== false) 
                {
                    if(substr($file,-4)=='.gif')
                    {
                        $code=substr($file,0,-4);
                        $txt=preg_replace("!(^|[^\"]):$code:!","$1<img src=\"{$GLOBALS['cms_smilies_url']}/$code.gif\" alt=\":$code:\" title=\":$code:\" border=\"0\">",$txt);
                    }
                    elseif(filetype("$dir/$file")=='dir' && substr($file,0,1)!='.')
                    {
                        $txt=lcml_smilies_load("$dir/$file",$txt);
                    }
                }
                closedir($dh);
            }
        }
        return $txt;
    }

    $txt = lcml_smilies($txt);
?>
