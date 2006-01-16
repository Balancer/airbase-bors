<?
    function lp_quote($txt,$params)
    {
		if(empty($params['description']))
			$out = "\n\n<blockquote><div class=\"incqbox quotemain\">";
		else
			$out = "\n\n<blockquote><div class=\"incqbox quotemain\"><small><b><div class=\"quotetop\" style=\"border-bottom-width: 1px; border-bottom-style: solid;\">{$params['description']}</div></b></small>";
		return $out.lcml($txt)."</div></blockquote>";
    }
?>