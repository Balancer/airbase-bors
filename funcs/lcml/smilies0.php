<?
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
                $txt=preg_replace("!(^|\s)".preg_replace($from,$to,$code)."(?=(\s|$|\)|\]|\.))!","$1<img src=\"{$GLOBALS['cms_smilies_url']}//$file.gif\" alt=\"$code\" title=\"$code\" border=\"0\">",$txt);
            }
        }
        return $txt;
    }

    $txt = lcml_smilies($txt);
?>
