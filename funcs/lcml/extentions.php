<?
    function ext_load($dir,$txt=NULL)
    {
//        echo "ext load dir $dir<br>\n";
//		return "Load dir: '$dir'; $txt";
        
        if(!is_dir($dir)) return $txt;
        
        $files = array();

        if($dh = opendir($dir)) 
        {
            while(($file = readdir($dh)) !== false)
                if(substr($file,0,1)!='.')
                    array_push($files, $file);
        }
        closedir($dh);
        
        sort($files);

        foreach($files as $file) 
        {
//            echo "load $file<br>\n";

            if(substr($file,-4)=='.php')
            {
                $time_start = time();
                include_once("$dir/$file");

                $fn = "lcml_".substr($file,3,-4);
                
                if(function_exists($fn))
                    $txt = $fn($txt);

                if($time_start<time())
                {
                    $fh=@fopen("{$_SERVER['DOCUMENT_ROOT']}/logs/lcml-profiling.log","at");
                    @fwrite($fh,strftime("%Y-%m-%d %H:%M:%S")."|$dir/$file|");
                    @fwrite($fh,(time()-$time_start)."\n");
                    @fclose($fh);
                }
            }
//            else
//                ext_load("$dir/$file");
        }

        return $txt;
    }
