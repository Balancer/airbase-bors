<?
    require_once("funcs/DataBase.php");
    require_once("funcs/DataBaseHTS.php");

    class CacheStaticFile
    {
		var $file;
		var $uri;
		var $page;
		var $original_uri;
	
        function CacheStaticFile($uri=NULL, $page=1)
        {
			$this->set_name($uri, $page);
        }

/*        function get_static_uri($uri)
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
*/		
		function set_name($uri, $page=1)
		{
//			echo "Set name '$uri'";
			$this->uri  = $uri;
			$this->page  = $page;
			$this->original_uri  = $uri;
			if(!empty($GLOBALS['bors']) && $GLOBALS['bors']->config()->cache_uri())
				$this->original_uri = $GLOBALS['bors']->config()->cache_uri();
			
			$this->file = $_SERVER['DOCUMENT_ROOT'].preg_replace('!http://[^/]+!', '', $uri);
			
			if(preg_match("!/[^\.]+$!", $uri))
				$uri .= "/";
			
			if($uri{strlen($uri)-1}=='/')
			{

				if($this->page > 1)
					$title = "index-$page.html";
				else
					$title = "index.html";

				$this->file .= $title;
				$this->uri  .= $title;
			}
//			echo "'$uri'";
		}

		function save(&$content)
		{
//			echo "save file '{$this->file}'";
			require_once("funcs/filesystem_ext.php");
			mkpath(dirname($this->file));
			
			if(!$fh = fopen($this->file, 'a+'))
				die("Can't open write $file");
			if(!flock($fh, LOCK_EX))
				die("Can't lock write $file");
			if(!ftruncate($fh, 0))
				die("Can't truncate write $file");

			fwrite($fh, $content);
			fclose($fh);
			
			@chmod($this->file, 0664);
			
            $db = &new DataBase($GLOBALS['cms']['mysql_cache_database']);
			
			$db->store('cached_files', "file = '".addslashes($this->file)."'", array(
					'file'			=> $this->file,
					'uri'			=> $this->uri,
					'original_uri'	=> $this->original_uri,
					'last_compile'	=> time(),
			));
			
			return $content;
		}
		
		function get_name($uri)
		{
            $db = &new DataBase($GLOBALS['cms']['mysql_cache_database']);
			
			return $db->get("SELECT uri FROM cached_files WHERE original_uri = '".addslashes($uri)."' ORDER BY last_compile DESC LIMIT 1");
		}

		function get_file($uri)
		{
            $db = &new DataBase($GLOBALS['cms']['mysql_cache_database']);
			
			return $db->get("SELECT file FROM cached_files WHERE original_uri = '".addslashes($uri)."' ORDER BY last_compile DESC LIMIT 1");
		}
		
		function clean($original_uri)
		{
            $db = &new DataBase($GLOBALS['cms']['mysql_cache_database']);
			
			$files = $db->get_array("SELECT file FROM cached_files WHERE original_uri = '".addslashes($original_uri)."'");
			$db->query("DELETE FROM cached_files WHERE original_uri = '".addslashes($original_uri)."'");
			foreach($files as $file)
			{
				@unlink($file);
				@rmdir(dirname($file));
			}
			
		}
    }
