<?php
    @header('Content-Type: text/html; charset=utf-8');
    @header('Content-Language: ru');

    ini_set('default_charset','utf-8');
    ini_set('mbstring.func_overload','7');
    setlocale(LC_ALL, "ru_RU.utf8");

    $dbh = @mysql_connect("localhost", "forum", "localforum") or die ("Could not connect");
    @mysql_select_db("forums_airbase_ru") or die ("Could not select database");
    mysql_query ("SET CHARACTER SET utf8");

    $res = mysql_query ("SELECT * FROM `ib_members` WHERE `warn_level`>0 OR `restrict_post` = 1 ORDER BY `warn_level` DESC, `name` ASC") or die ("Query failed, error ".mysql_errno().": ".mysql_error()."<BR>");

    echo "<font face=\"Verdana\" size=\"-1\">";

    while($member = mysql_fetch_array($res)) 
    {
        $warn=$member['warn_level'];
        $iwarn = intval($warn/2+0.5);
        if($iwarn>5) $iwarn=5;
        if($iwarn<0) $iwarn=0;
        list($start_ro, $stop_ro, $left_ro, $units_ro) = explode(":", $member['restrict_post']); //1095341423:1096867752:-423:h
        $ban = $stop_ro ? "<font size=\"1\" color=\"red\">[R/O до ".strftime("%d.%m.%Y %H:%M",$stop_ro)."]</font>" : "";
        if($member['restrict_post'] == "1")
	        $ban = "<font size=\"1\" color=\"red\">[Бесрочный R/O]</font>";
        echo "<a href=\"http://forums.airbase.ru/index.php?act=warn&mid={$member['id']}&CODE=view\"><img src=\"http://forums.airbase.ru/style_images/1/warn$iwarn.gif\" width=\"49\" height=\"9\" align=\"middle\" border=\"0\"></a>&nbsp;<a href=\"http://forums.airbase.ru/?act=Profile&CODE=03&MID={$member['id']}\">{$member['name']}</a> - <b>$warn</b> $ban<br>\n";
    }
    mysql_free_result($res);
?>
</font>