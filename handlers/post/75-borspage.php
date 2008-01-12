<?
	register_handler('!.*!', 'handler_bors_auto');

	function handler_bors_auto($uri, $m)
	{
//		echo "<tt>try show page '$uri'</tt><br/>"; exit();
		
//		$GLOBALS['cms']['cache_disabled'] = true;
		require_once("classes/objects/Bors.php");
		require_once("classes/inc/bors.php");

		if($ret = bors_object_show(class_load($uri, NULL, 1, false)))
			return $ret;

		return false;
	}
