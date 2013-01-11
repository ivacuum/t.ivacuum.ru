<style type="text/css">
.jqplot-target { margin: 0 auto; position: relative; color: #666666; font-family: "Trebuchet MS", Arial, Helvetica, sans-serif; font-size: 1em; height: 300px; width: 95%;
}

.jqplot-axis { font-size: 0.85em; }
.jqplot-xaxis { margin-top: 10px; }
.jqplot-x2axis { margin-bottom: 10px; }
.jqplot-yaxis { margin-right: 10px; }

.jqplot-y2axis, .jqplot-y3axis, .jqplot-y4axis, .jqplot-y5axis, .jqplot-y6axis, .jqplot-y7axis, .jqplot-y8axis, .jqplot-y9axis {
    margin-left: 10px;
    margin-right: 10px;
}

.jqplot-axis-tick, .jqplot-xaxis-tick, .jqplot-yaxis-tick, .jqplot-x2axis-tick, .jqplot-y2axis-tick, .jqplot-y3axis-tick, .jqplot-y4axis-tick, .jqplot-y5axis-tick, .jqplot-y6axis-tick, .jqplot-y7axis-tick, .jqplot-y8axis-tick, .jqplot-y9axis-tick {
    position: absolute;
}

.jqplot-xaxis-tick { top: 0px; left: 15px; vertical-align: top; }
.jqplot-x2axis-tick { bottom: 0px; left: 15px; vertical-align: bottom; }
.jqplot-yaxis-tick { right: 0px; top: 15px; text-align: right; }

.jqplot-y2axis-tick, .jqplot-y3axis-tick, .jqplot-y4axis-tick, .jqplot-y5axis-tick, .jqplot-y6axis-tick, .jqplot-y7axis-tick, .jqplot-y8axis-tick, .jqplot-y9axis-tick {
    left: 0px;
    top: 15px;
    text-align: left;
}

.jqplot-xaxis-label { margin-top: 10px; font-size: 11pt; position: absolute; }
.jqplot-x2axis-label { margin-bottom: 10px; font-size: 11pt; position: absolute; }
.jqplot-yaxis-label { margin-right: 10px; font-size: 11pt; position: absolute; }

.jqplot-y2axis-label, .jqplot-y3axis-label, .jqplot-y4axis-label, .jqplot-y5axis-label, .jqplot-y6axis-label, .jqplot-y7axis-label, .jqplot-y8axis-label, .jqplot-y9axis-label {
    font-size: 11pt;
    position: absolute;
}

