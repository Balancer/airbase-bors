<?
    function lcml_quote($txt)
    {
        $txt=preg_replace("!^(\s*)([^\s><]*?)(&gt;|>)(.+?)$!ms","$1<font size=\"-2\" color=\"#808080\"><b>$2</b>&gt;$4</font>",$txt);
        return $txt;
    }
