<?
	function str2js($text)
	{
		$out = "with(document){";

	    foreach(split("\n", $text) as $s)
	        $out .= "write(\"".addslashes($s)."\");\n";

		$out = preg_replace('!<script>(.+?)</script>!', "\"+$1+\"", $out);

		return $out."}";
	}
