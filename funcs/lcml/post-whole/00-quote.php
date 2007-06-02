<?
    function lcml_quote($txt)
    {
		$res = "";
		foreach(split("\n", $txt) as $s)
		{
			$break = false;
		
			if(strlen($s) > 255)
			{
				foreach(split(' ', $s) as $tmp)
					if(strlen($tmp) > 255)
					{
						$res .= "$s\n";
						$break = true;
						break;
					}
			}
			
			if($break)
				continue;
			
			$s = preg_replace("!^(\s*)([^\s><]*?)(&gt;|>)(.+?)$!s", "$1<span class=\"q\"><b>$2</b>&gt;$4</span>", $s);
			$res .= "$s\n";
		}
		
        return $res;
    }
