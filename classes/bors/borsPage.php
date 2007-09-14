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

	var $_called_url;
	function called_url() { return $this->_called_url; }
	function set_called_url($url) { return $this->_called_url = $url; }
	
	function dir()
	{
		//TODO: затычка!
		return $_SERVER['DOCUMENT_ROOT'].preg_replace('!^http://[^/]+!', '', $this->id());
	}
}
