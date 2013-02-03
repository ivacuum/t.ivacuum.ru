<!-- IF DEVELOPER -->
<!--
<div class="nav_links">
	<span class="item">Профиль</span>
	<span class="item">Торренты</span>
	<span class="item">Настройки</span>
	<span class="item">Графики</span>
</div>
-->
<!-- ENDIF -->

<!-- IF EDITABLE_TPLS -->
<div id="editable-tpl-can-leech" style="display: none;">
	<span class="editable-inputs nowrap" style="display: none;">
		<label><input class="editable-value" type="radio" name="editable-value" value="2" />Не сразу</label>
		<label><input class="editable-value" type="radio" name="editable-value" value="1" />{L_YES}</label>
		<label><input class="editable-value" type="radio" name="editable-value" value="0" />{L_NO}</label>&nbsp;
		<input type="button" class="editable-submit" value="&raquo;" style="width: 30px; font-weight: bold;" />
		<input type="button" class="editable-cancel" value="x" style="width: 30px;" />
	</span>
</div>
<!-- ENDIF / EDITABLE_TPLS -->

<!-- IF SHOW_ADMIN_OPTIONS -->
<script type="text/javascript">
ajax.init.edit_user_profile = function(params) {
	if( params.submit ) {
		ajax.exec({
			action  : params.action,
			edit_id : params.id,
			user_id : params.user_id || {PROFILE_USER_ID},
			field   : params.field || params.id,
			value   : params.value
		});
	} else {
		editableType = params.editableType || "input";
		ajax.makeEditable(params.id, editableType);
	}
};
ajax.callback.edit_user_profile = function(data) {
	ajax.restoreEditable(data.edit_id, data.new_value);
};
</script>

<var class="ajax-params">{action: "edit_user_profile", id: "user_regdate"}</var>
<var class="ajax-params">{action: "edit_user_profile", id: "user_lastvisit"}</var>
<!-- IF IGNORE_SRV_LOAD_EDIT -->
<var class="ajax-params">{action: "edit_user_profile", id: "ignore_srv_load", editableType: "yesno-radio"}</var>
<!-- ENDIF -->
<!-- IF CAN_EDIT_RATIO -->
<var class="ajax-params">{action: "edit_user_profile", id: "u_up_total"}</var>
<var class="ajax-params">{action: "edit_user_profile", id: "u_down_total"}</var>
<var class="ajax-params">{action: "edit_user_profile", id: "u_up_release"}</var>
<var class="ajax-params">{action: "edit_user_profile", id: "u_up_bonus"}</var>
<var class="ajax-params">{action: "edit_user_profile", id: "can_leech", editableType: "can-leech"}</var>
<!-- ENDIF -->

<!-- ENDIF / SHOW_ADMIN_OPTIONS -->

