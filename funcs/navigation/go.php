<?
    require_once('debug.php');

    function go($uri, $permament = true, $time = 0, $text = true)
    {
        if(!headers_sent($filename, $linenum) && $time==0) 
        {
            header("Status: 302 Moved temporary");
            header("Location: $uri");
            exit();
        }

//		if($text)
//	        echo "Load page <a href=\"$uri\">$uri</a><br />\n";
			
		echo "<meta http-equiv=\"refresh\" content=\"$time; url=$uri\">";

        debug("headers already out in $filename:$linenum");

        exit();
    }
?>
