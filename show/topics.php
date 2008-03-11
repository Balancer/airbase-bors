<?
@header("Cache-Control: no-cache, must-revalidate, max-age=0");
@header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
@header("Pragma: no-cache");

require_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config.php");
require_once('funcs/datetime.php');
require_once('funcs/users.php');
require_once('inc/texts.php');

foreach(split(" ", "lines time show t forum ignore_forums") as $var)
	$$var = @$_GET[$var];

if(empty($lines))  $lines=empty($time)?50:2000;
if(empty($forum) && empty($ignore_forums)) $forum="1-150";
if(empty($t)) $t=300;
if(empty($time)) $time='3d';
if(empty($show)) $show='last';

if($time[strlen($time)-1]=='h') $time=substr($time,0,strlen($time)-1)*3600;
if($time[strlen($time)-1]=='d') $time=substr($time,0,strlen($time)-1)*86400;

if(preg_match("!(\d+)\.(\d+)\.(\d+) (\d+):(\d+):(\d+)!",$time,$m))
    $time=time()-mktime($m[4],$m[5],$m[6],$m[2],$m[1],$m[3]);
if(preg_match("!(\d+)\.(\d+)\.(\d+) (\d+):(\d+)!",$time,$m))
    $time=time()-mktime($m[4],$m[5],0,$m[2],$m[1],$m[3]);
if(preg_match("!(\d+):(\d+):(\d+)!",$time,$m))
    $time=time()-mktime($m[1],$m[2],$m[3],date('m'),date('d'),date('Y'));
if(preg_match("!(\d+):(\d+)!",$time,$m))
    $time=time()-mktime($m[1],$m[2],0,date('m'),date('d'),date('Y'));
if(preg_match("!(\d\d)\.(\d\d)\.(\d\d\d\d)!",$time,$m))
    $time=time()-mktime(0,0,0,$m[2],$m[1],$m[3]);
if(preg_match("!(\d\d)\.(\d\d)!",$time,$m))
    $time=time()-mktime(0,0,0,$m[2],$m[1],date('Y'));

    if($time==-1)
    {
        if(empty($lines))
            $lines=500;

        $time = time() - user_data("show_time");

        if($time > 86400)
        {
            $time = 86400;
            if($lines>500)
                $lines=500;
        }
    }

    $time=time()-$time;

    $hts = new DataBaseHTS();

    if(!empty($forum))
        $forum = 'IN ('.parse_forums_list($forum).')';
    if(!empty($ignore_forums))
        $forum = 'NOT IN ('.parse_forums_list($ignore_forums).')';

    //$q="SELECT p.pid, p.author_name, p.post_date, p.forum_id, p.topic_id, p.author_id, t.title, t.description, f.name FROM {$GLOBALS['cms']['ipb_tables_pref']}posts p, {$GLOBALS['cms']['ipb_tables_pref']}topics t, {$GLOBALS['cms']['ipb_tables_pref']}forums f WHERE t.tid=p.topic_id AND t.forum_id=f.id AND p.post_date > $time AND p.forum_id IN ($forum) ORDER BY p.pid DESC LIMIT 0,10000";
    $q="SELECT p.pid, p.author_name, p.post_date, t.forum_id, p.topic_id, p.author_id, t.title, t.description, f.name, p.post FROM {$GLOBALS['cms']['ipb_tables_pref']}posts p, {$GLOBALS['cms']['ipb_tables_pref']}topics t, {$GLOBALS['cms']['ipb_tables_pref']}forums f WHERE t.tid=p.topic_id AND t.forum_id=f.id AND p.post_date > $time AND t.forum_id $forum AND f.id != 19 ORDER BY p.pid DESC LIMIT 0,1000";

	//echo $q;

    $to_echo = '';
    $n=0;

    if ($hts->dbh->query($q))
    {
        while( ($out = $hts->dbh->fetch()) && $n<$lines)
        {        
            $tid="$out[topic_id]";
            if(!empty($posted[$tid]) && $show=='last')
                continue;
            else
                $posted[$tid]=++$n;

            $thread_title = $out['title'];
            $forum_name =$out['name'];
            $author = $out['author_name'];          
                
            $date = strftime("%d.%m.%y %H:%M",$out['post_date']);

            $thread_url="http://forums.airbase.ru/index.php?showtopic={$out['topic_id']}&view=getnewpost&rnd={$out['pid']}";
//        $thread_url = "/forums/?act=ST&f=".$out['forum_id']."&t=".$out['topic_id']."&hl=&view=getnewpost&rnd={$out[pid]}#entry".$out['pid'];
//        $thread_url = "http://forums.airbase.ru/?showtopic=$out[topic_id]&view=findpost&p=$out[pid]";
            $thread_title = $thread_title;
            $forum_url = "http://forums.airbase.ru/?act=SF&f=".$out['forum_id'];
            $forum_name = $forum_name;
            $date       = $date;
//            $author     = str_replace("u","u",$author);
            $profile_link   = "http://forums.airbase.ru/?act=Profile&CODE=03&MID=".$out['author_id'];
            $message = preg_replace("!style_emoticons/<#EMO_DIR#>/!","http://forums.airbase.ru/style_emoticons/default/",$out['post']);
            $message = strip_text($message,512);

            $description=$out['description']?" (".strip_text($out['description'],64).")":"";

            $to_echo .= <<<EOT
<table cellSpacing=0 cellPadding=1 style='border-left: 1px dotted gray; border-bottom: 1px dotted gray; width:100%;font-family:Verdana;font-size:smaller'>
<tr>
<td>
<font size=1><a href=$thread_url>$thread_title</a>$description [<a href=$forum_url>$forum_name</a>], $date<br>
<b><a href=$profile_link>$author</a></b>: $message</font>
</td>
</tr>
</table>
<br>
EOT;
        }
        $hts->dbh->free();
    }

    $visit_time=strftime("%d.%m.%y %H:%M:%S",$time);
    echo <<<EOT
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<noindex>

<table cellpadding='1' cellspacing='1' border='0' width='100%' bgcolor='#000000'; style='font-family:Verdana;font-size:smaller'>
<tr>
<td align='left' bgcolor='#CCCCCC'><b>Последние сообщения</b></td>
</tr>
<tr>
<td align='left' bgcolor='#FEFEFE'>
$to_echo
</td>
</tr>

<tr><td align='left' bgcolor='#FEFEFE'>

<font size=1>Всего обновлённых топиков с $visit_time: $n</font><br>
EOT;

    $out_list=array();
    foreach($hts->dbh->get_array("SELECT * FROM {$GLOBALS['cms']['ipb_tables_pref']}forums WHERE `id` $forum ORDER BY `name`") as $f)
        $out_list[]="<a href=http://forums.airbase.ru/?act=SF&f={$f['id']}>".str_replace(" ","&nbsp;",$f['name'])."</a>";

    echo "<font size=1>Форумы: [".join(" | ",$out_list)."]</font><br>";

    echo "</td></tr></table>";
    echo "<br>";

    set_user_data("show_time",time());

    function parse_forums_list($list)
    {
        $forums=array();
        $f=split(",",$list);

        for($i=0, $count = count($f); $f<$count; $i++)
        {
            if(strpos($f[$i],'-')===false)
            {
                $forums[]=$f[$i];
            }
            else
            {
                list($b,$e)=split('-',$f[$i]);
                for($j=$b;$j<=$e;$j++)
                    $forums[]=$j;
            }
        }

        $forums=@join(",",$forums);
        $forums=str_replace(",,",",",$forums);
        return $forums;
    }

?>
</noindex>