<style type="text/css">
table.ratio { background: #F9F9F9; border: 1px solid #A5AFB4; border-collapse: separate; }
table.ratio th, table.ratio td { padding: 3px 12px; text-align: center; white-space: nowrap; font-size: 11px; }
table.ratio th { color: #000000; }
table.ratio td { padding: 2px 4px; }
#user-contacts th { text-align: right; white-space: nowrap; }
#user-contacts td { text-align: left; padding: 2px 6px; white-space: nowrap; }
</style>

<a name="editprofile"></a>

<h1 class="pagetitle">{L_VIEWING_PROFILE}</h1>

<p class="nav"><a href="{U_INDEX}">{T_INDEX}</a></p>

<table class="user_profile bordered w100" cellpadding="0" border=1>
<tr>
	<th colspan="2" class="thHead">{L_VIEWING_PROFILE}</th>
</tr>
<tr>
	<td class="row1 vTop tCenter" width="30%">

		<p class="mrg_4">{AVATAR_IMG}</p>
		<!-- IF POSTER_RANK -->
		<p class="small mrg_4">{POSTER_RANK}</p>
		<!-- ENDIF -->
		<h4 class="cat border bw_TB">{L_CONTACT} {USERNAME}</h4>

		<table class="borderless wAuto bCenter user_contacts">
		<!-- IF EMAIL_IMG -->
		<tr>
			<th>{L_EMAIL_ADDRESS}:</th>
			<td>{EMAIL_IMG}</td>
		</tr>
		<!-- ENDIF -->
		<!-- IF PM_IMG -->
		<tr>
			<th>{L_PRIVATE_MESSAGE}:</th>
			<td>{PM_IMG}</td>
		</tr>
		<!-- ENDIF -->
		<!-- IF MSN -->
		<tr>
			<th>{L_MSNM}:</th>
			<td>{MSN}</td>
		</tr>
		<!-- ENDIF -->
		<!-- IF YIM_IMG -->
		<tr>
			<th>{L_YIM}:</th>
			<td>{YIM_IMG}</td>
		</tr>
		<!-- ENDIF -->
		<!-- IF AIM_IMG -->
		<tr>
			<th>{L_AIM}:</th>
			<td>{AIM_IMG}</td>
		</tr>
		<!-- ENDIF -->
		<!-- IF ICQ_IMG -->
		<tr>
			<th>{L_ICQ}:</th>
			<td>{ICQ_IMG}</td>
		</tr>
		<!-- ENDIF -->
		</table><!--/user_contacts-->

		<!-- IF SHOW_GROUPS -->
		<h4 class="cat border bw_TB">Членство в группах</h4>
		<table cellpadding="2" cellspacing="0" border="0" class="borderless user_contacts w100" style="padding: 10px 10px 10px 10px;">
		<!-- BEGIN groups -->
			<tr>
				<td class="row2" nowrap="nowrap" align="left" class="catLeft"><span class="gentbl"><a href="{groups.URL}" class="gen"><b>{groups.TITLE}</b></a></span></td>
			</tr>
		<!-- END groups -->
		</table>
		<!-- ENDIF -->
	</td>
	<td class="row1" valign="top" width="70%">

		<div class="spacer_4"></div>

		<!-- IF not USER_ACTIVE -->
		<h4 class="mrg_4 tCenter warnColor1">Account disabled</h4>
		<!-- ENDIF -->

		<table class="user_details borderless w100">
			<tr>
				<th>{L_JOINED}:</th>
				<td id="user_regdate">
					<span class="editable bold">{USER_REGDATE}</span>
				</td>
			</tr>
			<tr>
				<th>{L_LAST_VISITED}:</th>
				<td id="user_lastvisit">
					<span class="editable bold">{LAST_VISIT_TIME}</span>
				</td>
			</tr>
			<tr>
				<th class="nowrap">{L_LAST_ACTIVITY}:</th>
				<td><b>{LAST_ACTIVITY_TIME}</b></td>
			</tr>
			<tr>
				<th>{L_TOTAL_POSTS}:</th>
				<td>
					<p><b>{POSTS}</b> [ <a href="{U_SEARCH_USER}" class="med">Найти сообщения</a> ]</p>
				</td>
			</tr>
			<!-- IF LOCATION -->
			<tr>
				<th style="vertical-align: middle;">{L_LOCATION}:</th>
				<td><b>{LOCATION}</b></td>
			</tr>
			<!-- ENDIF -->
			<!-- IF WWW -->
			<tr>
				<th>{L_WEBSITE}:</th>
				<td><b>{WWW}</b></td>
			</tr>
			<!-- ENDIF -->
			<!-- IF OCCUPATION -->
			<tr>
				<th>{L_OCCUPATION}:</th>
				<td><b>{OCCUPATION}</b></td>
			</tr>
			<!-- ENDIF -->
			<!-- IF INTERESTS -->
			<tr>
				<th>{L_INTERESTS}:</th>
				<td><b>{INTERESTS}</b></td>
			</tr>
			<!-- ENDIF -->
			<!-- BEGIN switch_upload_limits -->
			<!--<tr>
				<th>{L_UPLOAD_QUOTA}:</th>
				<td>
					<p class="med">[{UPLOADED} / {QUOTA} / {PERCENT_FULL}]</p>
					<p class="med"><a href="{U_UACP}" class="med">{L_UACP}</a></p>
				</td>
			</tr>-->
			<!-- END switch_upload_limits -->
			<!-- IF SHOW_ACCESS_PRIVILEGE -->
			<!--<tr>
				<th>{L_ACCESS}:</th>
				<td id="ignore_srv_load">{L_ACCESS_SRV_LOAD}: <span class="editable bold">{IGNORE_SRV_LOAD}</span></td>
			</tr>-->
			<!-- ENDIF -->
			<tr>
				<th>Торренты:</th>
				<td>
					<span class="seed"><img src="{STATIC_PATH}/i/tracker/icon_up.gif" alt="" style="vertical-align: text-top;"> {USER_SEEDING}<!-- IF SPEED_UP neq '-' --> ({SPEED_UP})<!-- ENDIF --></span> &nbsp;<span class="leech"><img src="{STATIC_PATH}/i/tracker/icon_down.gif" alt="" style="vertical-align: text-top;"> {USER_LEECHING}<!-- IF SPEED_DOWN neq '-' --> ({SPEED_DOWN})<!-- ENDIF --></span>
				</td>
			</tr>
			<tr>
				<th>Клиент:</th>
				<td><b>{USER_AGENT}</b></td>
			</tr>
			<tr>
				<th>Шара:</th>
				<td><b>{SHARE_SIZE}</b></td>
			</tr>
			<tr>
				<th>Вклад:</th>
				<td><b>{RELEASED_SIZE}</b></td>
			</tr>
			<tr>
				<th style="color: #930;"><b>Рейтинг:</b></th>
				<td id="u_ratio" class="gen">
					<!-- IF DOWN_TOTAL_BYTES gt MIN_DL_BYTES -->
					<b class="gen">{USER_RATIO}</b>&nbsp;
					[ <a class="genmed" href="#" onclick="$('#ratio-expl').toggle(); return false;">Формула расчёта</a> ]
					<!-- ELSE -->
					<span class="med">начнет учитываться после того как будет скачано <b>{MIN_DL_FOR_RATIO}</b></span>
					<!-- ENDIF -->
					<!-- IF SHOW_PASSKEY -->[ <a class="genmed" href="#" onclick="$('#passkey-expl').toggle(); return false;">Passkey</a> ]<!-- ENDIF -->
				</td>
			</tr>
			<tr id="ratio-expl" style="display: none;">
				<td colspan="2" class="med tCenter">
					(
						Всего отдано <b class="seedmed">{UP_TOTAL}</b>
						+ на своих раздачах <b class="seedmed">{RELEASED}</b>
						+ бонусных <b class="seedmed">{UP_BONUS}</b>
					) / {L_DOWNLOADED} <b class="leechmed">{DOWN_TOTAL}</b>
					= <b>{USER_RATIO_RAW}</b>
				</td>
			</tr>
			<!-- IF SHOW_PASSKEY -->
			<tr id="passkey-expl" style="display: none;">
				<th><a href="#" onclick="toggle_block('gen_passkey'); return false;" class="gen">Passkey:</a></th>
				<td>{AUTH_KEY}</td>
			</tr>
			<tr id="gen_passkey" style="display: none;">
				<td colspan="2" class="med tCenter">{S_GEN_PASSKEY}</td>
			</tr>
			<!-- ENDIF / SHOW_PASSKEY -->
			<!-- IF SHOW_ADMIN_OPTIONS -->
			<tr>
				<th>IP:</th>
				<td class="gen"><b>{IP}</b></td>
			</tr>
			<tr>
				<th>Может качать:</th>
				<td id="can_leech"><span class="editable bold">{CAN_LEECH}</span></td>
			</tr>
			<!-- ENDIF -->
			<tr>
				<th></th>
				<td class="pad_4">
					<table align="left" class="ratio borderless" cellspacing="1" width="1%">
						<tr class="row3">
							<td>&nbsp;</td>
							<th>Сегодня</th>
							<th>Вчера</th>
							<th>Всего&nbsp;учтено</th>
						</tr>
						<tr class="row5 leech">
							<th>Скачано</th>
							<td class="bold">{DOWN_TODAY}</td>
							<td>{DOWN_YDAY}</td>
							<td id="u_down_total"><span class="editable bold">{DOWN_TOTAL}</span></td>
						</tr>
						<tr class="row1 seed">
							<th>Отдано</th>
							<td class="bold">{UP_TODAY}</td>
							<td>{UP_YDAY}</td>
							<td id="u_up_total"><span class="editable bold">{UP_TOTAL}</span></td>
						</tr>
						<tr class="row5 seed">
							<th><a href="{U_SEARCH_RELEASES}">На своих</a></th>
							<td class="bold">{RELEASED_TODAY}</td>
							<td>{RELEASED_YDAY}</td>
							<td id="u_up_release"><span class="editable bold">{RELEASED}</span></td>
						</tr>
						<tr class="row1 seed">
							<th>Бонус</th>
							<td class="bold">{UP_BONUS_TODAY}</td>
							<td>{UP_BONUS_YDAY}</td>
							<td id="u_up_bonus"><span class="editable bold">{UP_BONUS}</span></td>
						</tr>
						<!-- IF DEVELOPER -->
						<tr class="row5 seed">
							<th>Таймбонус</th>
							<td class="bold"><!-- IF TIMEBONUS_TODAY gt 1000 -->1000<!-- ELSE -->{TIMEBONUS_TODAY}<!-- ENDIF --></td>
							<td>+{TIMEBONUS_YDAY} / <span class="leechmed">-{TIMEBONUS_SPENT_YDAY}</span></td>
							<td id="timebonus"><span class="editable bold">{TIMEBONUS}</span></td>
						</tr>
						<!-- ENDIF -->
					</table>
				</td>
			</tr>
		</table><!--/user_details-->

	</td>
</tr>
</table><!--/user_profile-->

<a name="torrent"></a>
<div class="spacer_8"></div>

<!-- IF SHOW_ADMIN_OPTIONS -->
<div class="med tRight">
		Администрирование:&nbsp;
		<a href="{U_MANAGE}">Профиль</a> |
		<a href="{U_PERMISSIONS}">Права доступа</a>
</div>
<!-- ENDIF -->

<table class="bordered w100">
	<tr>
		<th colspan="4" class="thHead">Текущие активные торренты</th>
	</tr>
	<tr>
		<td {OWN_ROWSPAN} class="row1 tCenter dlComplete lh_150 pad_4"><b>Свои</b><!-- IF OWNING_COUNT gt 0 --><br />[&nbsp;<b>{OWNING_COUNT}</b>&nbsp;]<!-- ENDIF --></td>
		<!-- BEGIN switch_owning_none -->
			<td colspan="3" class="row1 w100 tCenter pad_8">{L_NONE}</td>
		</tr>
		<!-- END switch_owning_none -->
		<!-- BEGIN own -->
		<td class="row3 tCenter">{L_FORUM}</td>
		<td class="row3 tCenter">{L_TOPICS}</td>
		<td class="row3 tCenter">Раздача</td>
	</tr>
	<!-- BEGIN ownrow -->
	<tr class="row1">
		<td class="tCenter pad_4"><a class="gen" href="{own.ownrow.U_VIEW_FORUM}">{own.ownrow.FORUM_NAME}</a></td>
		<td class="pad_4"><a class="med" href="{own.ownrow.U_VIEW_TOPIC}"><b>{own.ownrow.TOPIC_TITLE}</b></a></td>
		<td class="tCenter med nowrap pad_2"><div><p><span class="seedmed"><b>{own.ownrow.SEEDERS}</b></span><span class="med"> | </span><span class="leechmed"><b>{own.ownrow.LEECHERS}</b></span></p><p class="seedsmall" style="padding-top: 2px;">{own.ownrow.SPEED}</p></div></td>
	</tr>
	<!-- END ownrow -->
	<!-- END own -->
	<tr>
		<td colspan="4" class="row2 pad_0"><div class="spacer_4"></div></td>
	</tr>
	<tr>
		<td {SEED_ROWSPAN} class="row1 tCenter dlComplete lh_150 pad_4"><b>Сидер</b><!-- IF SEEDING_COUNT gt 0 --><br />[&nbsp;<b>{SEEDING_COUNT}</b>&nbsp;]<!-- ENDIF --></td>
		<!-- BEGIN switch_seeding_none -->
			<td colspan="3" class="row1 w100 tCenter pad_8">{L_NONE}</td>
		</tr>
		<!-- END switch_seeding_none -->
		<!-- BEGIN seed -->
		<td class="row3 tCenter">{L_FORUM}</td>
		<td class="row3 tCenter">{L_TOPICS}</td>
		<td class="row3 tCenter">Раздача</td>
	</tr>
	<!-- BEGIN seedrow -->
	<tr class="row1">
		<td class="tCenter pad_4"><a class="gen" href="{seed.seedrow.U_VIEW_FORUM}">{seed.seedrow.FORUM_NAME}</a></td>
		<td class="pad_4"><a class="med" href="{seed.seedrow.U_VIEW_TOPIC}"><b>{seed.seedrow.TOPIC_TITLE}</b></a></td>
		<td class="tCenter med nowrap pad_2"><div><p><span class="seedmed"><b>{seed.seedrow.SEEDERS}</b></span><span class="med"> | </span><span class="leechmed"><b>{seed.seedrow.LEECHERS}</b></span></p><p class="seedsmall" style="padding-top: 2px;">{seed.seedrow.SPEED}</p></div></td>
	</tr>
	<!-- END seedrow -->
	<!-- END seed -->
	<tr>
		<td colspan="4" class="row2 pad_0"><div class="spacer_4"></div></td>
	</tr>
	<tr>
		<td {LEECH_ROWSPAN} class="row1 tCenter dlDown lh_150 pad_4"><b>Личер</b><!-- IF LEECHING_COUNT gt 0 --><br />[&nbsp;<b>{LEECHING_COUNT}</b>&nbsp;]<!-- ENDIF --></td>
		<!-- BEGIN switch_leeching_none -->
		<td colspan="3" class="row1 w100 tCenter pad_8">{L_NONE}</td>
		</tr>
		<!-- END switch_leeching_none -->
		<!-- BEGIN leech -->
		<td class="row3 tCenter">{L_FORUM}</td>
		<td class="row3 tCenter">{L_TOPICS}</td>
		<td class="row3 tCenter">%</td>
	</tr>
	<!-- BEGIN leechrow -->
	<tr class="row1">
		<td class="tCenter pad_4"><a class="gen" href="{leech.leechrow.U_VIEW_FORUM}">{leech.leechrow.FORUM_NAME}</a></td>
		<td class="pad_4"><a class="med" href="{leech.leechrow.U_VIEW_TOPIC}"><b>{leech.leechrow.TOPIC_TITLE}</b></a></td>
		<td class="tCenter med"><b>{leech.leechrow.COMPL_PERC}</b></td>
	</tr>
	<!-- END leechrow -->
	<!-- END leech -->
	<tr class="row2 tCenter">
		<td colspan="4" class="pad_6">
			<!-- IF SHOW_SEARCH_DL -->
				<a href="{U_SEARCH_DL_WILL}">Будущие закачки</a>
				::
				<a href="{U_SEARCH_DL_COMPLETE}">Прошлые закачки</a>
			<!-- ENDIF -->
		</td>
	</tr>
</table>