<h1><a href="top_speed.php">Топ скоростей</a></h1>
<p>Информация обновляется каждую минуту.</p>

<table class="stats shadow-extralight">
	<!--<col width="5%"><col width="55%"><col align="left" width="20%"><col align="left" width="20%">-->
	<thead>
	<tr>
		<th class="number">#</th>
		<th>{L_USERNAME}</th>
		<th class="number">
			<!-- IF SORT_BY eq 'dl' -->
			<a href="{U_SORT_BY_UPLOAD}">Отдача</a>
			<!-- ELSE -->
			Отдача <img src="{STATIC_PATH}/i/site/arrow_270.png" alt="" style="vertical-align: text-top;">
			<!-- ENDIF -->
		</th>
		<th class="number">
			<!-- IF SORT_BY eq 'up' -->
			<a href="{U_SORT_BY_DOWNLOAD}">Скачивание</a>
			<!-- ELSE -->
			Скачивание <img src="{STATIC_PATH}/i/site/arrow_270.png" alt="" style="vertical-align: text-top;">
			<!-- ENDIF -->
		</th>
	</tr>
	</thead>
	<tbody>
	<!-- BEGIN memberrow -->
	<tr>
		<td class="number">{memberrow.ROW_NUMBER}</td>
		<td><a href="{memberrow.U_VIEWPROFILE}" class="gen"><b>{memberrow.USERNAME}</b></a></td>
		<td class="number"><span class="seedmed"><b>{memberrow.UPLOAD}</b></span></td>
		<td class="number"><span class="leechmed"><b>{memberrow.DOWNLOAD}</b></span></td>
	</tr>
	<!-- END memberrow -->
	</tbody>
</table>