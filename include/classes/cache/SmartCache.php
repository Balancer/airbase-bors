<?
    require_once("funcs/DataBase.php");
    require_once("BaseCache.php");

    class Cache extends BaseCache
    {
        var $dbh;
		var $create_time;
		var $expire_time;
        
        function Cache()
        {
            $this->dbh = &new DataBase($GLOBALS['cms']['mysql_cache_database']);
        }

        function get($type, $key, $uri='', $default=NULL)
        {
			$this->init($type, $key, $uri);
		
			if($GLOBALS['cms']['cache_disabled'])
           		return ($this->last = $default);

			$memcache = &new Memcache;
			$memcache->connect('localhost') or debug_exit("Could not connect memcache");

			@$GLOBALS['bors_stat_smart_cache_gets_total']++;
			if($x = @$memcache->get('phpmv3'.$this->last_hmd))
			{
				@$GLOBALS['bors_stat_smart_cache_gets_memcached_hits']++;
				return $this->last = $x;
			}
			
            $row = $this->dbh->get("SELECT `value`, `expire_time`, `count`, `saved_time`, `create_time` FROM `cache` WHERE `hmd`={$this->last_hmd}");
			$this->last = unserialize($row['value']);

			$now = $GLOBALS['now'];

			if($row['expire_time'] <= $now)
			{
				$this->last = NULL;
#	            $this->dbh->query("DELETE FROM `cache` WHERE `hmd`={$this->last_hmd}");
			}
			else
			{
				$this->create_time = $row['create_time'];
				$this->expire_time = $row['expire_time'];
			}

			$new_count = intval($row['count']) + 1;
			$rate = $row['saved_time'] * $new_count / (max($now - $row['create_time'], 1));

			if($this->last)
			{
				@$GLOBALS['bors_stat_smart_cache_gets_db_hits']++;
				$this->dbh->update('cache', "`hmd`={$this->last_hmd}", array (
					'int access_time' => $now, 
					'int count' => $new_count,
					'float rate' => $rate,
				));
			}	
			
            return ($this->last ? $this->last : $default);
        }

        function set($value, $time_to_expire = 86400, $infinite = false)
        {
//			echolog("Set cache {$this->last_type_name}", 1);
			list($usec, $sec) = explode(" ",microtime());
            $this->dbh->replace('cache', array(
				'int hmd'	=> $this->last_hmd,
				'int type'	=> $this->last_type,
				'int key'	=> $this->last_key,
				'int uri'	=> $this->last_uri,
				'value'	=> serialize($value),
				'int access_time' => 0,
				'int create_time' => $infinite ? -1 : time(),
				'int expire_time' => time() + intval($time_to_expire),
				'int count' => 1,
				'float saved_time' => (float)$usec + (float)$sec - $this->start_time,
				'float rate' => 0,
			));

			$memcache = &new Memcache;
			$memcache->connect('localhost') or debug_exit("Could not connect memcache");
			$memcache->set('phpmv3'.$this->last_hmd, $value, true, $time_to_expire);

            return $this->last = $value;
        }

        function clear_by_id($key)
        {
//			$key = "0x".substr(md5($key), -16);
			$key = "0x".md5($key);
			$this->dbh->query("DELETE FROM `cache` WHERE `key` = $key");
        }

        function clear_by_cache_id($hmd)
        {
			$this->dbh->query("DELETE FROM `cache` WHERE `hmd` = $hmd");
        }

        function clear_by_uri($uri)
        {
//			$uri = "0x".substr(md5($uri), -16);
			$uri = "0x".md5($uri);
			$this->dbh->query("DELETE FROM `cache` WHERE `uri` = $uri");
        }

        function clear_by_type($type)
        {
//			$type = "0x".substr(md5($type), -16);
			$type = "0x".md5($type);
			$this->dbh->query("DELETE FROM `cache` WHERE `type` = $type");
        }

        function get_array_by_uri($uri)
        {
//			$uri = "0x".substr(md5($uri), -16);
			$uri = "0x".md5($uri);
			return $this->dbh->get_array("SELECT DISTINCT value FROM `cache` WHERE `uri` = $uri");
        }

        function get_array_by_type($type)
        {
//			$type = "0x".substr(md5($type), -16);
			$type = "0x".md5($type);
			return $this->dbh->get_array("SELECT DISTINCT value FROM `cache` WHERE `type` = $type");
        }
}
