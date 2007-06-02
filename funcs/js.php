<?
	function str2js($text)
	{
		$out = "with(document){";

	    foreach(split("\n", $text) as $s)
	        $out .= "write(\"".addslashes($s)."\");\n";

		return $out."}";
	}
