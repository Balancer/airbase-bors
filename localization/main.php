<?
	include_once("localization/russian-{$GLOBALS['cms']['charset_u']}.php");

	function tr($txt)
	{
		return empty($GLOBALS['cms']['lang'][$txt]) ? $txt : $GLOBALS['cms']['lang'][$txt];
	}

	function ec($txt)
	{
		return iconv('utf8', $GLOBALS['cms']['charset_u'], $txt);
	}
?>