<?
    function lp_pre($text,$params)
    {
        $text = preg_replace("! !","&nbsp;",$text);
        $text = preg_replace("!<br>!","\n",$text);
		$text = str_replace("\n", "---save_cr---", $text);
        return "<pre><span style=\"font-size:14pt; font-family:Courier New;\">$text</span></pre>\n";
    }

    function lp_cr_as_br($text) { return preg_replace("!\n!", " <br>\n", $text); }
