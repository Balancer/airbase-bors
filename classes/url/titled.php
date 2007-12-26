<?php

class url_titled extends url_base
{
	function url($page=NULL)
	{
		if(preg_match("!^http://!", $this->id()->id()))
			return $this->id()->id();
			
		if($page < 1)
			$page = $this->id()->page();
			
		require_once("funcs/modules/uri.php");
		$uri = $this->id()->base_url().strftime("%Y/%m/%d/", $this->id()->modify_time());
		$uri .= $this->id()->uri_name()."-".$this->id()->id();

		if($page > 1)
			$uri .= ",$page";

		$uri .= "--".translite_uri_simple($this->id()->title()).".html"; 
		return $uri;
	}
}
