<?
    function lcml_color($txt)
    {
		$txt = preg_replace("!\[color=(.+?)\]!","[color name=\"$1\"]", $txt);
        return $txt;
    }
?>
