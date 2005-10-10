<?
    error_reporting(E_ALL);
    require_once("DataBase.php");

    class Cache
    {
        var $dbh;
        var $last;
        
        function Cache()
        {
            $this->dbh = new DataBase('CACHE');
//            echo "Created cache".$this->dbh->dbh."<br>\n";
        }

        function get($type, $key, $default=NULL)
        {
//            return $this->last = $default;

//            $GLOBALS['log_level']=2;
            $hmd = md5("$type:$key");
			
            $this->last = $this->dbh->get("SELECT `value` FROM `cache` WHERE `hmd`='$hmd'");

//            echo "Get from cache $type:$key = $this->last<br>";

            if($this->last)
                $this->dbh->query("UPDATE `cache` SET `access_time` = ".time()." WHERE `hmd`='$hmd'");

            return ($this->last ? $this->last : $default);
        }

        function set($type, $key, $value)
        {
//        	return $this->last = $value;
//            $GLOBALS['log_level']=4;
            $hmd = md5("$type:$key");
//            $value='';
//            echo "Set cache $type:$key = <xmp>'$value'</xmp><br>\n";
            $this->dbh->query("REPLACE `cache` (`type`,`key`,`hmd`,`value`,`access_time`,`create_time`) VALUES ('".addslashes($type)."','".addslashes($key)."','$hmd','".addslashes($value)."',".time().",".time().") ");
            return $this->last = $value;
        }

        function last()
        {
            return $this->last;
        }

        function clear_check($type, $time)
        {
//            $this->dbh->query("DELETE FROM `cache` WHERE `type`='$type' AND `create_time` < ".(time()-$time));
        }

        function clear($key)
        {
        	return;
            $this->dbh->query("DELETE FROM `cache` WHERE `key`='".addslashes($key)."'");
        }

        function clear_all()
        {
        	return;
            $this->dbh->query("DELETE FROM `cache` WHERE 1=1");
        }
    }
?>
