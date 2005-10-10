<?
	include_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config.php");

    function smarty_caches_clear()
	{
    	smarty_caches_clear_dir("{$GLOBALS['cms']['cache_dir']}/smarty-cache");
    	smarty_caches_clear_dir("{$GLOBALS['cms']['cache_dir']}/smarty-templates_c");
	}
	
    function smarty_caches_clear_dir($dir)
    {
//		echo $dir;
        if(!is_dir($dir)) 
        	return;
        
        if($dh = opendir($dir)) 
        {
            while(($file = readdir($dh)) !== false)
                if(substr($file,-4)=='.php')
                	unlink("$dir/$file");
        }
        closedir($dh);
    }
//	smarty_caches_clear();
?>
