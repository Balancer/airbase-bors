<?
//    error_reporting(E_ALL);

    function lcml_urls_pre_pre($txt)
    {
        $txt=preg_replace("!\[url\](.+?)\[/url\]!is","[url $1|$1]",$txt);
        $txt=preg_replace("!\[url=(.+?)\](.+?)\[/url\]!is","<a href=\"$1\">$2</a>",$txt);

        return $txt;
    }
?>
