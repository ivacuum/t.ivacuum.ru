<h2>{L_WHOSONLINE}</h2>
<p>{TOTAL_REGISTERED_USERS_ONLINE}</p>
<p>{TOTAL_GUEST_USERS_ONLINE}</p>
<p>Данные основаны на активности посетителей за последние 15 минут.</p>

<br />

<table class="stats shadow-extralight">
	<thead>
	<tr>
		<th>{L_USERNAME}</th>
		<th>{L_LAST_UPDATE}</th>
	</tr>
	</thead>
	<!-- BEGIN reg_user_row -->
	<tr>
		<td><a href="{reg_user_row.U_USER_PROFILE}">{reg_user_row.USERNAME}</a></td>
		<td class="tCenter">{reg_user_row.LASTUPDATE}</td>
	</tr>
	<!-- END reg_user_row -->
	<!-- BEGIN guest_user_row -->
	<tr>
		<td>{guest_user_row.USERNAME}</td>
		<td class="tCenter">{guest_user_row.LASTUPDATE}</td>
	</tr>
	<!-- END guest_user_row -->
</table>