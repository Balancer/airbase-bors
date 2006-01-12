<?
//    error_reporting(E_ALL);

    function lcml_comments($txt)
    {
		if(! @$GLOBALS['lcml']['sharp_not_comment'])
	        $txt=preg_replace("!^# .+$!m","",$txt);

        return $txt;
    }
?>
