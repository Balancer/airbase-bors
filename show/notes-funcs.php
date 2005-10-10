<?
    require_once("$DOCUMENT_ROOT/inc/config.site.php");
    require_once('funcs/DataBaseHTS.php');

    function show_last_note($notes_page)
    {
        $hts = new DataBaseHTS();
    
        $res = $hts->dbh->get_array("SELECT `id` FROM `hts_id` WHERE `uri` REGEXP '.*".addslashes($notes_page).".*/[0-9]{4}/[0-9]{1,2}/$'");

        if(!$res)
            return;

//        print_r($res);

//        $GLOBALS['log_level'] = 9;
        $page = $hts->dbh->get("SELECT `id` FROM `hts_data_create_time` WHERE `id` IN(".join(',',$res).") ORDER BY `value` DESC LIMIT 0, 1");
//        $GLOBALS['log_level'] = 2;
        $page = $hts->page_uri_by_id($page);

        $code = $hts->get_data($page, 'body');

        if(preg_match("_.+<!--note-->(.+?)<!--/note-->_s",$code,$m))
        {
            echo "<div class=\"box\">{$m[1]}\n";
            echo "<div align=\"left\"><a href=\"$page\"><b>Читать заметку  &#187;&#187;&#187;</b></a></div>\n";
            echo "</div>\n";
        }
    }

    function show_notes_weeks($notes_page)
    {
        $hts = new DataBaseHTS();
    
        $res = $hts->dbh->get_array("SELECT `id` FROM `hts_id` WHERE `uri` REGEXP '.*".addslashes($notes_page).".*/[0-9]{4}/[0-9]{1,2}/$'");

        if(!$res)
            return;

//        print_r($res);

//        $GLOBALS['log_level'] = 9;
        $pages = $hts->dbh->get_array("SELECT `id` FROM `hts_data_create_time` WHERE `id` IN(".join(',',$res).") ORDER BY `value`");
//        $GLOBALS['log_level'] = 2;
        
        $year = 0;
        foreach($pages as $p)
        {
            $page = $hts->page_uri_by_id($p);

            if(preg_match("!^(.+/(\d{4})/)(\d{1,2})/$!", $page, $m))
            {
                if($year != $m[2])
                {
                    if($year) echo "<br>\n";
                    $year = $m[2];
                    echo "<a href=\"{$m[1]}\">$year</a>: ";
                }
                echo "<a href=\"$page\">{$m[3]}</a> ";
            }
        }
        echo "<br>\n";
    }
?>