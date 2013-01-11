
<form method="post" action="{S_MODE_ACTION}">

<table width="100%">
	<tr>
		<td align="center" class="med" nowrap="nowrap">{L_SORT_BY}:&nbsp;{S_MODE_SELECT}&nbsp;&nbsp;{L_ORDER}&nbsp;{S_ORDER_SELECT}&nbsp;&nbsp;<input class="btn btn-small" type="submit" name="submit" value="{L_SUBMIT}" /></td>
	</tr>
</table>

<table class="stats shadow-extralight">
	<thead>
	<tr>
		<th class="number">#</th>
		<th>Имя</th>
		<th>ЛС</th>
		<th>Откуда</th>
		<th>Регистрация</th>
		<th class="number">Сообщ.</th>
		<th class="number"><img src="{STATIC_PATH}/i/t/images/icon_up.gif" alt="" /></th>
		<th class="number"><img src="{STATIC_PATH}/i/t/images/icon_down.gif" alt="" /></th>
		<th class="number">Рейтинг</th>
		<th class="number">Отдано</th>
		<th class="number">Скачано</th>
	</tr>
	</thead>
	<!-- BEGIN memberrow -->
	<tr>
		<td class="number">{memberrow.ROW_NUMBER}</td>
		<td><a href="{memberrow.U_VIEWPROFILE}"><b>{memberrow.USERNAME}</b></a></td>
		<td align="center"><a href="{memberrow.U_PM}"><img src="{STATIC_PATH}/i/_/mail_send.png" alt="" /></a></td>
		<td align="center">{memberrow.FLAG}</td>
		<td align="center">{memberrow.JOINED}</td>
		<td class="number">{memberrow.POSTS}</td>
		<td class="number seed">{memberrow.SEEDING}</td>
		<td class="number leech">{memberrow.LEECHING}</td>
		<td class="number">{memberrow.RATIO}</td>
		<td class="number seed"><b>{memberrow.UPLOAD}</b></td>
		<td class="number leech"><b>{memberrow.DOWNLOAD}</b></td>
	</tr>
	<!-- END memberrow -->
	<tr>
		<td colspan="11">
			<p style="float: left;">{PAGE_NUMBER}</p>
			<p style="float: right;">{PAGINATION}</p>
		</td>
	</tr>
</table>