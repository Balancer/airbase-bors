<?php
    require_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config.php");
    require_once('funcs/DataBaseHTS.php');
    ini_set('default_charset','utf-8');
    @header('Content-Type: text/html; charset=utf-8');
    setlocale(LC_ALL, "ru_RU.utf8");

    function show_comments($tid, $title='Обсуждение страницы', $lines = 15, $max_post = 0, $reverse = false)
    {
//        $GLOBALS['log_level']=9;

        $tid=intval($tid);

        $hts = new DataBaseHTS();

        if(!$tid)
            return;

        $forum = $hts->dbh->get("SELECT `forum_id` FROM forums_airbase_ru.ib_topics WHERE `tid`=$tid");

        $posts  = $hts->dbh->get_array("SELECT p.pid, p.post, p.post_date, p.author_name FROM forums_airbase_ru.ib_posts         p WHERE topic_id=$tid ORDER BY post_date");
        $posts += $hts->dbh->get_array("SELECT p.pid, p.post, p.post_date, p.author_name FROM forums_airbase_ru.ib_posts_archive p WHERE topic_id=$tid ORDER BY post_date");

        $posts_number = sizeof($posts)-1;
        if(!$posts_number)
            return;

        if($title) echo "<h2>$title. <small>(комментариев: $posts_number)</small></h2>\n";

        $start = max(0, $posts_number-$lines);

        if($posts_number <= $lines)
        {
            $prev_link="";
        }
        else
            $prev_link="\n<center>[ <a href=\"http://forums.airbase.ru/index.php?showtopic=$tid\">предыдущие сообщения (на форуме)</a> ]</center>\n";

        $posts = array_slice($posts, $start+1, $lines);

        $out = array();

        foreach($posts as $post)
        {
            $message=$post['post'];
            $date=strftime("%Y.%m.%d %H:%M",$post['post_date']);
            $nick=$post['author_name'];

            require_once("funcs/texts.php");

            if($max_post>0 && strlen($message)>$max_post)
                $message=strip_text($message, $max_post)." <font size=\"-3\">[ <a href=\"http://forums.airbase.ru/index.php?showtopic=$tid&view=findpost&p={$post['pid']}\">дальше...</a> (ещё символов: ".(strlen($message)-$max_post).") ]</font>";
            $message=preg_replace("!(^|<br>|<p>)\s*(=|\-|\+|_)+\s*($|<br>|<p>)!mi","<hr>",$message);
//            $message=better_wordwrap($message,32," ");
            $out[]="<div class=\"box\" width=\"100%\">$message <div align=\"right\"><small>$date, <b>$nick</b></small></div></div>\n";
        }

        if($reverse) $out = array_reverse($out);

        $out[]="<center>[ <a href=\"http://forums.airbase.ru/index.php?act=Post&CODE=02&f=$forum&t=$tid\">добавить $title</a> ]</center>\n\n";

        return $prev_link . join("",$out);
    }

    function show_forum_comments($id)
    {
        list($id,$forum,$title,$lines,$max_post,$reverse)=explode(",",$id.',,,,,');

        if(empty($forum)) $forum=14;
        if(empty($title)) $title="Комментарии";
        if($title == '-') $title='';
        $topic=intval($id);
        if(empty($lines))
            $lines=5;

        $hts = new DataBaseHTS();

        if($id>0)
            $id = $hts->dbh->get("SELECT tid FROM {$GLOBALS['cms']['ipb_tables_pref']}topics WHERE ubb_topic='$forum-$topic'");
        else
            $id = -$id;

        echo show_comments($id, $title, $lines, $max_post, $reverse);
    }
?>
