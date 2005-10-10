<?
    function lcml_chars($text)
    {
        $text = preg_replace("!<<([^\?].+?[^\?])>>!su", "&#171;$1&#187" ,$text);
        $text = preg_replace("! -- !", " &#151; " ,$text);

        return $text;
    }
?>