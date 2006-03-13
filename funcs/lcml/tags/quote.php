<?
    function lp_quote($txt,$params)
    {
		if(empty($params['description']))
			$out = "</p><blockquote><div class=\"incqbox quotemain\"><p>";
		else
			$out = "</p><blockquote><div class=\"incqbox quotemain\"><small><b><div class=\"quotetop\" style=\"border-bottom-width: 1px; border-bottom-style: solid;\">{$params['description']}</div></b></small><p>";
		return $out.lcml($txt)."</p></div></blockquote><p>\n";
    }
?>