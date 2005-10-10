<?
    require_once("{$_SERVER['DOCUMENT_ROOT']}/inc/config.site.php");
    require_once('funcs/DataBaseHTS.php');

    show_last_note();

    function show_last_note()
    {
	   	$notes_page = $GLOBALS['module_data']['notes_page'];

        $hts = new DataBaseHTS();
    
        $page = $hts->dbh->get("SELECT `id` FROM `hts_data_create_time` WHERE `id` REGEXP '.*".addslashes($notes_page).".*/[0-9]{4}/[0-9]{1,2}/$' ORDER BY `value` DESC LIMIT 0, 1");
//        $GLOBALS['log_level'] = 2;

        $code = $hts->get_data($page, 'body');

        if(preg_match("_.+<!--note-->(.+?)<!--/note-->_s",$code,$m))
        {
            echo "<div class=\"box\">{$m[1]}\n";
            echo "<div align=\"left\"><a href=\"$page\"><b>Читать заметку  &#187;&#187;&#187;</b></a></div>\n";
            echo "</div>\n";
        }
    }

?>
