<?
    function lp_code($txt,$params)
    {
/*        if(strpos($txt,"")>0)
        {
            die("<xmp>$txt</xmp>");
        }*/

        include_once("funcs/modules/colorer.php");

        $txt = colorer($txt, $params['orig']);//."<xmp>".print_r($params, true)."</xmp>";

/*        $txt = preg_replace( "#&lt;#"   , "&#60;", $txt );
        $txt = preg_replace( "#&gt;#"   , "&#62;", $txt );
        $txt = preg_replace( "#&quot;#" , "&#34;", $txt );
//        $txt = preg_replace( "#:#"      , "&#58;", $txt );
        $txt = preg_replace( "#\[#"     , "&#91;", $txt );
        $txt = preg_replace( "#\]#"     , "&#93;", $txt );
        $txt = preg_replace( "#\)#"     , "&#41;", $txt );
        $txt = preg_replace( "#\(#"     , "&#40;", $txt );
//        $txt = preg_replace( "#\n#"     , "<br>", $txt );
        $txt = preg_replace( "# {1};#" , "&#59;", $txt );*/
        
        // Ensure that spacing is preserved
        
//        $txt = preg_replace("!!", " ", $txt);
//        $txt = preg_replace("! +$!", "", $txt);

        if(preg_match("!(Created with colorer.+?Type ')(.+?)(')$!m",$txt,$m))
            $txt = preg_replace("!(Created with colorer.+?Type ')(.+?)(')$!m","",$txt);

        $txt=preg_replace("!^\n+!","",$txt);
        $txt=preg_replace("!\n+$!","",$txt);


        $txt=split("\n",trim($txt));
        foreach($txt as $s)
            $s=" $s";
        if(!empty($GLOBALS['lcml']['cr_type']) && $GLOBALS['lcml']['cr_type'] == 'empty_as_para')
            $txt=join("<br />\n",$txt);
        else
            $txt=join("\n",$txt);


        $txt="<table border='0' align='center' width='95%' cellpadding='3' cellspacing='1'><tr><td class='code' id='CODE'><tt>$txt</tt></td></tr></table>";
//        if(isset($m[2]))
//            $txt.="<div style=\"font-size: xx-small; text-align: right;\">Created with Colorer, type '<b>$m[2]</b>'</div>";
        
        $txt=preg_replace("!( {2,})!em","str_repeat('&nbsp;',strlen('$1'))",$txt);
//        $txt=str_replace(" ", "&nbsp", $txt);
        $txt=str_replace("[","&#91;",$txt);
        $txt=str_replace("-","&#45;",$txt);

        return $txt;
    }
?>