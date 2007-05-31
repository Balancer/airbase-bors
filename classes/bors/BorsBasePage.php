<?
	require_once("classes/objects/BorsClassPage.php");

	class BorsBasePage extends BorsClassPage
	{
		function type() 
		{
			if(preg_match('!^http://!', $this->id()))
				return get_class($this);
			else
				return get_class($this);
		}
		
		var $match;
		function BorsBasePage($uri, $match = false)
		{
			$this->match = $match;
			parent::BorsClassPage($uri);
		}
		
		function internal_uri() 
		{
			if(preg_match('!^http://!', $this->id()))
				return $this->id(); 
			else
				return $this->type()."://".$this->id()."/";
		}

		function cacheable_body()
		{
			$data = array();
		
			if($qlist = $this->_queries())
				foreach($qlist as $qname => $q)
				{
					$cache = NULL;
					if(preg_match("!^(.+)\|(\d+)$!s", $q, $m))
					{
						$q		= $m[1];
						$cache	= $m[2];
					}
					
					$data[$qname] = $this->db->get_array($q, false, $cache);
				}

			$data['template_dir'] = $this->_body_template_dir();

			return template_assign_data($this->_body_template(), $data);
		}

		function add_template_data($var_name, $value)
		{
			$GLOBALS['cms']['templates']['data'][$var_name] = $value;
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
		function _global_queries() { return array(); }
	}
