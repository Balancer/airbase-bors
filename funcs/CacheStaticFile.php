<?
    error_reporting(E_ALL);
    require_once("DataBaseHTS.php");

    class CacheStaticFile
    {
		$hts;
	
        function CacheStaticFile()
        {
			$hts = new DataBaseHTS();
        }

        function get_static_uri($uri)
        {
			$mtime = $hts->get($uri, 'modify_time');
			$md    = md5("$uri"."$mtime");
			$path  = $md{0}."/".$md{1}."/".$md{2}."/".$md{3}."/".$md{4}."/".$md;
			mkdirs("{$GLOBALS['cms']['cache_dir']}/$path");
			
		}
    }
?>
