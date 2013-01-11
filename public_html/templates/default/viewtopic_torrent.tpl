
<table class="forumline dl_list">

<col class="row1">
<col class="row1">

<tr id="dl_list_header">
	<td colspan="2" class="catTitle">{L_DL_LIST_AND_TORRENT_ACTIVITY}</td>
</tr>

<!-- IF SHOW_DL_LIST_TOR_INFO -->
<tr>
	<td colspan="2" class="borderless bCenter pad_8">{L_SIZE}: &nbsp;<b>{TOR_SIZE}</b> &nbsp; | &nbsp; {L_IS_REGISTERED}: &nbsp;<b>{TOR_LONGEVITY}</b> &nbsp; | &nbsp; <a href="#" id="get_torrent_dl_list" title="Посмотреть список скачавших">{L_COMPLETED}</a>: &nbsp;<b>{TOR_COMPLETED}</b> <span class="genmed" title="Скачан последний раз">({TOR_LAST_DL_TIME})</span></td>
</tr>
<!-- ENDIF / SHOW_DL_LIST_TOR_INFO -->

<!-- IF SHOW_TOR_ACT -->

	<!-- IF S_MODE_COUNT -->

		<tr>
			<td colspan="2" class="<!-- IF SHOW_DL_LIST -->row2<!-- ELSE -->row1<!-- ENDIF --> pad_2 bCenter">
				<div id="full_details" class="bCenter">

				<!-- IF not SEED_COUNT -->
				<p class="mrg_10">{SEEDER_LAST_SEEN}</p>
				<!-- ENDIF -->

				<!-- IF SEED_COUNT || LEECH_COUNT -->
				<div class="mrg_4 pad_4">
				<!-- IF SEED_COUNT -->
				<span class="seed"><b>Сиды:</b> &nbsp;[ &nbsp;<b>{SEED_COUNT}</b> &nbsp;<!-- IF TOR_SPEED_UP gt 0 -->| <img src="{STATIC_PATH}/i/tracker/icon_up.gif" alt="" style="display: inline; vertical-align: text-top;">&nbsp;{TOR_SPEED_UP} &nbsp;<!-- ENDIF -->]</span> &nbsp;
				<!-- ENDIF -->
				<!-- IF LEECH_COUNT -->
				<span class="leech"><b>Личи:</b> &nbsp;[ &nbsp;<b>{LEECH_COUNT}</b> &nbsp;<!-- IF TOR_SPEED_DOWN gt 0 -->| <img src="{STATIC_PATH}/i/tracker/icon_down.gif" alt="" style="display: inline; vertical-align: text-top;">&nbsp;{TOR_SPEED_DOWN} &nbsp;<!-- ENDIF -->]</span> &nbsp;
				<!-- ENDIF -->
				<!-- ENDIF / SEED_COUNT || LEECH_COUNT -->

				<!-- IF PEERS_FULL_LINK && PEER_EXIST -->
				<a href="{SPMODE_FULL_HREF}" class="gen" id="get_peers_details">{L_SPMODE_FULL}</a>
				<!-- ENDIF -->
				</div>

				</div>
			</td>
		</tr>
	<!-- ENDIF / S_MODE_COUNT -->
<!-- ENDIF / SHOW_TOR_ACT -->

</table>

<div class="spacer_6"></div>

<div class="tCenter">
	<button class="btn-left btn-positive btn-icons" id="dl_set_will"><img src="{STATIC_PATH}/i/_/plus.png" alt="" />Добавить в &laquo;Будущие закачки&raquo;</button><a class="btn btn-middle btn-orange btn-primary btn-icons" href="/download.php?id={TOR_ATTACH_ID}"><img src="{STATIC_PATH}/i/_/drive_download.png" alt="" />Скачать торрент</a><button class="btn-middle btn-icons" id="thumb_up"><img src="{STATIC_PATH}/i/_/thumb_up.png" alt="" />Сказать &laquo;Спасибо&raquo;</button><button class="btn-right btn-negative btn-icons" id="dl_set_delete"><img src="{STATIC_PATH}/i/_/cross_script.png" alt="" />Удалить из списка закачек</button>
	<div id="dl_set_info" style="display: none; margin: 0.6em 0 0.3em;"></div>
</div>

<div class="spacer_6"></div>

<script type="text/javascript">
$(document).ready(function() {
  /* Удалить из списка закачек */
  $('#dl_set_delete').bind('click', function() {
    if( confirm('Удалить раздачу из списка ваших закачек?') ) {
      ajax.exec({ action: 'set_dl_status', dl_status: 'delete', topic_id: {TOPIC_ID} });
    }

    return false;
  });

  /* Добавить в будущие закачки */
  $('#dl_set_will').bind('click', function() {
    if( confirm('Добавить раздачу в список ваших «Будущих закачек»?') ) {
      ajax.exec({ action: 'set_dl_status', dl_status: 'will', topic_id: {TOPIC_ID} });
    }

    return false;
  });

  /* Подробная статистика пиров */
  $('#get_peers_details').bind('click', function() {
    ajax.exec({ action: 'get_peers_details', topic_id: {TOPIC_ID} });
    return false;
  });

  /* Список скачавших */
  $('#get_torrent_dl_list').bind('click', function() {
    ajax.exec({ action: 'get_torrent_dl_list', topic_id: {TOPIC_ID} });
    return false;
  });

  /* Спасибо */
  $('#thumb_up').bind('click', function() {
    ajax.exec({ action: 'thumb_up', attach_id: {TOR_ATTACH_ID}, mode: '' });
    return false;
  });
});

ajax.callback.get_peers_details = function() {
  $('#full_details').parent().removeClass();
};

ajax.callback.get_torrent_dl_list = function(response) {
  $('#dl_list_header').after(response.html);
  $('#get_torrent_dl_list').each(function() {
    $(this).replaceWith($(this).text());
  });
};

ajax.callback.set_dl_status = function(response) {
  $('#dl_set_info').fadeToggle().html(response.html).delay(1500).fadeToggle();
};

ajax.callback.thumb_up = function() {
  $('#dl_set_info').fadeToggle().html('<img src="{STATIC_PATH}/i/_/tick.png" alt="" style="vertical-align: text-top;"> Ваш голос учтен').delay(1500).fadeToggle();
};
</script>