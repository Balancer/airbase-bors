<?
    function ext_load($dir,$txt=NULL)
    {
//        echo("load $dir from {$GLOBALS['PHP_SELF']}<br>\n");
        if(is_dir($dir))
        {
            if($dh = opendir($dir)) 
            {
                while(($file = readdir($dh)) !== false) 
                {
//                    echo "$file<br>";
                    if(substr($file,-4)=='.php')
                    {
                        include_once("$dir/$file");
                    }
                    elseif(filetype("$dir/$file")=='dir' && substr($file,0,1)!='.')
                    {
                        ext_load("$dir/$file");
                    }
                }
                closedir($dh);
            }
        }
        return $txt;
    }
?>
