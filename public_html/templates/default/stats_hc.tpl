<style type="text/css">
#chart1, #chart2, #chart3, #chart4, #chart5 { margin-bottom: 2em; }
</style>
<script type="text/javascript" src="//ivacuum.org/js/highstock/1.0b/highstock.js"></script>
<div style="margin: 0 1em 0 0;">

<div class="tCenter" style="margin-bottom: 0.6em;">
	<a href="?mode=peers" class="btn btn-left <!-- IF MODE eq 'peers' --> btn-active<!-- ENDIF -->">Пиры</a><a href="?mode=users" class="btn btn-middle <!-- IF MODE eq 'users' --> btn-active<!-- ENDIF -->">Регистрации</a><a href="?mode=torrents" class="btn btn-middle <!-- IF MODE eq 'torrents' --> btn-active<!-- ENDIF -->">Торренты</a><a href="?mode=posts" class="btn btn-middle <!-- IF MODE eq 'posts' --> btn-active<!-- ENDIF -->">Сообщения</a><a href="?mode=speed" class="btn btn-middle <!-- IF MODE eq 'speed' --> btn-active<!-- ENDIF -->">Скорость обмена</a><a href="?mode=traffic&time=week" class="btn btn-right <!-- IF MODE eq 'traffic' --> btn-active<!-- ENDIF -->">Трафик</a>
</div>

<!-- IF MODE eq 'peers' -->
<div class="tCenter" style="margin-bottom: 0.6em;">
	<a href="?mode=peers&time=day" class="btn btn-left<!-- IF TIME eq 'day' --> btn-active<!-- ENDIF -->">За день</a><a href="?mode=peers&time=week" class="btn btn-middle<!-- IF TIME eq 'week' --> btn-active<!-- ENDIF -->">За неделю</a><a href="?mode=peers&time=month" class="btn btn-right<!-- IF TIME eq 'month' --> btn-active<!-- ENDIF -->">За месяц</a>
</div>
<!-- ENDIF -->

<!-- IF MODE eq 'speed' -->
<div class="tCenter" style="margin-bottom: 0.6em;">
	<a href="?mode=speed&time=day" class="btn btn-left<!-- IF TIME eq 'day' --> btn-active<!-- ENDIF -->">За день</a><a href="?mode=speed&time=week" class="btn btn-middle<!-- IF TIME eq 'week' --> btn-active<!-- ENDIF -->">За неделю</a><a href="?mode=speed&time=month" class="btn btn-right<!-- IF TIME eq 'month' --> btn-active<!-- ENDIF -->">За месяц</a>
</div>
<!-- ENDIF -->

<!-- IF MODE eq 'traffic' -->
<div class="tCenter" style="margin-bottom: 0.6em;">
	<a href="?mode=traffic&time=week" class="btn btn-left<!-- IF TIME eq 'week' --> btn-active<!-- ENDIF -->">За неделю</a><a href="?mode=traffic&time=month" class="btn btn-right<!-- IF TIME eq 'month' --> btn-active<!-- ENDIF -->">За месяц</a>
</div>
<!-- ENDIF -->

<div id="chart1"></div>
<div id="chart2"></div>
<div id="chart3"></div>
<div id="chart4"></div>
<div id="chart5"></div>

</div>

<script type="text/javascript">
Highcharts.setOptions({
	credits: {
		enabled: false,
	},
	lang: {
		months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
		rangeSelectorFrom: 'С:',
		rangeSelectorTo: 'по:',
		rangeSelectorZoom: 'Время',
		thousandsSep: ' ',
		weekdays: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота']
	}
});

