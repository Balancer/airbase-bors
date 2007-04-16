<?
	require_once("classes/objects/BorsClassPage.php");

	class BorsBasePage extends BorsClassPage
	{
		function type() { return 'borspage'.get_class($this); }
		
		var $match;
		function BorsBasePage($uri, $match)
		{
			$this->db = &new DataBase($GLOBALS['cms']['mysql_database']);
			$this->match = $match;
			parent::BorsClassPage($uri);
		}
		
		function internal_uri() { return $this->type()."://{$this->id()}"; }
	}
