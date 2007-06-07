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
	
		function BorsBaseDbPage($id)
		{
			$this->db = &new DataBase($GLOBALS['cms']['mysql_database']);
			
			parent::BorsBasePage($id);

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
