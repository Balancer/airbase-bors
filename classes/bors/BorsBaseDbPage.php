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
	
		function cacheable_body()
		{
			$data = array();
		
			if($qlist = $this->_queries())
				foreach($qlist as $qname => $q)
				{
					$cache = NULL;
					if(preg_match("!^(.+)\|(\d+)$!", $q, $m))
					{
						$q		= $m[1];
						$cache	= $m[2];
					}
					
					if(preg_match("!^GLOBAL:(.+)$!", $qname, $m))
						$GLOBALS['cms']['templates']['data'][$m[1]] = $this->db->get_array($q, false, $cache);
					else
						$data[$qname] = $this->db->get_array($q, false, $cache);
				}

			$data['template_dir'] = $this->_body_template_dir();

			return template_assign_data($this->_body_template(), $data);
		}
		
		function _body_template()
		{
			$cf = $this->_class_file();
			if($cf)
				return preg_replace("!^(.+)\.php$!", "xfile:$1.html", $cf);
			else
				return 'main.html';
		}

		function _body_template_dir()
		{
			$cf = $this->_class_file();
			return dirname($cf);
		}
		
		function _queries() { return array(); }
	}
