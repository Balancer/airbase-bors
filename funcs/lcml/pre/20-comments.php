<?
//    error_reporting(E_ALL);

    function lcml_comments($txt)
    {
        $txt=preg_replace("!^# .+$!m","",$txt);

        return $txt;
    }
?>
