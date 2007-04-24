<?
	require_once("classes/objects/BorsClassPage.php");

	class BorsBasePage extends BorsClassPage
	{
		function type() 
		{
			if(substr($this->id(), 0, 7) == 'http://')
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
			if(substr($this->id(), 0, 7) == 'http://')
				return $this->id(); 
			else
				return $this->type()."://".$this->id()."/";
		}
	}
