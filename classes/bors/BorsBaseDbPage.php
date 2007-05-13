<?
	require_once("BorsBasePage.php");

	class BorsBaseDbPage extends BorsBasePage
	{
		var $db;

		function parents()
		{
//			print_r("http://{$this->match[1]}{$this->match[2]}");

			$uri = "http://{$this->match[1]}{$this->match[2]}";

			if(borsclass_uri_load($uri))
				return array(array('borspage', $uri));
			else
				return array(array('page', $uri));
		}
	
		function BorsBaseDbPage($id, $match = false)
		{
			parent::BorsBasePage($id, $match);

			$this->db = &new DataBase($GLOBALS['cms']['mysql_database']);

			if(!($qlist = $this->_global_queries()))
				return;
				
			foreach($qlist as $qname => $q)
			{
				if(isset($GLOBALS['cms']['templates']['data'][$qname]))
					continue;
			
				$cache = NULL;
				if(preg_match("!^(.+)\|(\d+)$!", $q, $m))
				{
					$q		= $m[1];
					$cache	= $m[2];
				}
					
				$GLOBALS['cms']['templates']['data'][$qname] = $this->db->get_array($q, false, $cache);
			}
		}
	}
