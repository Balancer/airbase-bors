<?
    require_once("funcs/DataBaseHTS.php");

    function lcml_wiki($txt)
    {
        $txt = preg_replace("!\[\[([^\[]+?)\|([^\[]+)\]\]!e", "lcml_wiki_do('$1','$2')", $txt);
        $txt = preg_replace("!\[\[([^\[]+)\]\]!e", "lcml_wiki_do('$1')", $txt);

        return $txt;
    }

    function lcml_wiki_do($title, $text = NULL)
    {
        if(!$text)
            $text = $title;

        $hts = new DataBaseHTS();

        $pid = $hts->page_id_by_value('title', $title);
        if(!$pid)
            return "<a href=\"http://airbase.ru/admin/wiki-new.php?title=".urlencode($title)."&ref=".urlencode($GLOBALS['page'])."\" class=\"wiki_none\">$text</a>";

        $uri = $hts->page_uri_by_id($pid);
        return "<a href=\"$uri\" class=\"wiki_int\">$text</a>";
        
    }
?>