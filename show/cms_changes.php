<?php
    ini_set('default_charset','utf-8');
    @header("Cache-Control: no-cache, must-revalidate, max-age=0");
    @header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    @header("Pragma: no-cache");
    @header('Content-Type: text/html; charset=utf-8');
    @header('Content-Language: ru');

    setlocale(LC_ALL, "ru_RU.utf8");

    require_once("{$_SERVER['DOCUMENT_ROOT']}/inc/config.site.php");
    require_once('funcs/DataBaseHTS.php');
    require_once('funcs/users.php');

//    $log_level = 9;

    function cms_changes_show($lines=50, $page=false)
    {
        $hts = new DataBaseHTS;
        $acts = array(
            'edit' => 'редактирование',
            'new_page' => 'новая страница',
            'change_property' => 'изменение настроек страницы',
            'delete' => 'удаление страницы',
            'revert_version' => 'откат версии',
            );

        echo "<ul>\n";
        if($lines)
            $lines = " LIMIT 0, ".intval($lines);
        if($page)
            $res = $hts->dbh->get_array("SELECT * FROM `hts_ext_log` WHERE pid='".addslashes($hts->normalize_uri($page))."' GROUP BY user, action ORDER BY `time` DESC $lines");
        else
            $res = $hts->dbh->get_array("SELECT * FROM `hts_ext_log` GROUP BY pid, user ORDER BY `time` DESC $lines");

//        echo "$page";
            
        foreach($res as $r)
        {
            $page  = $r['pid'];
            if($page)
            {
                $title = $hts->get_data($page, 'title', $page);
                $link = "<a href=\"$page?version={$r['version']}\">$title</a>";
            }
            else
            {
                $link = $r['pid'];
            }
            $action = !empty($acts[$r['action']]) ? $acts[$r['action']] : $r['action'];
            if($r['member_id'])
                $user = user_data('nick', $r['member_id'], $r['user']);
            else
                $user = $r['user'];
            echo "<li><b>".strftime("%Y-%m-%d %H:%M:%S", $r['time'])."</b>: $link, <small>$user, $action {$r['description']}</small>\n";
        }
        echo "</ul>\n";
    }

    if(empty($lines))
        $lines = 50;
    if(empty($page) || $page == "http://$host$PHP_SELF")
        $page = false;

//    echo $PHP_SELF;

    cms_changes_show($lines, $page);
?>