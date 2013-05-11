<?php

class storage_fs_hts extends base_null
{
	private $hts;
	private $obj;
	function ext($key, $new_name = NULL)
	{
    	if(!$new_name)
        	$new_name = $key;
			
		if(preg_match("!^#$key +(.*?)$!m", $this->hts, $m))
			$this->hts = preg_replace("!(^|\n)#$key +.*?(\n|$)!", '$1$2', $this->hts);
		elseif(preg_match("!^#$key()$!m", $this->hts, $m))
			$this->hts = preg_replace("!(^|\n)#$key(\n|$)!", '$1$2', $this->hts);
		else
			return;

//		echo "Extracted for ($key,$new_name) = '{$m[1]}'<br>";

		if($new_name == '-')
			return $m[1];
		else
			return $this->obj->set($new_name, $m[1], false);
	}

	function load($object)
	{
		$dir = $object->dir();
		
		if(!file_exists($file = "{$dir}/index.hts"))
			return $object->set_is_loaded(false);

		// По дефолту в index.hts разрешёны HTML и все BB-теги.
		$object->set_html_disable(false, false);
		$object->set_lcml_tags_enabled(NULL, false);
		
		if(!($hts = @file_get_contents($file)))
			return $object->set_is_loaded(false);

		$hts = str_replace("\r", "", $hts);

		$hts = iconv('WINDOWS-1251','UTF-8', $hts);

		$this->obj = &$object;

		$old = false;
		$this->hts = $hts;

    	@list($title, $h1, $h2, $h3) = explode('|', $this->ext('head', '-'));

    	$this->ext('copyr','copyright');
    	$this->ext('type');
    	$this->ext('maked','create_time');
    	$this->ext('style');
    	$this->ext('template');
    	$this->ext('color');
    	$this->ext('logdir');
    	$this->ext('cr_type');
    	$this->ext('split_type');

    	$this->ext('flags');

    	$this->hts = preg_replace("!#begin\s*\n!","",$this->hts);
    	$this->hts = preg_replace("!\n#end\s*!","",$this->hts);

    	$this->ext('long');
    	$this->ext('short');
    	$this->ext('start');
    	$this->ext('file');
    	$this->ext('forum_id');

    	$hts = explode("\n", $this->hts);
		$parents = array();
		$nav_open = false;
		$last = NULL;

	    for($i=0; $i<sizeof($hts); $i++)
    	{
//			echo "<tt>$i:[".htmlspecialchars($hts[$i])."]</tt><br />";
	        if(preg_match("!^#nav!",$hts[$i]))
			{
	            $nav_open = true;
				$hts[$i] = '';
			}
			elseif($nav_open && preg_match("!^#!",$hts[$i]))
			{
    	        $nav_open = false;
				$hts[$i] = '';
				$parents[$url] = $last;
			}
			elseif($nav_open)
        	{
				list($url, $nav_title) = explode(',', $hts[$i]);
				$hts[$i] = '';
				$last = $url;
	        }
    	}

	    if(!$object->type())
    	{
        	$type='hts';

	        $nav_sum="";

    	    for($i=0;$i<sizeof($hts);$i++)
        	{
            	if(preg_match("!^#lev\s+(.+)!",$hts[$i],$data))
	            {
    	            $nav_sum.="$data[1]\n";
        	        $hts[$i]="";
            	}
	        }

    	    if($nav_sum)
        	{
	            $navs++;
    	        $nav="nav$navs";
        	    $$nav=$nav_sum;
            	$old=1;
        	}
    	}

	    $hts = join("\n", $hts);

	    if($old)
    	{
        	$hts = preg_replace("!\n\n#p\s+!","\n#p\n",$hts);
	        $hts = preg_replace("!\n\n#p(\n|$)!","\n#p$1",$hts);
    	    $hts = preg_replace("!\n+#p(\n|$)!","\n$1",$hts);
	        $hts = preg_replace("!\n#t\s+!","\n\n",$hts);
    	    $hts = preg_replace("!\|(.+?)\|!","$1",$hts);
	        $copyr =preg_replace("!\|(.+?)\|!","$1",$copyr);
    	}

	    $hts = preg_replace("!^\n+!","",$hts);
    	$object->set_source(preg_replace("!\n+$!","",$hts), false);

	    if(!$title) $title="$h1, $h3, $h2";
    	if(!$h1)
			$h1 = $title;
		
		$object->set_title($title, false);
		$object->set_nav_name($h1, false);
		$object->set_parents(array_keys($parents), false);
		
//		print_d($object->source());
		
		return $object->set_is_loaded(true);
	}
	
	function save($object)
	{
		debug_exit("Try to save index.hts");
	}
}
