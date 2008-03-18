<?
    require_once("{$_SERVER['DOCUMENT_ROOT']}/inc/config.site.php");
    require_once('funcs/DataBaseHTS.php');

    show_notes_weeks();

    function show_notes_weeks()
    {
        $hts = new DataBaseHTS();

	   	$notes_page = $GLOBALS['module_data']['notes_page'];

//        print_r($res);

//        $GLOBALS['log_level'] = 9;
        $pages = $hts->dbh->get_array("SELECT `id` FROM `hts_data_create_time` WHERE `id` REGEXP '.*".addslashes($notes_page).".*/[0-9]{4}/[0-9]{1,2}/$' ORDER BY `value`");
//        $GLOBALS['log_level'] = 2;
        
        $year = 0;
        foreach($pages as $page)
        {
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
