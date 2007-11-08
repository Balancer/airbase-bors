<?
require_once("classes/bors/borsPage.php");
include_once("funcs/templates/global.php");

class def_page extends borsPage
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
			{
				foreach($qlist as $qname => $q)
				{
					$cache = NULL;
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
			}
			$data['template_dir'] = $this->_class_dir();
			$data['this'] = $this;

			require_once('funcs/templates/assign.php');
			return template_assign_data($this->_body_template(), $data);
		}

		function add_template_data($var_name, $value)
		{
			return $GLOBALS['cms']['templates']['data'][$var_name] = $value;
		}

		function add_template_data_array($var_name, $value)
		{
			if(preg_match('!^(.+)\[(.+)\]$!', $var_name, $m))
				$GLOBALS['cms']['templates']['data'][$m[1]][$m[2]] = $value;
			else
				$GLOBALS['cms']['templates']['data'][$var_name][] = $value;
		}

		function template_data($var_name)
		{
			return @$GLOBALS['cms']['templates']['data'][$var_name];
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
		
		function change_time() { return max($this->create_time(), $this->modify_time()); }

	function error_message($message, $redirect = false)
	{
		require_once('funcs/modules/messages.php');
		return error_message($message, $redirect, "", -1);
	}

	function message($message, $redirect = false)
	{
		require_once('funcs/modules/messages.php');
		return message($message, $redirect, "", -1);
	}

	var $stb_parents;
	function parents() { return $this->stb_parents; }
	function set_parents($array) { return $this->stb_parents = $array; }
	
	function set_template_data($data)
	{
		foreach($data as $pair)
		{
			if(preg_match('!^(.+?)\[(.+?)\]\s*=(.+)$!', $pair, $m))
				$this->add_template_data_array(trim($m[1]).'['.trim($m[2]).']', trim($m[3]));
			else if(preg_match('!^(.+?)=(.+)$!', $pair, $m))
				$this->add_template_data(trim($m[1]), trim($m[2]));
		}
	}

	function search_source() { return strip_tags($this->body()); }
	function search_type_name() { return "";}

	function auto_search_index() { return true; }
}
