<?
//    require_once('debug.php');

    function go($uri, $permanent = false, $time = 0, $exit = true)
    {
//		echo 0/0; exit("Go to $url");
	
        if(!headers_sent($filename, $linenum) && $time==0) 
        {
			if($permanent)
	            header("Status: 301 Moved Permanently");
			else
	            header("Status: 302 Moved Temporarily");
			if(preg_match("!\n!", $uri))
				echolog("cr in uri '$uri'", 1);
				
			header("Location: $uri");
			if($exit)
	            exit();
        }

//		if($text)
//	        echo "Load page <a href=\"$uri\">$uri</a><br />\n";
			
		echo "<meta http-equiv=\"refresh\" content=\"$time; url=$uri\">";

//        debug("headers already out in $filename:$linenum");

		if($exit)
	        exit();
		return true;
    }

    function go_ref($def = "/")
	{
		if(!empty($GLOBALS['ref']))
			go($GLOBALS['ref']);

		if(!empty($_SERVER['HTTP_REFERER']))
			go($_SERVER['HTTP_REFERER']);
			
		go($def);
	}
