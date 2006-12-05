<?
	include_once("funcs/modules/translit.php");
	
    function module_chat_main()
    {
		$text = `cat /usr/local/games/l2j-server/log/chat.log|grep -P '( ALL | SHOUT )'|tail -n 200`;
		$text = str_replace("\r",'', $text);
#		$text = htmlspecialchars($text);
		foreach(split("\n", $text) as $s)
		{
			$s = preg_replace("!^(\[.+?\])\s+(ALL|SHOUT)\s+\[(.+?)\](.*?)$!ue", "\"<small><font color=\\\"#b0b0b0\\\">$1</font></small> <font color=\\\"\".('$2'=='SHOUT'?'orange':'#e0e0e0').\"\\\">$3: \".from_translit(stripslashes(\"$4\")).\"</font><br />\n\"", $s);
			echo "$s";
		}
    }

    module_chat_main();
?>

