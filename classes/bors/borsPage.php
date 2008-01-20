<?

require_once('classes/objects/BorsBaseObject.php');
class borsPage extends BorsBaseObject
{
	var $hts;
		
	function BorsClassPage($uri)
	{
//			echo "page '$uri'<br />";
		if(preg_match("!^\w+://!", $uri))
			$this->hts = &new DataBaseHTS($uri);
		else
			$this->hts = false;
				
		$this->BorsBaseObject($uri);
	}
	
       function title()
	{
		return $this->hts ? $this->hts->get('title') : parent::title();
	}

	function dir()
	{
		//TODO: затычка!
		return $_SERVER['DOCUMENT_ROOT'].preg_replace('!^http://[^/]+!', '', $this->called_url());
	}

	function can_be_empty()		{ return true; }
}
