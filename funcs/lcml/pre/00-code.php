<?
    function lcml_code($txt)
    {
        $txt = preg_replace("![\s \n\t\r]+(\[code)!s","\n$1",$txt);
        $txt = preg_replace("!(\[/code\])[\s \n\t\r]+!s","$1\n",$txt);
		$txt = preg_replace("!(\[code\])(.+?)(\[/code\])!ise", '"$1".str_replace("[","<!--lcml_left_bracket-->","$2")."$3"', $txt);
        return $txt;
    }
?>