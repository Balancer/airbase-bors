<?
    function lcml_code($txt)
    {
        $txt = preg_replace("![\s \n\t\r]+(\[code)!s","\n$1",$txt);
        $txt = preg_replace("!(\[/code\])[\s \n\t\r]+!s","$1\n",$txt);
//		$txt = preg_replace("!(\[code[^\]]*\])(.+?)(\[/code[^\]]*\])!ise", '"$1".str_replace(array("[","<",">"), array("lcml_save_left_bracket", "lcml_save_lt", "lcml_save_gt"), ("$2"))."$3"', $txt);

		$txt = preg_replace("!(\[code[^\]]*\])(.+?)(\[/code[^\]]*\])!ise", 
			"\"$1\".str_replace(array('[','<','>'), array('lcml_save_left_bracket', 'lcml_save_lt', 'lcml_save_gt'), str_replace(\"\\'\", \"'\", \"$2\")).\"$3\"", 
			$txt);

        return $txt;
    }
