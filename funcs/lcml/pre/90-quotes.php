<?
    function lcml_quotes($txt)
    {
		$txt = preg_replace("!\[quote=([^\]]+?),([^\]]+?)\]!","[quote|$1, $2:]", $txt);
		$txt = preg_replace("!\[quote=([^\]]+?)\]!","[quote|$1:]", $txt);
        return $txt;
    }
?>
