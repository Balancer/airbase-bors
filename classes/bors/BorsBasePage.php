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
			$this->db = &new DataBase($GLOBALS['cms']['mysql_database']);
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
	}
