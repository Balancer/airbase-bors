<?
    function abs_path_from_relative($uri, $page)
    {
        if(preg_match("!^\w+://!", $uri))
            return $uri;
        
        if(preg_match("!^/!", $uri))
            return 'http://'.$_SERVER['HTTP_HOST'].$uri;

        return "$page$uri";
    }

    function mkpath($strPath, $mode=0777)
    {
        if(is_dir($strPath)) 
            return true;
  
        $pStrPath = dirname($strPath);

        if(!mkpath($pStrPath, $mode)) 
            return false;

        return mkdir($strPath, $mode);
    }
?>