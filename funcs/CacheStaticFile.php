<?
    error_reporting(E_ALL);
    require_once("DataBaseHTS.php");

    class CacheStaticFile
    {
		private $hts;
	
        function CacheStaticFile()
        {
			$hts = new DataBaseHTS();
        }

        function get_static_uri($uri)
        {
			$mtime = $hts->get($uri, 'modify_time');
			$title = $hts->get($uri, 'title');
			$md    = md5("$uri"."$mtime");
			$path  = strftime("/%Y/%m/%d/%H/%M/%S/");
			$file = "";//translit_uri($title).substr($md,4).".htm");
			mkdirs($path);
			return $path."/".$file;
		}

        function get_static_uri($uri)
        {
			$mtime = $hts->get($uri, 'modify_time');
			$title = $hts->get($uri, 'title');
			$md    = md5("$uri"."$mtime");
			$path  = strftime("/%Y/%m/%d/%H/%M/%S/");
			$file = "";//translit_uri($title).substr($md,4).".htm");
			mkdirs($path);
			return $path."/".$file;
		}
    }
?>
