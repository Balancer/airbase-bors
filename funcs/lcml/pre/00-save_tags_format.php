<?
    function lcml_save_tags_format($txt)
    {
		foreach(split(' ', 'code music') as $tag)
			$txt = preg_replace("!(\[$tag(\W[^\]]*)?\])(.+?)(\[/$tag\])!ise", "'$1'.save_format(stripslashes('$3')).'$4'", $txt);

        return $txt;
    }
