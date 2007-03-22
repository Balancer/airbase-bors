<?
    require_once("funcs/DataBase.php");

    class Cache
    {
        var $dbh;
        var $last;
		var $last_type;
		var $last_key;
		var $last_uri;
		var $last_hmd;
		var $start_time;
        
        function Cache()
        {
            $this->dbh = &new DataBase($GLOBALS['cms']['mysql_cache_database']);
        }

        function get($type, $key, $uri='', $default=NULL)
        {
			$this->last_type = $type = "0x".substr(md5($type), -16);
			$this->last_key  = $key  = "0x".substr(md5($key), -16);
			$this->last_uri  = $uri  = "0x".substr(md5($uri), -16);
            $this->last_hmd  = $hmd  = "0x".substr(md5("$type:$key"), -16);

			list($usec, $sec) = explode(" ",microtime());
			$this->start_time = (float)$usec + (float)$sec;
		
			if($GLOBALS['cms']['cache_disabled'])
           		return ($this->last = $default);

            $row = $this->dbh->get("SELECT `value`, `expire_time`, `count`, `saved_time`, `create_time` FROM `cache` WHERE `hmd`=$hmd");
			$this->last = $row['value'];

			$now = time();

			if($row['expire_time'] <= $now)
			{
				$this->last = NULL;
	            $this->dbh->query("DELETE FROM `cache` WHERE `hmd`=$hmd");
			}

			$new_count = intval($row['count']) + 1;
			$rate = $row['saved_time'] * $new_count / ($now - $row['create_time'] + 1);

			if($this->last)
				$this->dbh->update('cache', "`hmd`=$hmd", array (
					'int access_time' => $now, 
					'int count' => $new_count,
					'float rate' => $rate,
				));
				
			
            return ($this->last ? $this->last : $default);
        }

        function set($value, $time_to_expire = 86400)
        {
			list($usec, $sec) = explode(" ",microtime());
            $this->dbh->replace('cache', array(
				'int hmd'	=> $this->last_hmd,
				'int type'	=> $this->last_type,
				'int key'	=> $this->last_key,
				'int uri'	=> $this->last_uri,
				'value'	=> $value,
				'int access_time' => 0,
				'int create_time' => time(),
				'int expire_time' => time() + intval($time_to_expire),
				'int count' => 1,
				'float saved_time' => (float)$usec + (float)$sec - $this->start_time,
				'float rate' => 0,
			));

            return $this->last = $value;
        }

        function last()
        {
            return $this->last;
        }

        function clear_by_id($key)
        {
			$key = "0x".substr(md5($key), -16);
			$this->dbh->query("DELETE FROM `cache` WHERE `key` = $key");
        }

        function clear_by_uri($uri)
        {
			$uri = "0x".substr(md5($uri), -16);
			$this->dbh->query("DELETE FROM `cache` WHERE `uri` = $uri");
        }

        function clear_by_type($type)
        {
			$type = "0x".substr(md5($type), -16);
			$this->dbh->query("DELETE FROM `cache` WHERE `type` = $type");
        }

        function get_array_by_uri($uri)
        {
			$uri = "0x".substr(md5($uri), -16);
			return $this->dbh->get_array("SELECT DISTINCT value FROM `cache` WHERE `uri` = $uri");
        }
    }
