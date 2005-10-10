<?
    // URLs processing
    // Global vars: none
    //
    // (c) Balancer 2003-2004
    // 07.06.04 исправлена обработка ссылок, "упирающихся" в тэг, например, <li> http://www.ru/<li>

    function lcml_strip_url($url)
    {
            return strlen($url)>77?substr($url,0,50).' [ ... ] '.substr($url,-20):$url;
    }

    function lcml_urls_title($url)
    {
        require_once "HTTP/Request.php";
        $req =& new HTTP_Request($url);
        $req->addHeader("Content-Encoding", 'gzip');
        
        $req->sendRequest();

        if(PEAR::isError($response)) 
            return lcml_strip_url($url);

        $location = $req->getResponseHeader('Location');
        if($location)
        {
            if(substr($location,0,1)=='/')
            {
                $ura =  parse_url($url);
                $location=$ura['scheme'].'://'.$ura['host'].$location;
            }
            $req->setURL($location);
            $req->sendRequest();
            if(PEAR::isError($response)) 
                return lcml_strip_url($url);
        }

        $data = $req->getResponseBody();

        $content_type = $req->getResponseHeader('Content-Type');
        if(preg_match("!charset=(\S+)!i",$content_type,$m))
            $charset = $m[1];
        else
            $charset = '';

        if(preg_match("!<meta http\-equiv=\"Content\-Type\"[^>]+charset=(.+?)\">!i",$data,$m))
            $charset = $m[1];

        if(preg_match("!<title>(.+?)</title>!is",$data,$m)) //@file_get_contents($url)
        {
            if($charset)
                $m[1] = iconv($charset,'utf-8//translit', $m[1]);
            return substr(trim(preg_replace("!\s+!"," ",str_replace("\n"," ",$m[1]))),0,256);
        }

        return lcml_strip_url($url);
    }

    function lcml_urls($txt)
    {
        $txt=preg_replace("!\[(http://[^\s\|\]]+?)\]!ie","'<a href=\"$1\">'.lcml_urls_title('$1').'</a>'",$txt);
        $txt=preg_replace("!\[(www\.[^\s\|\]]+?)\]!ie","'<a href=\"http://$1\">'.lcml_urls_title('http://$1').'</a>'",$txt);
        $txt=preg_replace("!\[(ftp://[^\s\|\]]+?)\]!i","<a href=\"$1\">$1</a>",$txt);
        $txt=preg_replace("!\[(ftp\.[^\s\|\]]+?)\]!i","<a href=\"ftp://$1\">$1</a>",$txt);

        $txt=preg_replace("!(?<=\s|^|\()(http://[^\s<>]+)(\)|\.|,|\!|\-)(?=\s|$)!ie","'<a href=\"$1\">'.lcml_urls_title('$1').'</a>$2'",$txt);
        $txt=preg_replace("!(?<=\s|^|\()(http://[^\s<>]+)(?=\s|$)!ie","'<a href=\"$1\">'.lcml_urls_title('$1').'</a>'",$txt);
        $txt=preg_replace("!(?<=\s|^|\()(www\.[^\s<>]+)(\)|\.|,|\!|\-)(?=\s|$)!ie","'<a href=\"http://$1\">'.lcml_urls_title('http://$1').'</a>$2'",$txt);
        $txt=preg_replace("!(?<=\s|^|\()(www\.[^\s<>]+)(?=\s|$)!ie","'<a href=\"http://$1\">'.lcml_urls_title('http://$1').'</a>'",$txt);

        $txt=preg_replace("!(?<=\s|^|\()(ftp://[^\s<>]+)(\)|\.|,|\!|\-)(?=\s|$)!i","<a href=\"$1\">$1</a>$2",$txt);
        $txt=preg_replace("!(?<=\s|^|\()(ftp://[^\s<>]+)(?=\s|$)!i","<a href=\"$1\">$1</a>",$txt);
        $txt=preg_replace("!(?<=\s|^|\()(ftp\.[^\s<>]+)(\)|\.|,|\!|\-)(?=\s|$)!i","<a href=\"ftp://$1\">$1</a>$2",$txt);
        $txt=preg_replace("!(?<=\s|^|\()(ftp\.[^\s<>]+)(?=\s|$)!i","<a href=\"ftp://$1\">$1</a>",$txt);

        return $txt;
    }

    $txt = lcml_urls($txt);
?>