$(document).ready(function() {
	<!-- IF MODE eq 'peers' -->
	var trend_leechers = [{TREND_LEECHERS}];
	var trend_peers = [{TREND_PEERS}];
	var trend_seeders = [{TREND_SEEDERS}];
	var trend_users_online = [{TREND_USERS_ONLINE}];
	var trend_visitors = [{TREND_VISITORS}];
	
	var chart1 = new Highcharts.StockChart({
		chart: {
			backgroundColor: 'transparent',
			renderTo: 'chart1',
		},
		title: {
			text: 'Количество пиров'
		},
		xAxis: {
			type: 'datetime',
			maxZoom: 1 * 24 * 3600000
		},
		yAxis: {
			gridLineDashStyle: 'ShortDash',
			title: {
				text: ''
			},
			min: 0
		},
		rangeSelector: {
			buttons: [{
				type: 'day',
				count: 1,
				text: '1д'
			}, {
				type: 'week',
				count: 1,
				text: '7д'
			}, {
				type: 'month',
				count: 1,
				text: '1м'
			}, {
				type: 'month',
				count: 6,
				text: '6м'
			}, {
				type: 'year',
				count: 1,
				text: '1г'
			}, {
				type: 'all',
				text: 'все'
			}],
			selected: 0
		},
		tooltip: {
			formatter: function(){
				var point = this.points[0], 
				    series = point.series, 
				    format = '%A, %b %e, %Y, %H:%M';
				
				if( series.tooltipHeaderFormat ) {
					format = series.tooltipHeaderFormat;
				}
	            
				return '<b>' + Highcharts.dateFormat(format, this.x) + '</b><br>Пиры: ' + Highcharts.numberFormat(this.points[0].y, 0);
	        }
	    },
		plotOptions: {
			areaspline: {
				dataGrouping: {
					groupPixelWidth: 5,
				}
			},
		},
		series: [{
			name: 'Пиры',
			data: trend_peers,
			type: 'areaspline'
		}]
	});

	// $.jqplot('chart1', [trend_peers], {
	// 	title: 'Количество пиров',
	// 	legend: { show: true, location: 'nw', xoffset: 4, yoffset: 4 },
	// 	axes: {
	// 		xaxis: { pad: 0, renderer: $.jqplot.DateAxisRenderer, tickOptions: { formatString: '{DATE_FORMAT}' } },
	// 		yaxis: { autoscale: true, min: 0, max: {MAX_TREND_PEERS}, tickOptions: { formatString: '%d' } }
	// 	},
	// 	cursor: { intersectionThreshold: {THRESHOLD}, showCursorLegend: true, showVerticalLine: true, showTooltip: false, cursorLegendFormatString: '%1$s: %3$d (%2$s)' },
	// 	grid: { borderWidth: 0, gridLineColor: '#ddd', shadow: false, },
	// 	series: [ { label: 'Пиры', fill: true, fillAlpha: .75, shadowAngle: 0, shadowOffset: 1, shadowAlpha: .08, shadowDepth: 3 } ]
	// });

	// $.jqplot('chart2', [trend_seeders, trend_leechers], {
	// 	title: 'Количество сидов/личей',
	// 	legend: { show: true, location: 'nw', xoffset: 4, yoffset: 4 },
	// 	axes: {
	// 		xaxis: { pad: 0, renderer: $.jqplot.DateAxisRenderer, tickOptions: { formatString: '{DATE_FORMAT}' } },
	// 		yaxis: { autoscale: true, min: 0, max: {MAX_TREND_SEEDERS}, tickOptions: { formatString: '%d' } },
	// 		y2axis: { autoscale: true, min: 0, max: {MAX_TREND_LEECHERS}, tickOptions: { formatString: '%d' } }
	// 	},
	// 	cursor: { intersectionThreshold: {THRESHOLD}, showCursorLegend: true, showVerticalLine: true, showTooltip: false, cursorLegendFormatString: '%1$s: %3$d (%2$s)' },
	// 	grid: { borderWidth: 0, gridLineColor: '#ddd', shadow: false, },
	// 	series: [
	// 		{ label: 'Сиды', color: '#006600', fill: true, fillAlpha: .75, shadowAngle: 0, shadowOffset: 1, shadowAlpha: .08, shadowDepth: 3 },
	// 		{ label: 'Личи', color: '#800000', fill: true, fillAlpha: .75, shadowAngle: 0, shadowOffset: 1, shadowAlpha: .08, shadowDepth: 3 }
	// 	]
	// });

	// $.jqplot('chart3', [trend_users_online], {
	// 	title: 'Онлайн на сайте',
	// 	legend: { show: true, location: 'nw', xoffset: 4, yoffset: 4 },
	// 	axes: {
	// 		xaxis: { pad: 0, renderer: $.jqplot.DateAxisRenderer, tickOptions: { formatString: '{DATE_FORMAT}' } },
	// 		yaxis: { autoscale: true, min: 0, max: {MAX_TREND_USERS_ONLINE}, tickOptions: { formatString: '%d' } }
	// 	},
	// 	cursor: { intersectionThreshold: {THRESHOLD}, showCursorLegend: true, showVerticalLine: true, showTooltip: false, cursorLegendFormatString: '%1$s: %3$d (%2$s)' },
	// 	grid: { borderWidth: 0, gridLineColor: '#ddd', shadow: false, },
	// 	series: [ { label: 'Посетителей', color: '#097054', fill: true, fillAlpha: .75, shadowAngle: 0, shadowOffset: 1, shadowAlpha: .08, shadowDepth: 3 } ]
	// });

	// $.jqplot('chart4', [trend_visitors], {
	// 	title: 'Уникальных посетителей за сутки',
	// 	legend: { show: true, location: 'nw', xoffset: 4, yoffset: 4 },
	// 	axes: {
	// 		xaxis: { pad: 0, renderer: $.jqplot.DateAxisRenderer, tickOptions: { formatString: '{DATE_FORMAT}' } },
	// 		yaxis: { autoscale: true, min: 0, max: {MAX_TREND_VISITORS}, tickOptions: { formatString: '%d' } }
	// 	},
	// 	cursor: { intersectionThreshold: {THRESHOLD}, showCursorLegend: true, showVerticalLine: true, showTooltip: false, cursorLegendFormatString: '%1$s: %3$d (%2$s)' },
	// 	grid: { borderWidth: 0, gridLineColor: '#ddd', shadow: false, },
	// 	series: [ { label: 'Посетителей', color: '#00628B', fill: true, fillAlpha: .75, shadowAngle: 0, shadowOffset: 1, shadowAlpha: .08, shadowDepth: 3 } ]
	// });
	<!-- ENDIF -->

	<!-- IF MODE eq 'users' -->
	var trend_users = [{TREND_USERS}];

	$.jqplot('chart1', [trend_users], {
		title: 'Приток пользователей по дням',
		legend: { show: false, location: 'nw', xoffset: 4, yoffset: 4 },
		axes: {
			xaxis: { pad: 1.035, renderer: $.jqplot.DateAxisRenderer, tickOptions: { formatString: '{DATE_FORMAT}' } },
			yaxis: { autoscale: true, min: 0, tickOptions: { formatString: '%d' } }
		},
		cursor: { show: false, showTooltip: true },
		grid: { borderWidth: 0, gridLineColor: '#ddd', shadow: false, },
		series: [ { label: 'Пользователи', color: 'silver', shadowAngle: 0, shadowOffset: 1, shadowAlpha: .08, shadowDepth: 3, markerOptions: { shadowDepth: 0 } } ]
	});
	<!-- ENDIF -->

	<!-- IF MODE eq 'torrents' -->
	var trend_torrents = [{TREND_TORRENTS}];

	$.jqplot('chart1', [trend_torrents], {
		title: 'Приток торрентов по дням',
		legend: { show: false, location: 'nw', xoffset: 4, yoffset: 4 },
		axes: {
			xaxis: { pad: 1.035, renderer: $.jqplot.DateAxisRenderer, tickOptions: { formatString: '{DATE_FORMAT}' } },
			yaxis: { autoscale: true, min: 0, tickOptions: { formatString: '%d' } }
		},
		cursor: { show: false, showTooltip: true },
		grid: { borderWidth: 0, gridLineColor: '#ddd', shadow: false, },
		series: [ { label: 'Торренты', color: 'silver', shadowAngle: 0, shadowOffset: 1, shadowAlpha: .08, shadowDepth: 3, markerOptions: { shadowDepth: 0 } } ]
	});
	<!-- ENDIF -->

	<!-- IF MODE eq 'posts' -->
	var trend_posts = [{TREND_POSTS}];

	$.jqplot('chart1', [trend_posts], {
		title: 'Приток сообщений по дням',
		legend: { show: false, location: 'nw', xoffset: 4, yoffset: 4 },
		axes: {
			xaxis: { pad: 1.035, renderer: $.jqplot.DateAxisRenderer, tickOptions: { formatString: '{DATE_FORMAT}' } },
			yaxis: { autoscale: true, min: 0, tickOptions: { formatString: '%d' } }
		},
		cursor: { show: false, showTooltip: true },
		grid: { borderWidth: 0, gridLineColor: '#ddd', shadow: false, },
		series: [ { label: 'Сообщения', color: 'silver', shadowAngle: 0, shadowOffset: 1, shadowAlpha: .08, shadowDepth: 3, markerOptions: { shadowDepth: 0 } } ]
	});
	<!-- ENDIF -->

	<!-- IF MODE eq 'speed' -->
	var trend_speed = [{TREND_SPEED}];

	$.jqplot('chart1', [trend_speed], {
		title: 'Скорость обмена (в мегабайтах в секунду)',
		legend: { show: true, location: 'nw', xoffset: 4, yoffset: 4 },
		axes: {
			xaxis: { pad: 0, renderer: $.jqplot.DateAxisRenderer, tickOptions: { formatString: '{DATE_FORMAT}' } },
			yaxis: { autoscale: true, min: 0, max: {MAX_TREND_SPEED}, tickOptions: { formatString: '%.1f' } }
		},
		cursor: { intersectionThreshold: {THRESHOLD}, showCursorLegend: true, showVerticalLine: true, showTooltip: false, cursorLegendFormatString: '%1$s: <b>%3$d МБ/с</b> (%2$s)' },
		grid: { borderWidth: 0, gridLineColor: '#ddd', shadow: false, },
		series: [
			{ label: 'Скорость', color: '#006600', fill: true, fillAlpha: .75, shadowAngle: 0, shadowOffset: 1, shadowAlpha: .08, shadowDepth: 3 }
		]
	});
	<!-- ENDIF -->

	<!-- IF MODE eq 'traffic' -->
	var trend_traffic = [{TREND_TRAFFIC}];

	$.jqplot('chart1', [trend_traffic], {
		title: 'Трафик (в терабайтах)',
		legend: { show: false, location: 'nw', xoffset: 4, yoffset: 4 },
		axes: {
			xaxis: { pad: 0, renderer: $.jqplot.DateAxisRenderer, tickOptions: { formatString: '{DATE_FORMAT}' } },
			yaxis: { autoscale: true, min: 0, tickOptions: { formatString: '%.1f' } }
		},
		cursor: { intersectionThreshold: {THRESHOLD}, showCursorLegend: true, showVerticalLine: true, showTooltip: false, cursorLegendFormatString: '%1$s: <b>%3$.1f ТБ</b> (%2$s)' },
		grid: { borderWidth: 0, gridLineColor: '#ddd', shadow: false, },
		series: [
			{ label: 'Трафик', color: '#006600', fill: true, fillAlpha: .75, shadowAngle: 0, shadowOffset: 1, shadowAlpha: .08, shadowDepth: 3 }
		]
	});
	<!-- ENDIF -->
});
</script>