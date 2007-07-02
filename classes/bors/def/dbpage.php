<?
	class_include('def_page');

	class def_dbpage extends def_page
	{
		var $db;

		function parents()
		{
//			echo "match=".print_r($this->match, true)."<br />\n";
			return array("http://{$this->match[1]}{$this->match[2]}");
		}

		function main_db_storage() { return $GLOBALS['cms']['mysql_database']; }
	
		function def_dbpage($id)
		{
			$this->db = &new DataBase($this->main_db_storage());
			
			parent::def_page($id);

			if(!($qlist = $this->_global_queries()))
				return;
				
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
		}
	}
