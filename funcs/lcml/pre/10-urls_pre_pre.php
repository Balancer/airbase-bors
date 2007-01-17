<?
    function lcml_urls_pre_pre($txt)
    {
        $txt = preg_replace("!\[url\](.+?)\[/url\]!is", "<a href=\"$1\">$1</a>", $txt);
        $txt = preg_replace("!\[url=([^]]+?)\](.+?)\[/url\]!is", "<a href=\"$1\">$2</a>", $txt);
        $txt = preg_replace("!\[img=([^]]+?)\]!is", "<img src=\"$1\" />", $txt);

        return $txt;
    }
