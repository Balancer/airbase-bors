<?
    error_reporting(E_ALL);

    function lcml_urls_pre_post($txt)
    {
        $n=100;
        while((
                preg_match("!\[([^\]\s\|]*?/[^\]\s\|]*?)\|(.+?)\]!is", $txt, $m) // "!isu for _utf8_
                ||
                preg_match("!\[([^\]\s]*?/[^\]\s]*?)&#124;(.+?)\]!is", $txt, $m) // "!isu for _utf8_
                ) && $n-->0)
            $txt = str_replace($m[0], lcml("[url {$m[1]}|{$m[2]}]"), $txt);

        return $txt;
    }
?>
