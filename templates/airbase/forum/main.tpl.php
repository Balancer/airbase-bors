<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?=$title?></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="SHORTCUT ICON" href="/favicon.ico" />

<link rel="stylesheet" type="text/css" href="http://forums.balancer.ru/tpl/default/css/main3.css" />

	<script  type="text/javascript" src="/bors-shared/js/funcs.js"></script>
	<script  type="text/javascript" src="/bors-shared/js/tune.js"></script>


<script type="text/javascript"><!--
createCookie('class_name', '<?=$self->class_name()?>');createCookie('object_id', '<?=$self->id()?>')

var me_id = readCookie('user_id', 0)
var me_hash = readCookie('cookie_hash', 0)
if(me_id > 1 && me_hash) {
	document.writeln("<"+"script  type=\"text/javascript\" src=\"/user/"+me_id+"/setvars.js\"></"+"script>")
}
--></script>

<script type="text/javascript"><!--
if(top.me_is_coordinator) {
	document.writeln('<link rel="stylesheet" type="text/css" href="/bors-shared/css/coordinators.css" />')
	document.writeln('<'+'script  type="text/javascript" src="/bors-shared/js/coordinators.js"></'+'script>')
}

--></script>

<?if($type == 'topic' && $id):?>
<script type="text/javascript"><!--
var readed = readCookie('readedTopics', "").split(' ')
if(!inArray(readed, <?=$id?>) && readed.length < 40) readed.push(<?=$id?>)
createCookie('readedTopics', readed.join(' '), 3)
--></script>
<?endif;?>
<!--[if IE]><style>
.outer, .wide, h2, .wrapper, .minwidth {
	height: 0;
	height: auto;
	zoom: 1;
?>
dd, dt { height: 1%; ?>
</style><![endif]-->
<!--[if lt IE 7]><style>
.minwidth {
	border-left: 404px solid #fff;
?>
.wrapper {
	margin-left: -404px;
	position: relative;
?>
</style><![endif]-->

<?php
if($header)
	foreach($header as $h)
		echo $h;

if($meta)
	foreach($meta as $key => $value)
echo "<meta name=\"{$key}\" content=\"".htmlspecialchars($value)."\" />\n";

if($head_append)
	foreach($head_append as $s)
		echo $s;
?>
</head>

<body onload="onLoadPage()" id="body">

<div class="minwidth"><div class="wrapper">

<div class="wide top">
&nbsp;
</div>

<div class="outer">

	<div class="tpl_column_center"><div class="incenter" id="incenter">

		<dl class="box">
		<dt class="nav"> <?=bors_tpl_module('nav_top')?> </dt>
		<dd>
			<h1><?=$title?></h1>
			<?=$description?>
<?if($self->keywords_string()):?><br /><i>Ключевые слова: <?=$self->keywords_string()?></i><?endif;?>
		</dd>
		</dl>

<!--[if lt IE 7]><dl class="box"><dd class="warning_note left">
Вы используете Internet Explorer устаревшей и не поддерживаемой более версии.
Чтобы не было проблем с отображением сайтов или форумов обновите его до 
<a href="http://www.microsoft.com/rus/windows/downloads/ie/getitnow.mspx">версии 7.0</a> или более новой. Ещё лучше - поставьте
браузер <a href="http://www.opera.com/">Opera</a> или 
<a href="http://mozilla.ru/">Mozilla Firefox</a>. <br/><br/>
<b>Обсудить и задать вопросы <a href="http://balancer.ru/support/2009/02/topic-66122-IE6-bol~she-ne-podderzhivaetsya.8198.html">можно в этой теме</a></b>.
</dd></dl><![endif]-->

		<?=$body?>

		<dl class="box">
		<dt class="nav"> <?=bors_tpl_module('nav_top')?> </dt>
		</dl>

	  <div class="copyright">
        Copyright &copy; Balancer 1997..<?=date('Y')?><br/>
<?
if($ct = $self->create_time(true))
	echo "Создано ".date('d.m.Y', $ct)."<br />\n";
if($views)
	echo $views."-е посещение с ".date('d.m.Y', $views_first)."<br />\n";
if($views_average)
	echo "В среднем посещений в день: ".$views_average."<br />\n";
?>
	 </div>


	</div></div> <!-- end center div -->

	<div class="tpl_column_left" id="inleft"><div class="inleft">

		<dl class="box w200">
		<dt>Поиск</dt>
		<dd>
			<form method="get" action="http://balancer.ru/tools/search/result/"><div>
			<input type="text" class="text" name="q" value="Су-27" />
			<input type="submit" class="submit" value="искать" />
			</div></form>
		</dd>
		</dl>
		
		<dl class="box w200">
		<dt>Настройки</dt>
		<dd>
			<script type="text/javascript">
				createSelect("Шрифт", "fontFamily", "default(Verdana):Verdana;Georgia;sans-serif;serif;monospace", "Verdana")
				createSelect("Размер", "fontSize", "8pt;default(10pt):10pt;12pt;14pt;16pt;20pt;24pt", "10pt")
				createSelect("Расстояние до монитора", "incenter.width", "auto;30cm:14cm;45cm:20cm;70cm:30cm", "auto")
			</script>
			<noscript><p>Не работает без JavaScript</p></noscript>
		</dd>
		</dl>

<dl class="box w200">
<dt>Персональное</dt>
<dd>
<script type="text/javascript"><!--
if(me_id < 2 || !me_hash)
	document.writeln("<"+"script  type=\"text/javascript\" src=\"/templates/login.js\"></"+"script>")
else {
	document.writeln("<"+"script  type=\"text/javascript\" src=\"/user/"+me_id+"/personal.js\"></"+"script>")
	document.writeln("<"+"script  type=\"text/javascript\" src=\"/js/users/touch.js?<?=$self->class_name()?>://<?=$self->id()?>\"></"+"script>")
}
--></script>
<noscript><p>Не работает без JavaScript</p></noscript>
</dd></dl>

<dl class="box w200"><dt>Новости сайта</dt>
<dd><?=bors_tpl_module('forum_sitenews')?></dd>
</dl>

<dl class="box w200"><dt>Популярные темы</dt>
<dd><script type="text/javascript" src="http://balancer.ru/js/forum/topvisits.js"></script></dd>
</dl>

<script type="text/javascript"><!--
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
--></script>
<script type="text/javascript"><!--
var pageTracker = _gat._getTracker("UA-3907196-1");
pageTracker._trackPageview();
--></script>

<dl class="box w200"><dt>География форума</dt>
<dd>
<div align="center">

<span><!--<center><a href="http://whos.amung.us/show/qz1y4sp9"><img src="http://whos.amung.us/swidget/qz1y4sp9.png" alt="website counter" width="80" height="15" /></a></center>--></span>
<br />
<script type="text/javascript"><!--
kod = 2537;
width = 185;
document.write('<span><!-'+'-<a href="http://www.mapyourvisitors.com/map' + kod + '.php"><img src="http://www.mapyourvisitors.com/' + width + '/' + kod + '.jpg?r=' + escape(document.referrer) + '&u=' + escape(document.URL) + '" alt="MapYourVisitors.COM" title="MapYourVisitors.COM - Нанесите на карту ваших посетителей" /></a>-'+'-></span><br />');
--></script>
<br />

<span><!--<center><a href="http://s02.flagcounter.com/more/NNik"><img src="http://s02.flagcounter.com/count/NNik/bg=FFFFFF/txt=000000/border=CCCCCC/columns=2/maxflags=12/viewers=0/labels=0/" alt="free counters" border="0"></a></center>--></span>
<br/>

<!--Rating@Mail.ru COUNTER--><script language="JavaScript" type="text/javascript"><!--
d=document;var a='';a+=';r='+escape(d.referrer)
js=10//--></script><script language="JavaScript1.1" type="text/javascript"><!--
a+=';j='+navigator.javaEnabled()
js=11//--></script><script language="JavaScript1.2" type="text/javascript"><!--
s=screen;a+=';s='+s.width+'*'+s.height
a+=';d='+(s.colorDepth?s.colorDepth:s.pixelDepth)
js=12//--></script><script language="JavaScript1.3" type="text/javascript"><!--
js=13//--></script><script language="JavaScript" type="text/javascript"><!--
d.write('<a href="http://top.mail.ru/jump?from=1574967"'+
' target="_top"><img src="http://d8.c0.b8.a1.top.mail.ru/counter'+
'?id=1574967;t=57;js='+js+a+';rand='+Math.random()+
'" alt="Рейтинг@Mail.ru"'+' border="0" height="31" width="88"/><\/a>')
if(11<js)d.write('<'+'!-- ')//--></script><noscript><a
target="_top" href="http://top.mail.ru/jump?from=1574967"><img
src="http://d8.c0.b8.a1.top.mail.ru/counter?js=na;id=1574967;t=57"
border="0" height="31" width="88"
alt="Рейтинг@Mail.ru"/></a></noscript><script language="JavaScript" type="text/javascript"><!--
if(11<js)d.write('--'+'>')//--></script><!--/COUNTER-->
</div>

</dd></dl>

	</div></div> <!-- end left div -->

	<br class="clear" />
</div> <!-- end outer div -->

<div class="wide bottom">
&nbsp;
</div>

</div></div>
</body>
</html>
