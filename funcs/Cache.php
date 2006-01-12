<?
    error_reporting(E_ALL);
    require_once("DataBase.php");

    class Cache
    {
        var $dbh;
        var $last;
        
        function Cache()
        {
            $this->dbh = new DataBase($GLOBALS['cms']['mysql_cache_database']);
        }

        function get($type, $key, $default=NULL)
        {
			if($GLOBALS['cms']['cache_disabled'])
           		return $this->last = $default;

            $hmd = md5("$type:$key");
			
//            $GLOBALS['log_level']=4;
            $this->last = $this->dbh->get("SELECT `value` FROM `cache` WHERE `hmd`='$hmd'");
//            $GLOBALS['log_level']=2;

//            echo "Get from cache $type:$key = $this->last<br>";

            if($this->last)
                $this->dbh->query("UPDATE `cache` SET `access_time` = ".time()." WHERE `hmd`='$hmd'");

            return ($this->last ? $this->last : $default);
        }

        function set($type, $key, $value, $time_to_expire = 604800)
        {
//        	return $this->last = $value;
//            $GLOBALS['log_level']=4;
            $hmd = md5("$type:$key");
//            echo "Set cache $type:$key = <xmp>'$value'</xmp><br>\n";
            $this->dbh->query("REPLACE `cache` (`type`,`key`,`hmd`,`value`,`access_time`,`create_time`,`expire_time`) VALUES ('".addslashes($type)."','".addslashes($key)."','$hmd','".addslashes($value)."',".time().",".time().",".(time()+$time_to_expire).") ");
            return $this->last = $value;
        }

        function last()
        {
            return $this->last;
        }

        function clear_check($type, $time)
        {
            $this->dbh->query("DELETE FROM `cache` WHERE `type`='$type' AND `create_time` < ".(time()-$time));
        }

        function clear($key)
        {
//        	return;
            $this->dbh->query("DELETE FROM `cache` WHERE `key` LIKE '".addslashes($key)."'");
        }

        function clear_all()
        {
        	return;
            $this->dbh->query("DELETE FROM `cache` WHERE 1=1");
        }
    }
?>