table.jqplot-table-legend, table.jqplot-cursor-legend { background-color: rgba(255,255,255,0.6); border: 1px solid #cccccc; position: absolute; font-size: 1em; }
td.jqplot-table-legend { vertical-align: middle; }
td.jqplot-table-legend > div { border: 1px solid #cccccc; padding: 0.2em; }

div.jqplot-table-legend-swatch { width: 0px; height: 0px; border-top-width: 0.35em; border-bottom-width: 0.35em; border-left-width: 0.6em; border-right-width: 0.6em; border-top-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-style: solid; }

.jqplot-title { top: 0px; left: 0px; padding-bottom: 0.5em; font-size: 1.2em; }

table.jqplot-cursor-tooltip { border: 1px solid #cccccc; font-size: 0.75em; }
.jqplot-cursor-tooltip { border: 1px solid #cccccc; font-size: 1.00em; white-space: nowrap; background: rgba(208,208,208,0.5); padding: 1px; }
.jqplot-highlighter-tooltip { border: 1px solid #cccccc; font-size: 0.75em; white-space: nowrap; background: rgba(208,208,208,0.5); padding: 1px; }
.jqplot-point-label { font-size: 0.75em; }

td.jqplot-cursor-legend-swatch { vertical-align: middle; text-align: center; }
div.jqplot-cursor-legend-swatch { width: 1.2em; height: 0.7em; }

#chart1, #chart2, #chart3, #chart4, #chart5 { margin-bottom: 2em; }
</style>
<script type="text/javascript" src="{STATIC_PATH}/js/jqplot/0.9.7/excanvas.min.js"></script>
<script type="text/javascript" src="{STATIC_PATH}/js/jqplot/0.9.7/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="{STATIC_PATH}/js/jqplot/0.9.7/plugins/jqplot.cursor.min.js"></script>
<script type="text/javascript" src="{STATIC_PATH}/js/jqplot/0.9.7/plugins/jqplot.dateAxisRenderer.min.js"></script>
<!-- IF MODE eq 'users' or MODE eq 'torrents' or MODE eq 'posts' -->
<script type="text/javascript" src="{STATIC_PATH}/js/jqplot/0.9.7/plugins/jqplot.pointLabels.min.js"></script>
<!-- ENDIF -->
<div style="margin: 0 1em 0 0;">

<div class="tCenter" style="margin-bottom: 0.6em;">
	<a href="stats.php?mode=peers" class="btn btn-left <!-- IF MODE eq 'peers' --> btn-active<!-- ENDIF -->">Пиры</a><a href="stats.php?mode=users" class="btn btn-middle <!-- IF MODE eq 'users' --> btn-active<!-- ENDIF -->">Регистрации</a><a href="stats.php?mode=torrents" class="btn btn-middle <!-- IF MODE eq 'torrents' --> btn-active<!-- ENDIF -->">Торренты</a><a href="stats.php?mode=posts" class="btn btn-middle <!-- IF MODE eq 'posts' --> btn-active<!-- ENDIF -->">Сообщения</a><a href="stats.php?mode=speed" class="btn btn-middle <!-- IF MODE eq 'speed' --> btn-active<!-- ENDIF -->">Скорость обмена</a><a href="stats.php?mode=traffic&time=week" class="btn btn-right <!-- IF MODE eq 'traffic' --> btn-active<!-- ENDIF -->">Трафик</a>
</div>

<!-- IF MODE eq 'peers' -->
<div class="tCenter" style="margin-bottom: 0.6em;">
	<a href="stats.php?mode=peers&time=day" class="btn btn-left<!-- IF TIME eq 'day' --> btn-active<!-- ENDIF -->">За день</a><a href="stats.php?mode=peers&time=week" class="btn btn-middle<!-- IF TIME eq 'week' --> btn-active<!-- ENDIF -->">За неделю</a><a href="stats.php?mode=peers&time=month" class="btn btn-right<!-- IF TIME eq 'month' --> btn-active<!-- ENDIF -->">За месяц</a>
</div>
<!-- ENDIF -->

<!-- IF MODE eq 'speed' -->
<div class="tCenter" style="margin-bottom: 0.6em;">
	<a href="stats.php?mode=speed&time=day" class="btn btn-left<!-- IF TIME eq 'day' --> btn-active<!-- ENDIF -->">За день</a><a href="stats.php?mode=speed&time=week" class="btn btn-middle<!-- IF TIME eq 'week' --> btn-active<!-- ENDIF -->">За неделю</a><a href="stats.php?mode=speed&time=month" class="btn btn-right<!-- IF TIME eq 'month' --> btn-active<!-- ENDIF -->">За месяц</a>
</div>
<!-- ENDIF -->

<!-- IF MODE eq 'traffic' -->
<div class="tCenter" style="margin-bottom: 0.6em;">
	<a href="stats.php?mode=traffic&time=week" class="btn btn-left<!-- IF TIME eq 'week' --> btn-active<!-- ENDIF -->">За неделю</a><a href="stats.php?mode=traffic&time=month" class="btn btn-right<!-- IF TIME eq 'month' --> btn-active<!-- ENDIF -->">За месяц</a>
</div>
<!-- ENDIF -->

<div id="chart1"></div>
<div id="chart2"></div>
<div id="chart3"></div>
<div id="chart4"></div>
<div id="chart5"></div>

</div>

<script type="text/javascript">
$(document).ready(function() {
	<!-- IF MODE eq 'peers' -->
	var trend_leechers = [{TREND_LEECHERS}];
	var trend_peers = [{TREND_PEERS}];
	var trend_seeders = [{TREND_SEEDERS}];
	var trend_users_online = [{TREND_USERS_ONLINE}];
	var trend_visitors = [{TREND_VISITORS}];

	$.jqplot('chart1', [trend_peers], {
		title: 'Количество пиров',
		legend: { show: true, location: 'nw', xoffset: 4, yoffset: 4 },
		axes: {
			xaxis: { pad: 0, renderer: $.jqplot.DateAxisRenderer, tickOptions: { formatString: '{DATE_FORMAT}' } },
			yaxis: { autoscale: true, min: 0, max: {MAX_TREND_PEERS}, tickOptions: { formatString: '%d' } }
		},
		cursor: { intersectionThreshold: {THRESHOLD}, showCursorLegend: true, showVerticalLine: true, showTooltip: false, cursorLegendFormatString: '%1$s: %3$d (%2$s)' },
		grid: { borderWidth: 0, gridLineColor: '#ddd', shadow: false, },
		series: [ { label: 'Пиры', fill: true, fillAlpha: .75, shadowAngle: 0, shadowOffset: 1, shadowAlpha: .08, shadowDepth: 3 } ]
	});

	$.jqplot('chart2', [trend_seeders, trend_leechers], {
		title: 'Количество сидов/личей',
		legend: { show: true, location: 'nw', xoffset: 4, yoffset: 4 },
		axes: {
			xaxis: { pad: 0, renderer: $.jqplot.DateAxisRenderer, tickOptions: { formatString: '{DATE_FORMAT}' } },
			yaxis: { autoscale: true, min: 0, max: {MAX_TREND_SEEDERS}, tickOptions: { formatString: '%d' } },
			y2axis: { autoscale: true, min: 0, max: {MAX_TREND_LEECHERS}, tickOptions: { formatString: '%d' } }
		},
		cursor: { intersectionThreshold: {THRESHOLD}, showCursorLegend: true, showVerticalLine: true, showTooltip: false, cursorLegendFormatString: '%1$s: %3$d (%2$s)' },
		grid: { borderWidth: 0, gridLineColor: '#ddd', shadow: false, },
		series: [
			{ label: 'Сиды', color: '#006600', fill: true, fillAlpha: .75, shadowAngle: 0, shadowOffset: 1, shadowAlpha: .08, shadowDepth: 3 },
			{ label: 'Личи', color: '#800000', fill: true, fillAlpha: .75, shadowAngle: 0, shadowOffset: 1, shadowAlpha: .08, shadowDepth: 3 }
		]
	});

	$.jqplot('chart3', [trend_users_online], {
		title: 'Онлайн на сайте',
		legend: { show: true, location: 'nw', xoffset: 4, yoffset: 4 },
		axes: {
			xaxis: { pad: 0, renderer: $.jqplot.DateAxisRenderer, tickOptions: { formatString: '{DATE_FORMAT}' } },
			yaxis: { autoscale: true, min: 0, max: {MAX_TREND_USERS_ONLINE}, tickOptions: { formatString: '%d' } }
		},
		cursor: { intersectionThreshold: {THRESHOLD}, showCursorLegend: true, showVerticalLine: true, showTooltip: false, cursorLegendFormatString: '%1$s: %3$d (%2$s)' },
		grid: { borderWidth: 0, gridLineColor: '#ddd', shadow: false, },
		series: [ { label: 'Посетителей', color: '#097054', fill: true, fillAlpha: .75, shadowAngle: 0, shadowOffset: 1, shadowAlpha: .08, shadowDepth: 3 } ]
	});

	$.jqplot('chart4', [trend_visitors], {
		title: 'Уникальных посетителей за сутки',
		legend: { show: true, location: 'nw', xoffset: 4, yoffset: 4 },
		axes: {
			xaxis: { pad: 0, renderer: $.jqplot.DateAxisRenderer, tickOptions: { formatString: '{DATE_FORMAT}' } },
			yaxis: { autoscale: true, min: 0, max: {MAX_TREND_VISITORS}, tickOptions: { formatString: '%d' } }
		},
		cursor: { intersectionThreshold: {THRESHOLD}, showCursorLegend: true, showVerticalLine: true, showTooltip: false, cursorLegendFormatString: '%1$s: %3$d (%2$s)' },
		grid: { borderWidth: 0, gridLineColor: '#ddd', shadow: false, },
		series: [ { label: 'Посетителей', color: '#00628B', fill: true, fillAlpha: .75, shadowAngle: 0, shadowOffset: 1, shadowAlpha: .08, shadowDepth: 3 } ]
	});
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