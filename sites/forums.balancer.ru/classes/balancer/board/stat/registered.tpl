<dl class="box">
<dt>Количество регистраций в месяц</dt>
<dd>
<p>Учитываются только пользователи, написавшие более десяти сообщений</p>
<div id="mcontainer" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
</dd>
</dl>

<dl class="box">
<dt>Количество регистраций в день</dt>
<dd>
<p>Учитываются только пользователи, написавшие более десяти сообщений</p>
<div id="dcontainer" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
</dd>
</dl>


{js_ready}

$('#mcontainer').highcharts({
	chart: {
		type: 'column'
	},
	title: {
		text: 'Регистраций в месяц'
	},
	xAxis: {
		type: 'datetime'
	},
	yAxis: {
		min: 0,
		title: {
			text: 'Количество регистраций'
		}
	},
	plotOptions: {
		column: {
			pointPadding: 0.002,
			borderWidth: 0
		}
	},
	series: [{
		name: 'Дата',
		data: [
{foreach $regs_by_month as $x}
			{if !$x@first},{/if}[Date.UTC({$x.year}, {$x.month}, 1), {$x.count} ]
{/foreach}
		]
	}]
});

$('#dcontainer').highcharts({
	chart: {
		type: 'column'
	},
	title: {
		text: 'Регистраций в день'
	},
	xAxis: {
		type: 'datetime'
	},
	yAxis: {
		min: 0,
		title: {
			text: 'Количество регистраций'
		}
	},
	plotOptions: {
		column: {
			pointPadding: 0.002,
			borderWidth: 0
		}
	},
	series: [{
		name: 'Дата',
		data: [
{foreach $regs_by_day as $x}
			{if !$x@first},{/if}[Date.UTC({$x.year}, {$x.month}, {$x.day}), {$x.count} ]
{/foreach}
		]
	}]
});

{/js_ready}
