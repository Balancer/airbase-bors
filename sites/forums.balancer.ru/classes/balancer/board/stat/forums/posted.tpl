<dl class="box">
<dt>Количество сообщений в месяц</dt>
<dd>
<div id="mcontainer" style="min-width: 400px; height: 900px; margin: 0 auto"></div>
</dd>
</dl>

<dl class="box">
<dt>Количество сообщений в год</dt>
<dd>
<div id="ycontainer" style="min-width: 400px; height: 900px; margin: 0 auto"></div>
</dd>
</dl>

{js_ready}

$('#mcontainer').highcharts({
	chart: {
		type: 'column',
		zoomType: 'x'
	},
	title: {
		text: 'Активность форумов'
	},
	xAxis: {
		type: 'datetime',
//		maxZoom: 100 * 24 * 3600000,
	},
	yAxis: {
		min: 0,
		title: {
			text: 'Количество сообщений в месяц'
		}
	},
	plotOptions: {
		column: {
			stacking: 'normal',
			pointPadding: 0,
			borderWidth: 0,
		}
	},
	series: [
{foreach $posts_by_month as $s}
		{
//			pointStart: Date.UTC({date("Y, m, d", $start)}),
//			pointInterval: 30 * 24 * 3600000,
//			pointRange: 10*30 * 24 * 3600000,
			name: "{$s.forum->title()|addslashes}",
			data: [
{foreach $s.data as $x}
				[Date.UTC({$x.year}, {$x.month}, 1), {$x.count} ]{if !$x@last},{/if} 
{/foreach}
			]
		}{if not $s@last}, {/if} 
{/foreach}
	]
});

$('#ycontainer').highcharts({
	chart: {
		type: 'column',
		zoomType: 'x'
	},
	title: {
		text: 'Активность форумов по годам'
	},
	xAxis: {
		type: 'datetime',
	},
	yAxis: {
		min: 0,
		title: {
			text: 'Количество сообщений в год'
		}
	},
	plotOptions: {
		column: {
			stacking: 'normal',
			pointPadding: 0,
			borderWidth: 0
		}
	},
	series: [
{foreach $posts_by_year as $s}
		{
			name: "{$s.forum->title()|addslashes}",
			data: [
{foreach $s.data as $x}
				[Date.UTC({$x.year}, 1, 1), {$x.count} ]{if !$x@last},{/if} 
{/foreach}
			]
		}{if not $s@last}, {/if} 
{/foreach}
	]
});

{/js_ready}
