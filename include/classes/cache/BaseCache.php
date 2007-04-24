<?
    class BaseCache
    {
        var $last;
		var $last_type;
		var $last_key;
		var $last_uri;
		var $last_hmd;
		var $start_time;

		function init($type, $key, $uri = '')
		{
			if(!$uri)
				$uri = $key;
		
			$this->last_type = $type = "0x".substr(md5($type), -16);
			$this->last_key  = $key  = "0x".substr(md5($key), -16);
			$this->last_uri  = $uri  = "0x".substr(md5($uri), -16);
            $this->last_hmd  = $hmd  = "0x".substr(md5("$type:$key"), -16);

			list($usec, $sec) = explode(" ",microtime());
			$this->start_time = (float)$usec + (float)$sec;
		}

		function group_register($group, $obj = NULL)
		{
//			echo "Register {$obj->internal_uri()} to $group";
		
			$ch = &new Cache();
			$ch->init("cache_group-$group", $this->last_cache_id());
			$ch->set($this->last_cache_id(), 86400*365.25, true);
			
			
			if($obj)
			{
				$ch->init("cache_group_obj-$group", $obj->internal_uri());
				$ch->set($obj->internal_uri(), 86400*365.25, true);
			}
		}

		function group_clean($group)
		{
			require_once("classes/objects/Bors.php");
			require_once("handlers/pre/borspage.php");
		
			$ch = &new Cache();
			foreach($ch->get_array_by_type("cache_group_obj-$group") as $uri)
			{
				echo "$uri<br />";
				$cls = class_load($uri);
				if(method_exists($cls, cache_clean_self))
					$cls->cache_clean_self();
				else
					echo "Can't load $uri<br />";
			}

			foreach($ch->get_array_by_type("cache_group-$group") as $cache_id)
				$ch->clear_by_cache_id($cache_id);
				
			$this->clear_by_type("cache_group_obj-$group");
		}

        function last()
        {
            return $this->last;
        }

        function last_cache_id()
        {
            return $this->last_hmd;
        }

		function instance()
		{
			return new Cache();
		}
    }
