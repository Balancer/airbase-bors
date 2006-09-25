<?
    function lp_music($text,$params)
    {
		global $wgScriptPath;
		error_reporting( E_ALL & ~ E_NOTICE );
		define( 'MEDIAWIKI', true );
		$incs =  ini_get('include_path');
		include_once("/var/www/wiki.airbase.ru/htdocs/LocalSettings.php");
		
		$text = html_entity_decode($text, ENT_NOQUOTES, 'UTF-8');

		$ret = Wikitex::music($text, array());
		error_reporting( E_ALL );
		ini_set('include_path', $incs);
		return $ret;
	}
?>
