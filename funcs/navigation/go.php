<?
    require_once('debug.php');

    function go($uri, $permament = true)
    {
        if (!headers_sent($filename, $linenum)) 
        {
            header("Status: 302 Moved temporary");
            header("Location: $uri");
            exit();
        }

        echo "Загружаю страницу <a href=\"$uri\">$uri</a><br>\n<meta http-equiv=\"refresh\" content=\"5; url=$uri\">";

        debug("headers already out in $filename:$linenum");

        exit();
    }
?>
