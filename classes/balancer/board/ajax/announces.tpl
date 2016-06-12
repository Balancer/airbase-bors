<style>
.ann_wr {
	display: block;
	float: left;
	width: 300px;
	height: 200px;
	position: relative;
	margin: 0 8px 8px 0;
}

.ann_wr .im {
	background-repeat: no-repeat;
	background-size: cover;
	position: absolute;
	background-position: center;
	width: 300px;
	height: 200px;
	text-decoration: none;
}

.ann_wr .title {
	text-align: center;
	font: 18px ScadaAB, Arial, sans-serif;
	color: white;
	text-shadow: 0 0 4px black, 2px 2px 8px black;
	text-decoration: none;
}

</style>

<div style="clear: both; margin-top: 20px;">
{foreach $announces as $x}
<div class="shadow8 ann_wr">
	<a class="im" style="background-image: url('{$x->image_url()}')" href="{$x->announce_url()}" title="{$x->description()|htmlspecialchars}">
		<div class="title">{$x->title()}</div>
	</a>
</div>
{/foreach}
<div class="clear">&nbsp;</div>
</div>

<dl class="box">
	<dt class="nav">
		<a style="font-size: 0.8em" href="http://www.wrk.ru/support/2016/06/t93048,new--anonsy-vnizu-stranits.9657.html">Обсуждение блока анонсов</a>
	</dt>
</dl>
<!-- bors/composer/airbase-bors -->
