<?
	function scandir($dir, $listDirectories=false, $skipDots=true) 
	{
		$dirArray = array();
		if ($handle = opendir($dir)) 
		{
       		while (false !== ($file = readdir($handle))) 
			{
           		if (($file != "." && $file != "..") || $skipDots == true) 
				{
               		if($listDirectories == false)
						if(is_dir($file))
							continue;
               		
					array_push($dirArray, basename($file));
           		}
       		}
       		closedir($handle);
   		}
   		return $dirArray;
	}
