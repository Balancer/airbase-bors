<?
    ini_set('include_path', ini_get('include_path') . ":/www/docs/www1001kran/www/cms");

    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
    ini_set('log_errors', 'On');

    require_once("funcs/DataBaseHTS.php");
    require_once("funcs/Cache.php");

    echo module_show_news();
    
    function module_show_news()
    {
		$db = new DataBaseHTS();
//		echo $GLOBALS['page'];

//		$GLOBALS['log_level']=10;
		$pages = $db->dbh->get_array("SELECT b.*, t.value as time FROM `hts_data_body` b LEFT JOIN `hts_data_create_time` t ON (b.id LIKE t.id) WHERE b.id LIKE '".addslashes($GLOBALS['page'])."%' AND b.id NOT LIKE '".addslashes($GLOBALS['page'])."' ORDER BY t.value DESC LIMIT 0,10");
		foreach($pages as $p)
		{
//			print_r($p);
		    $page = $p['id'];
		    $body = $p['value'];
	    	$description = $db->get_data($page, 'description');
		    $title = $db->get_data($page, 'title');
		    $date = strftime("%d.%m.%y", $p['time']); //$db->get_data($page, 'create_time'));

		    $width  = @$GLOBALS['module_data']['width'];
		    $height = @$GLOBALS['module_data']['height'];

		    if(!$width)		
		    	$width  = 600;

		    if(!$height)	
		    	$height = 600;

			$more = ec("<div align=\"right\"><a href=\"$page\" class=\"popup\" target=\"_blank\" onClick=\"window.open('$page','Popup".md5($page)."','toolbar=no,directories=no,width=$width,height=$height,resizable=yes'); return false;\">Подробнее &#187;&#187;&#187;&nbsp;&nbsp;&nbsp;</a></div>");

?>
<div style="background-color: #F0EBCB; border: 1px solid #906030; padding: 2px; font-size: 9pt; font-weight: bold; text-align: center;"><?echo $title?></div>
<div style="border: 1px solid #906030; padding: 2px; font-size: 8pt;"><div style="font-size: 8pt; font-weight: bold; text-align: right;"><?echo $date?></div><?echo $description?$description.$more:$body?></div>
<p>
<?
		}
//	print_r($pages);	
//	$GLOBALS['log_level']=2;
    }
?>
