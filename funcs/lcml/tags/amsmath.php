<?
    function lp_amsmath($text,$params)
    {
		global $wgScriptPath;
		error_reporting( E_ALL & ~ E_NOTICE );
		define( 'MEDIAWIKI', true );
		$incs =  ini_get('include_path');
		include_once("/var/www/wiki.airbase.ru/htdocs/LocalSettings.php");
		$ret = Wikitex::amsmath($text, array());
		error_reporting( E_ALL );
		ini_set('include_path', $incs);
		return $ret;
	}
?>
