<?
require_once("BorsBasePage.php");

class BorsBaseDbPage extends BorsBasePage
{
		var $db;

		function parents()
		{
//			echo "match=".print_r($this->match, true)."<br />\n";
			return array("http://{$this->match[1]}{$this->match[2]}");
		}
	
		function __construct($id, $page = 1)
		{
			if(method_exists($this, 'main_db_storage'))
				$this->db = &new DataBase($this->main_db_storage());
			else
				$this->db = &new DataBase($GLOBALS['cms']['mysql_database']);
			
			parent::__construct($id, $page);

			if(!($qlist = $this->_global_queries()))
				return;

			$save_cached = $GLOBALS['cms']['cache_disabled'];
			$GLOBALS['cms']['cache_disabled'] = false;
				
			foreach($qlist as $qname => $q)
			{
				if(isset($GLOBALS['cms']['templates']['data'][$qname]))
					continue;
			
				$cache = NULL;
				if(preg_match("!^(.+)\|(\d+)$!s", $q, $m))
				{
					$q		= $m[1];
					$cache	= $m[2];
				}
					
				$GLOBALS['cms']['templates']['data'][$qname] = $this->db->get_array($q, false, $cache);
			}
			
			$GLOBALS['cms']['cache_disabled'] = $save_cached;
		}

	function storage_engine() { return 'storage_db_mysql'; }
}
