<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="https://www.google.com/jsapi"></script>


<script>
	function drawChart() {

		var data = new google.visualization.DataTable();

		data.addColumn('datetime', 'Месяц');
		data.addColumn('number', 'Сообщений в месяц');

		{foreach $stat as $date => $count}
		data.addRow([(new Date({$date})),parseFloat({$count})]);
		{/foreach}

		var chart = new google.visualization.ColumnChart($('#chart').get(0));

		chart.draw(data, {
			title: 'Форумная активность {$this->user()->title()}',
			colors: ['blue'],
			bar: { groupWidth: "90%"}
		});

	}

	google.load('visualization', '1', {
		packages: ['corechart', 'bar']
	});

	// call drawChart once google charts is loaded
	google.setOnLoadCallback(drawChart);
</script>

<div id="chart" style="width: 100%; height: 400px;"></div>
