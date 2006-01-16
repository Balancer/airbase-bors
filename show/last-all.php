<?
@header('Content-Type: text/html; charset=utf-8');
@header('Content-Language: ru');

ini_set('default_charset','utf-8');

setlocale(LC_ALL, "ru_RU.utf8");

//$log="";include("{$_SERVER['DOCUMENT_ROOT']}/inc/head.phtml");

require_once("/home/airbase/html/inc/funcs/DataBaseHTS.php");
require_once("/home/airbase/html/inc/funcs/Cache.php");

$ch = new Cache();
$ch->clear_check('last_all','all',120);
if($ch->get('last_all','all'))
{
    echo $ch->last()."cached";
    return;
}

$hts = new DataBaseHTS();

ob_start();

?>
<h5>Последние обновления</h5>
<noindex>
<table class="bta"b cellSpacing="0" cellPadding="2">
<tr><th>Страницы</th></tr>
<?
    foreach($hts->dbh->get_array("
        SELECT time.id id, d.value as description, time.value as time, title.value as title, h1.value as h1, h2.value as h2, h3.value as h3 
            FROM hts_data_modify_times time 
            LEFT JOIN hts_data_descriptions d ON (d.id=time.id) 
            LEFT JOIN hts_data_titles title ON (time.id=title.id)  
            LEFT JOIN hts_data_h1s h1 ON (time.id=h1.id)
            LEFT JOIN hts_data_h2s h2 ON (time.id=h2.id)
            LEFT JOIN hts_data_h3s h3 ON (time.id=h3.id)
            ORDER BY time.value DESC LIMIT 0,10
    ") as $p)
    {
        if(empty($p['title']))
            $p['title'] = $p['h1'].", ".$p['h3'].", ".$p['h2'];
        echo "<tr><td><small><a href=\"".$hts->page_uri_by_id($p['id'])."\"><b>{$p['title']}</b></a><br>{$p['description']} <i>".strftime("%d.%m.%y %H:%M",$p['time'])."</small></td></tr>\n";
    }
?>

<tr><th>Форумы</th></tr>
<?

$dbh = @mysql_connect("localhost", "forum", "localforum") or die (__FILE__.':'.__LINE__." Could not connect");
@mysql_query ("SET CHARACTER SET utf8");
@mysql_select_db("forums_airbase_ru") or die (__FILE__.':'.__LINE__." Could not select database");

$q="SELECT p.pid, p.post, p.author_name, p.post_date, t.forum_id, p.topic_id, p.author_id, t.title, f.name FROM ib_posts p, ib_topics t, ib_forums f WHERE t.tid=p.topic_id AND t.forum_id=f.id AND t.forum_id NOT IN (17) ORDER BY pid DESC LIMIT 0,50";
//echo "q='$q'";
$query = mysql_query ($q) or  die (__FILE__.':'.__LINE__." Query '$q' failed, error ".mysql_errno().": ".mysql_error()."<BR>");

require_once("/home/airbase/html/inc/funcs-text.phtml");

$to_echo='';

if ($rows=mysql_num_rows($query) )
{

    $n=0;

    while( ($out = mysql_fetch_array($query)) && $n<4) 
    {        
        $tid="$out[forum_id]-$out[topic_id]";
        if(!empty($posted[$tid]))
            continue;
        else
            $posted[$tid]=++$n;

        $thread_title = $out['title'];
        $forum_name =$out['name'];
        $author = $out['author_name'];          
                
        $date = strftime("%H:%M",$out['post_date']);

        $thread_url = "http://forums.airbase.ru/?act=ST&f=".$out['forum_id']."&t=".$out['topic_id']."&hl=&view=getnewpost#entry".$out['pid'];
        $thread_title = strip_text($thread_title ,28);
        $forum_url = "http://forums.airbase.ru/?act=SF&f=".$out['forum_id'];
        $date       = $date;
        $profile_link   = "http://airbase.ru/forums/?act=Profile&CODE=03&MID=".$out['author_id'];
        $message = strip_text($out['post'],100-strlen("$forum_name$date"));
        $description=!empty($out['description'])?" (".strip_text($out['description'],64).")":"";
        $message=preg_replace("!<br>|<p>!i"," ",$message);

        $to_echo .= "<tr><td><font size=\"1\"><a href=\"$thread_url\"><b>$thread_title</b></a><br />$message <i>$author, <a href=\"$forum_url\">$forum_name</a>, $date</i></font></td></tr>\n";
    }

}

echo $to_echo;
?>

<tr><th>Чаты</th></tr>
<?
    $out_array=array();
    $chatlist=file("{$_SERVER['DOCUMENT_ROOT']}/chat/chatlist.txt");
    foreach($chatlist as $chat)
    {
        list($chat,$name)=@split("\|",$chat);
        if($name)
        {
            $fh=fopen("{$_SERVER['DOCUMENT_ROOT']}/chat/$chat.txt","rt");
            $number=1;
            while(!feof($fh) && $number)
            {   
                list($number,$time,$author,$ip,$text)=@split("\|",chop(fgets($fh))."||||");
                if($time)
                {
                    $out_array[]="$time|$author|$chat|$name|$text";
                }
            }
            fclose($fh);
        }
    }
    rsort($out_array);
    for($i=0; $i<4; $i++)
    {
        list($time,$author,$chat,$name,$text)=@split("\|",$out_array[$i]);
        $text = strip_text($text,100);
        $text = preg_replace("!<br>|<p>!i"," ",$text);
        $time = strftime("%d.%m.%y %H:%M",$time);
        echo "<tr><td><font size=\"1\">$text, <i>$author, <a href=\"http://airbase.ru/chat/?r=$chat\">$name</a>, $time</i></font></td></tr>\n";
    }
    
?>
</table>
</noidex>

<?
    $out = ob_get_clean();
    echo $out;
    $ch->set('last_all','all',$out);
?>

