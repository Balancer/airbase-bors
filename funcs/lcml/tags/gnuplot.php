<?
    function lp_gnuplot($text,$params)
    {
		global $wgScriptPath, $wgUploadPath;
		error_reporting( E_ALL & ~ E_NOTICE );
		define( 'MEDIAWIKI', true );
		$incs =  ini_get('include_path');
		include_once("/var/www/wiki.airbase.ru/htdocs/LocalSettings.php");
		include_once('GlobalFunctions.php');
		include_once('/var/www/wiki.airbase.ru/htdocs/extensions/Gnuplot.php');
		$wgUploadPath = "http://wiki.airbase.ru/images";
		$ret = renderGnuplot($text);
		error_reporting( E_ALL );
		ini_set('include_path', $incs);
		return $ret;
	}
