<?
    require_once("$DOCUMENT_ROOT/inc/config.site.php");
    require_once('funcs/DataBaseHTS.php');
    ini_set('default_charset','utf-8');
    @header('Content-Type: text/html; charset=utf-8');
    setlocale(LC_ALL, "ru_RU.utf8");

    function show_topic($tid, $page)
    {
		$ppt = 15;

        $tid=intval($tid);

		$start = ($page-1)*$ppt;

        $hts = new DataBaseHTS();

        if(!$tid)
            return;

        $forum = $hts->dbh->get("SELECT `forum_id` FROM FORUM.ib_topics WHERE `tid`=$tid");

        $posts  = $hts->dbh->get_array("SELECT p.pid, p.post, p.post_date, p.author_name FROM FORUM.ib_posts         p WHERE topic_id=$tid ORDER BY post_date LIMIT $start,$ppt;");
        $posts += $hts->dbh->get_array("SELECT p.pid, p.post, p.post_date, p.author_name FROM FORUM.ib_posts_archive p WHERE topic_id=$tid ORDER BY post_date LIMIT $start,$ppt;");

        $posts = array_slice($posts, 0, $ppt);

//        print_r($posts);

        $out = array();

        require_once("funcs/funcs.phtml");

        foreach($posts as $post)
        {
            $message=$post['post'];
            $date=strftime("%Y.%m.%d %H:%M",$post['post_date']);
            $nick=$post['author_name'];

            $out[]="<dl class=\"box\" width=\"600\"><dt><small><b>$nick</b>, $date</small><dd>$message</dl>\n";
        }

        return join("",$out);
    }
	if(empty($page))
	{
?>
<link rel="stylesheet" type="text/css" href="http://www.airbase.ru/inc/css/style.phtml">
<style>
h1 { font: bold x-large Verdana, sans-serif; border-bottom: 2px solid #003366; margin: 0px; padding: 0px;}
h2 { font: bold large Verdana, sans-serif; border-bottom: 1px solid #003366; margin: 8px 0px 4px 0px; padding: 0px;}
h3 { font: bold normal Verdana, sans-serif; margin: 0px; padding: 0px;}
h4 { font: bold xx-small Verdana, sans-serif; margin: 0px; padding: 0px;}
h5 { font: bold smaller Verdana, sans-serif; margin: 0px; padding: 0px;}
h6 { font: bold xxx-small Verdana, sans-serif; margin: 0px; padding: 0px;}
td {font: smaller Verdana, sans-serif; text-align: justify;}
.nav { border-bottom: 1px solid #003366; width: auto; font-size: x-small;}
body {margin: 0px; padding: 0px 0px 32px 4px;}
dl.box {border: 1px solid #0066cc; margin: 0px 4px 4px 4px; padding: 0px; background-color: #e8f0fc;}
dl.box dt, dl.box dt a {margin: 0px; color: #ffffff; background-color: #0066cc; padding: 1px; font-size: 8pt; font-weight: bold; font-family: Verdana, sans-serif;}
dl.box dd {margin: 2px; font-size: xx-small;}
dl.box dt a {color: #ffffd0;}

.box {border: 1px solid #0066cc; margin: 0px 4px 4px 4px; padding: 2px;  background-color: #e8f0fc;}

div.tabs {
    background: transparent;
    border-collapse: collapse;
    border-bottom-color: #0066cc;
    border-bottom-style: solid;
    border-bottom-width: 1px;
    padding: 0.5em 0 0 2em;
    margin: 0;
    white-space: nowrap;
}

div.tabs a {
    background: transparent; /*#e8f0fc*/;
    border-color: #0066cc;
    border-width: 1px; 
    border-style: solid solid none solid;
    color: #436976;
    font-weight: normal;
    font-size: smaller;
    height: 1.2em;
    margin: 0;
    margin-right: 0.5em;
    padding: 0 1em;
    text-decoration: none;
}

div.tabs a.selected {
    background: #F0F4F8;
    border: 1px solid #0066cc;
    border-bottom: #F0F4F8 1px solid;
    color: #436976;
    font-weight: bold;
}

div.tabs a:hover {
    background: #F0F4F8;
    border-color: #0066cc;
    border-bottom: #F0F4F8 1px solid;
    color: #436976;
    text-decoration: none;
}

a.external {
    background: url(http://www.airbase.ru/img/design/system/external.png) center right no-repeat;
    padding-right: 13px;
}
</style>
<?
	    $pages = array();
		for($i=1;$i<=17;$i++)
		{
			$pages[] = "<a href=\"#$i\" onClick=\"script_load('/cms-local/include/js.php/cms-local/include/t.php?page=$i'); return false;\">[$i]</a>";
		}
		echo "<b><font size=+1>".join(" ", $pages)."</font></b><br><br>";
?>
<div id="loader_here"></div>
<div id="inline_here"><?echo show_topic(31754,1);?></div>
<script>
function script_load(file)
{
    var loader=document.getElementById("loader_here")

	loader.innerHTML="";
	loader.innerHTML="<script></"+"script>";

	top.insert_element = document.getElementById("inline_here")

	var script = loader.getElementsByTagName("script")[0]
    script.language = "JavaScript"
    if (script.setAttribute) 
    	script.setAttribute('src', file)
    else 
    	script.src = file
}
</script>
<?
	}
	else
	{
		echo show_topic(31754,$page);
	}
?>
