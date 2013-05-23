<?php
    @header('Content-Type: text/html; charset=utf-8');
    @header('Content-Language: ru');

    ini_set('default_charset','utf-8');
    ini_set('mbstring.func_overload','7');
    setlocale(LC_ALL, "ru_RU.utf8");
?>
<link rel=stylesheet type="text/css" href="/inc/css/style.phtml">
<table border=0 id=btab cellSpacing=0>
<tr>
<th>Date</th>
<th>From</th>
<th>To</th>
<th>Reputation</th>
<th>Topic</th>
<?php
    $dbh = mysql_connect("localhost", "forum", "localforum") or die ("Could not connect");
    mysql_select_db("forums_airbase_ru") or die ("Could not select database");
    mysql_query ("SET CHARACTER SET utf8") or  die ("Query '$q' failed, error ".mysql_errno().": ".mysql_error()."<BR>");

    $q="SELECT * FROM `ib_reputation` WHERE `msg_date` > ".(time()-86400*7)." ORDER BY `msg_date` DESC";
    $res = mysql_query ($q) or die ("Query '$q' failed, error ".mysql_errno().": ".mysql_error()."<BR>");

    while($reput = mysql_fetch_array($res)) 
    {
        $time=strftime("<nobr>%Y-%m-%d</nobr> <nobr>%H:%M:%S</nobr>",$reput['msg_date']);
        $from=member($reput['from_id'],$reput['topic_id']);
        $to=member($reput['member_id'],$reput['topic_id']);
        $img="http://forums.airbase.ru/style_images/1/r_".($reput['CODE']==1?'up':'down').".gif";
        $link="http://forums.airbase.ru/?act=ST&f={$reput['forum_id']}&t={$reput['topic_id']}&view=findpost&p={$reput['post']}";

        if($reput['topic_id'])
        {
            $q="SELECT `title` FROM `ib_topics` WHERE `tid`=".$reput['topic_id'];
            $name = mysql_query ($q) or die ("Query '$q' failed, error ".mysql_errno().": ".mysql_error()."<BR>");
            $name = mysql_fetch_array($name);
            $name=$name['title'];
        }
        else
        {
            $name=$link;
        }

        $link="<a href=\"$link\">$name</a>";

        $warn_link = "http://forums.airbase.ru/index.php?act=warn&mid={$reput['from_id']}&CODE=view";

        echo "
<tr><td>$time</td>
<td><a href=\"$warn_link\">{$from['name']}</a><br>{$from['warn_image']}</td>
<td>{$to['name']}</td>
<td><img src=\"$img\" width=\"17\" height=\"17\" valign=\"middle\">{$reput['message']}</td>
<td>$link</td>\n";
    }
    mysql_free_result($res);

    function member($id,$topic)
    {
        global $dbh;
        $id=intval($id);
        if(!$id)
            return NULL;

        $q = "SELECT `name`, `warn_level` FROM `ib_members` WHERE `id`=$id";
        $res = mysql_query ($q) or die ("Query '$q' failed, error ".mysql_errno().": ".mysql_error()."<BR>");
        $res = mysql_fetch_array($res);

        if($res['warn_level']>5) $res['warn_level']=5;
        if($res['warn_level']<0) $res['warn_level']=0;
        $res['warn_image'] = "<a href=\"http://forums.airbase.ru/index.php?act=warn&type=minus&mid=$id&t=$topic&st=0\"><img src=\"http://forums.airbase.ru/style_images/1/warn_minus.gif\" width=\"10\" height=\"9\" border=\"0\"></a><img src=\"http://forums.airbase.ru/style_images/1/warn{$res['warn_level']}.gif\" width=\"49\" height=\"9\" align=\"middle\"><a href=\"http://forums.airbase.ru/index.php?act=warn&type=add&mid=$id&t=$topic&st=0\"><img src=\"http://forums.airbase.ru/style_images/1/warn_add.gif\" width=\"10\" height=\"9\" border=\"0\"></a>";
        return $res;
    }
?>
</table>