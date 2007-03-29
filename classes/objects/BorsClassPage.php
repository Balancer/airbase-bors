<?
	require_once('BorsBaseObject.php');
	class BorsClassPage extends BorsBaseObject
	{
		var $hts;
		
		function BorsClassPage($uri)
		{
//			echo "page '$uri'<br />";
			$this->hts = &new DataBaseHTS($uri);
			$this->BorsBaseObject($uri);
		}
	
		function type() { return 'page'; }
		
		function parents()
		{
			return array();
		}

        function title()
		{
			return $this->hts->get('title');
		}
	}
