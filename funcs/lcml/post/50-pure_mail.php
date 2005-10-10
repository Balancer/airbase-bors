<?
    function lcml_pure_mail($txt)
    {
		$mail_chars = 'a-zA-Z0-9\_\-\+\.';
        $txt=preg_replace("!(\s+|^|\])([$mail_chars]+@[$mail_chars]+)(\s+|$|\[)!im","$1<a href=\"mailto:$2\">$2</a>$3",$txt);

        return $txt;
    }
?>
