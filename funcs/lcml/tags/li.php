<?
/*    function lt_li($text)
    {
        return "<li />";
    }
*/
    function lp_li($text)
    {
        return "<li>".lcml($text)."</li>\n";
    }

    function lp_ul($text)
    {
        return "<ul>\n".lcml($text)."</ul>\n";
    }
