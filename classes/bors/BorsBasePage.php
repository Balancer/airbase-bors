<?
	require_once("borsPage.php");

	class BorsBasePage extends borsPage
	{
		function internal_uri() 
		{
			if(preg_match('!^http://!', $this->id()))
				return $this->id(); 
			else
				return get_class($this)."://".$this->id();
		}

		function cacheable_body()
		{
			$data = array();
		
			if($qlist = $this->_queries())
				foreach($qlist as $qname => $q)
				{
					$cache = false;
					if(preg_match("!^(.+)\|(\d+)$!s", $q, $m))
					{
						$q		= $m[1];
						$cache	= $m[2];
					}

					if(preg_match("/!(.+)$/s", $q, $m))
						$data[$qname] = $this->db->get($m[1], false, $cache);
					else
						$data[$qname] = $this->db->get_array($q, false, $cache);
				}

			$data['template_dir'] = $this->_class_dir();
			$data['this'] = $this;

			require_once('engines/smarty/assign.php');
			return template_assign_data($this->_body_template(), $data);
		}

		function _class_dir()
		{
			if(!method_exists($this, '_class_file'))
				return NULL;

			return dirname($this->_class_file());
		}

		function _body_template()
		{
			$cf = false;
			if(method_exists($this, '_class_file'))
				$cf = $this->_class_file();
				
			if($cf)
				return preg_replace("!^(.+)\.php$!", "xfile:$1.html", $cf);
			else
				return 'main.html';
		}

		function _queries() { return array(); }
		function _global_queries() { return array(); }
	}
