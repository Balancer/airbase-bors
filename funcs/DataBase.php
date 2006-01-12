<?
//	if($_SERVER['REMOTE_ADDR']!='83.237.205.36')
//		exit("Temporary downed (~ to 14:30)");

    require_once("debug.php");
    require_once("funcs/global-data.php");
    require_once("funcs/texts.php");

    $global_db_new_connections=0;
    $global_db_resume_connections=0;
    $global_db_queries=0;

    class DataBase
    {
        var $dbh;
        var $result;
        var $row;
        var $db_name;

        function DataBase($base=NULL, $login=NULL, $password=NULL, $server=NULL) // DataBase
        {
            if(empty($base))
            	$base = $GLOBALS['cms']['mysql_database'];
            
            $this->db_name = $base;
            if(is_global_key("DataBaseHandler",$base))
            {
                if(!isset($GLOBALS['global_db_resume_connections']))
                    $GLOBALS['global_db_resume_connections'] = 0;
                $GLOBALS['global_db_resume_connections']++;
                $this->dbh = global_key("DataBaseHandler",$base);
//                echo "cont\[{$base}]=".$this->dbh."<br>\n";
                mysql_select_db($base,$this->dbh) or die(__FILE__.':'.__LINE__." Could not select database '$base' (".mysql_errno($this->dbh)."): ".mysql_error($this->dbh)."<BR />");
                mysql_query("SET CHARACTER SET {$GLOBALS['cms']['charset_u']};",$this->dbh)
					 or die(__FILE__.':'.__LINE__." Could not select database '$base' (".mysql_errno($this->dbh)."): ".mysql_error($this->dbh)."<BR />");
                mysql_query("SET NAMES {$GLOBALS['cms']['charset_u']};",$this->dbh)
					 or die(__FILE__.':'.__LINE__." Could not select database '$base' (".mysql_errno($this->dbh)."): ".mysql_error($this->dbh)."<BR />");
            }
            else
            {
                if(empty($GLOBALS['global_db_new_connections']))
                    $GLOBALS['global_db_new_connections'] = 0;
                $GLOBALS['global_db_new_connections']++;

                if(empty($login))    $login    = $GLOBALS['cms']['mysql_login'];
                if(empty($password)) $password = $GLOBALS['cms']['mysql_pw'];
                if(empty($server))   $server   = $GLOBALS['cms']['mysql_server'];

                $this->dbh = 0;
                $nnn = 0;
                while(!$this->dbh && $nnn<10)
                {
                    $this->dbh = @mysql_connect($server, $login, $password);
//                    echo "NNew\[{$base}]".$this->dbh."<br>\n";
                    if(!$this->dbh)
                        sleep(5);
                    $nnn++;
                }
                if(!$this->dbh)
                    die (__FILE__.':'.__LINE__." Query failed, error ".mysql_errno($this->dbh).": ".mysql_error($this->dbh)."<BR />");
                mysql_select_db($base,$this->dbh) or die(__FILE__.':'.__LINE__." Could not select database '$base' (".mysql_errno($this->dbh)."): ".mysql_error($this->dbh)."<BR />");

//                mysql_query ("SET CHARACTER SET koi8-r",$this->dbh);
                
                set_global_key("DataBaseHandler",$base,$this->dbh);
//                echo "new\[{$base}]=".$this->dbh."<br>\n";
            }
        }

        function query($query, $ignore_error=false)
        {
            mysql_select_db($this->db_name, $this->dbh);
            
            if(empty($GLOBALS['global_db_queries']))
                $GLOBALS['global_db_queries'] = 0;
            $GLOBALS['global_db_queries']++;

            echolog("<xmp>query='$query'</xmp>",4);

            list($usec, $sec) = explode(" ",microtime());
            $qstart = ((float)$usec + (float)$sec);
           
            $this->result = !empty($query) ? @mysql_query($query,$this->dbh) : false;

            list($usec, $sec) = explode(" ",microtime());
            $qtime = ((float)$usec + (float)$sec) - $qstart;

            if(0)
            {
                $fh = fopen('/var/www/localhost/htdocs/hts-queries.log','at');
                fputs($fh,"$query\n");
                fclose($fh);
            }

            //   @mysql_num_rows(), ..    SELECT!
            if($this->result)
                if(preg_match("!^SELECT!", $query))
                    return $this->rows = mysql_num_rows($this->result); 
                else
                    return $this->result;

            if(!$ignore_error)
                echolog("Invalid query '<tt>$query</tt>': " . mysql_error($this->dbh),1);

            return false;
        }

        function free()
        {
            @mysql_free_result($this->result);
        }

        function fetch()
        {
            $this->row = $this->result ? mysql_fetch_assoc($this->result) : false;

//  			echolog("fetch:<xmp>".print_r($this->row,true)."</xmp>");

            if(is_array($this->row) && sizeof($this->row)==1)
                foreach($this->row as $res)
                    $this->row = quote_fix($res);

            return $this->row;
        }

        function get($query, $ignore_error=true)
        {
//            if(is_global_key("db_get",$query)) 
//                return global_key("db_get",$query);

            $this->query($query, $ignore_error);
            $this->fetch();
            $this->free();

            return set_global_key("db_get",$query, $this->row);
        }

        function loop($func,$query)
        {
            $this->query($query);
            
            while($this->fetch()!==false)
                $func($this->row);

            $this->free();
        }

        function get_array($query,$ignore_error=true)
        {
//			echo "==<pre>$query</pre>==";
            $res=array();
            $this->query($query,$ignore_error);
            
            while($this->fetch()!==false)
                $res[]=$this->row;

            $this->free();

            return $res;
        }

        function make_string_values($array)
        {
            $keys="";
            $values="";
            $first=1;
            foreach($array as $k => $v)
            {
                if($first)
                {
                    $first=0;
                }
                else
                {
                    $keys.=",";
                    $values.=",";
                }

                $keys.="`$k`";
                $values.="'".addslashes($v)."'"; // mysql_real_escape_string
            }
            return " ($keys) VALUES ($values) ";
        }

        function make_string_set($array)
        {
            $first=1;
            $set='';

            foreach($array as $k => $v)
            {
                if($first)
                    $first=0;
                else
                    $set.=",";
                $set.="`$k`='".addslashes($v)."'"; // mysql_real_escape_string
            }
            return " SET $set ";
        }

        function insert($table,$fields)
        {
            $this->query("INSERT INTO $table ".$this->make_string_values($fields));
        }

        function store($table, $where, $fields, $append=false)
        {
			if(!$append)
	            $n = $this->query("SELECT * FROM `".addslashes($table)."` WHERE $where");

            if(!$append && $n>0)
                $res = $this->query("UPDATE `".addslashes($table)."` ".$this->make_string_set($fields)." WHERE $where");
            else
                $res = $this->query("REPLACE INTO `".addslashes($table)."` ".$this->make_string_values($fields));

            if($res === false)
            {
#                mysql_query ("REPAIR TABLE `$table`");
                echo("Invalid query: " . mysql_error($this->dbh) ." ");
                die(__FILE__.":".__LINE__." Error and try autorepair ('$table','$where','$fields').");
            }
        }

        function store_array($table,$where,$fields_array)
        {
            $n=$this->query("SELECT * FROM `".addslashes($table)."` WHERE $where LIMIT 1");

            if($n>0)
            {
                $q="DELETE FROM `".addslashes($table)."` WHERE $where";
                $this->query($q);
            }

            foreach($fields_array as $fields)
            {
                $q="INSERT INTO `".addslashes($table)."` ".$this->make_string_values($fields);
                $res=$this->query($q);
                if($res === false)
                    die(__FILE__.':'.__LINE__." Invalid query '$q': " . mysql_error($this->dbh));
            }
        }

        function get_value($table,$key_search,$value,$key_res)
        {
            if(is_global_key("get_value($table,$key_search,$value)",$key_res)) 
                return global_key("get_value($table,$key_search,$value)",$key_res);

            return set_global_key("get_value($table,$key_search,$value)",$key_res, 
                $this->get("SELECT `".addslashes($key_res)."` FROM `".addslashes($table)."` WHERE `".addslashes($key_search)."`='".addslashes($value)."'"));
        }

        function get_last_id()
        {
            return mysql_insert_id($this->dbh);
        }

        function get_field_list()
        {
            return $this->get_array("SELECT * FROM `hts_keys`");
        }
    }
?>
