<?
    function lcml_quote($txt)
    {
        $txt=preg_replace("!^(\s*)([^\s><]*?)(&gt;|>)(.+?)$!ms","$1<span class=\"q\"><b>$2</b>&gt;$4</span>",$txt);
        return $txt;
    }
